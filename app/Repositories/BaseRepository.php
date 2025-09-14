<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * Base Repository class providing common database operations
 * 
 * @package App\Repositories
 */
abstract class BaseRepository
{
    protected Model $model;

    /**
     * Constructor - Initialize the model
     */
    public function __construct()
    {
        $this->model = app($this->model());
    }

    /**
     * Get the model class name
     * 
     * @return string
     */
    abstract public function model();

    /**
     * Get all records with pagination and filtering
     * 
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @param array $fields
     * @return array
     */
    public function all(array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*']): array
    {
        try {
            $query = $this->model->query();
            
            // Load relations if specified
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            // Select specific fields if specified
            if (!empty($fields) && $fields !== ['*']) {
                $query->select($fields);
            }
            
            // Apply filters with optimization
            $this->applyOptimizedFilters($query, $filters);
            
            // Apply sorting
            $this->applyOptimizedSorting($query, $filters);
            
            $paginator = $query->paginate($perPage);
            
            return [
                'data' => $paginator->items(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                    'has_more_pages' => $paginator->hasMorePages(),
                ]
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to retrieve records: " . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Apply optimized filters to query
     * 
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyOptimizedFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $key => $value) {
            if ($value === '' || $value === null) continue;
            
            switch ($key) {
                case 'search':
                    $this->applySearchFilter($query, $value);
                    break;
                case 'ids':
                    $this->applyIdsFilter($query, $value);
                    break;
                case 'date_range':
                    $this->applyDateRangeFilter($query, $value);
                    break;
                case 'status':
                case 'is_active':
                    $query->where($key, $value);
                    break;
                case 'like':
                    $this->applyLikeFilter($query, $value);
                    break;
                case 'in':
                    $this->applyInFilter($query, $value);
                    break;
                case 'between':
                    $this->applyBetweenFilter($query, $value);
                    break;
                case 'not_null':
                    $this->applyNotNullFilter($query, $value);
                    break;
                case 'is_null':
                    $this->applyIsNullFilter($query, $value);
                    break;
                default:
                    $this->applyDefaultFilter($query, $key, $value);
            }
        }
    }
    
    /**
     * Apply IDs filter with optimization
     * 
     * @param Builder $query
     * @param mixed $value
     * @return void
     */
    protected function applyIdsFilter(Builder $query, $value): void
    {
        $idsArray = is_array($value) ? $value : explode(',', $value);
        // Limit to prevent too many IDs
        $idsArray = array_slice($idsArray, 0, 500);
        $query->whereIn('id', $idsArray);
    }
    
    /**
     * Apply date range filter
     * 
     * @param Builder $query
     * @param mixed $value
     * @return void
     */
    protected function applyDateRangeFilter(Builder $query, $value): void
    {
        if (is_array($value) && isset($value['start']) && isset($value['end'])) {
            $query->whereBetween('created_at', [$value['start'], $value['end']]);
        }
    }
    
    /**
     * Apply LIKE filter
     * 
     * @param Builder $query
     * @param array $value
     * @return void
     */
    protected function applyLikeFilter(Builder $query, array $value): void
    {
        foreach ($value as $field => $likeValue) {
            if (is_array($likeValue)) {
                $query->where(function($q) use ($field, $likeValue) {
                    foreach ($likeValue as $val) {
                        $q->orWhere($field, 'like', "%$val%");
                    }
                });
            } else {
                $query->where($field, 'like', "%$likeValue%");
            }
        }
    }
    
    /**
     * Apply IN filter
     * 
     * @param Builder $query
     * @param array $value
     * @return void
     */
    protected function applyInFilter(Builder $query, array $value): void
    {
        foreach ($value as $field => $inValues) {
            if (is_array($inValues)) {
                $query->whereIn($field, $inValues);
            }
        }
    }
    
    /**
     * Apply BETWEEN filter
     * 
     * @param Builder $query
     * @param array $value
     * @return void
     */
    protected function applyBetweenFilter(Builder $query, array $value): void
    {
        foreach ($value as $field => $betweenValues) {
            if (is_array($betweenValues) && count($betweenValues) === 2) {
                $query->whereBetween($field, $betweenValues);
            }
        }
    }
    
    /**
     * Apply NOT NULL filter
     * 
     * @param Builder $query
     * @param mixed $value
     * @return void
     */
    protected function applyNotNullFilter(Builder $query, $value): void
    {
        if (is_array($value)) {
            foreach ($value as $field) {
                $query->whereNotNull($field);
            }
        }
    }
    
    /**
     * Apply IS NULL filter
     * 
     * @param Builder $query
     * @param mixed $value
     * @return void
     */
    protected function applyIsNullFilter(Builder $query, $value): void
    {
        if (is_array($value)) {
            foreach ($value as $field) {
                $query->whereNull($field);
            }
        }
    }
    
    /**
     * Apply default filter
     * 
     * @param Builder $query
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function applyDefaultFilter(Builder $query, string $key, $value): void
    {
        if (in_array($key, $this->model->getFillable()) || in_array($key, ['id', 'created_at', 'updated_at'])) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
    }
    
    /**
     * Apply optimized sorting
     * 
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyOptimizedSorting(Builder $query, array $filters): void
    {
        if (isset($filters['sort_by'])) {
            $this->applySorting($query, $filters['sort_by']);
        } else {
            // Default sorting
            $query->orderBy('created_at', 'desc');
        }
    }
    
    /**
     * Apply search filter
     * 
     * @param Builder $query
     * @param string $searchValue
     * @return void
     */
    protected function applySearchFilter(Builder $query, string $searchValue): void
    {
        $query->where(function($q) use ($searchValue) {
            // Search in main model fields
            $searchableFields = $this->getSearchableFields();
            foreach ($searchableFields as $field) {
                if (in_array($field, $this->model->getFillable()) || in_array($field, ['id', 'username', 'email'])) {
                    $q->orWhere($field, 'like', "%$searchValue%");
                }
            }
            // Search in relationships if they exist
            $this->applyRelationshipSearch($q, $searchValue);
        });
    }

    /**
     * Get searchable fields for the model
     * 
     * @return array
     */
    protected function getSearchableFields(): array
    {
        return ['name', 'description', 'title', 'content'];
    }

    /**
     * Apply relationship search
     * 
     * @param Builder $query
     * @param string $searchValue
     * @return void
     */
    protected function applyRelationshipSearch(Builder $query, string $searchValue): void
    {
        // Override in child classes if needed
    }

    /**
     * Apply sorting to query
     * 
     * @param Builder $query
     * @param string $sortBy
     * @return void
     */
    protected function applySorting(Builder $query, string $sortBy): void
    {
        if (empty($sortBy)) {
            $query->orderBy('created_at', 'desc');
            return;
        }
        
        // Parse sorting string: "field:direction" or "field"
        $parts = explode(':', $sortBy);
        $field = $parts[0];
        $direction = isset($parts[1]) ? strtolower($parts[1]) : 'asc';
        
        // Validate direction
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }
        
        // Check if field exists in model's fillable or common fields
        $allowedFields = array_merge(
            $this->model->getFillable(),
            ['id', 'created_at', 'updated_at', 'name', 'title', 'email', 'status']
        );
        
        if (in_array($field, $allowedFields)) {
            $query->orderBy($field, $direction);
        } else {
            // Fallback to default sorting
            $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Find a record by ID
     * 
     * @param mixed $id
     * @param array $relations
     * @param array $fields
     * @return array|null
     */
    public function find($id, array $relations = [], array $fields = ['*']): ?array
    {
        try {
            $query = $this->model->query();
            
            // Load relations if specified
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            // Select specific fields if specified
            if (!empty($fields) && $fields !== ['*']) {
                $query->select($fields);
            }
            
            $model = $query->find($id);
            return $model ? $model->toArray() : null;
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to find record: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Find a record by ID with caching
     * 
     * @param mixed $id
     * @param array $relations
     * @param array $fields
     * @param int $cacheMinutes
     * @return array|null
     */
    public function findWithCache($id, array $relations = [], array $fields = ['*'], int $cacheMinutes = 60): ?array
    {
        $cacheKey = $this->generateCacheKey('find', $id, $relations, $fields);
        
        return Cache::remember($cacheKey, $cacheMinutes, function() use ($id, $relations, $fields) {
            return $this->find($id, $relations, $fields);
        });
    }

    /**
     * Create a new record
     * 
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $model = $this->model->create($data);
            return $model->toArray();
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to create record: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update a record by ID
     * 
     * @param mixed $id
     * @param array $data
     * @return array|null
     */
    public function update($id, array $data): ?array
    {
        try {
            $model = $this->model->find($id);
            if ($model) {
                $model->update($data);
                return $model->fresh()->toArray();
            }
            return null;
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to update record: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Delete a record by ID
     * 
     * @param mixed $id
     * @return bool
     */
    public function delete($id): bool
    {
        try {
            $model = $this->model->find($id);
            if ($model) {
                return $model->delete();
            }
            return false;
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to delete record: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Check if record exists
     * 
     * @param mixed $id
     * @return bool
     */
    public function exists($id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get first record or create if not exists
     * 
     * @param array $data
     * @return array
     */
    public function firstOrCreate(array $data): array
    {
        try {
            $model = $this->model->firstOrCreate($data);
            return $model->toArray();
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to find or create record: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Bulk insert records
     * 
     * @param array $data
     * @return bool
     */
    public function bulkInsert(array $data): bool
    {
        try {
            return $this->model->insert($data);
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to bulk insert records: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get the model instance
     * 
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Search records with advanced filtering
     * 
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @param array $fields
     * @return array
     */
    public function search(array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*']): array
    {
        try {
            $query = $this->model->query();
            
            // Load relations if specified
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            // Select specific fields if specified
            if (!empty($fields) && $fields !== ['*']) {
                $query->select($fields);
            }
            
            // Apply search filters
            $this->applyOptimizedFilters($query, $filters);
            
            // Apply sorting
            $this->applyOptimizedSorting($query, $filters);
            
            // Return paginated results
            $paginator = $query->paginate($perPage);
            
            return [
                'data' => $paginator->items(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                    'has_more_pages' => $paginator->hasMorePages(),
                ]
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to search records: " . $e->getMessage(), 0, $e);
        }
    }


    /**
     * Update records by condition
     * 
     * @param array $conditions
     * @param array $data
     * @return int
     */
    public function updateByCondition(array $conditions, array $data): int
    {
        try {
            $query = $this->model->query();
            
            // Apply conditions
            $this->applyUpdateConditions($query, $conditions);
            
            // Update records
            return $query->update($data);
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to update records by condition: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Apply conditions for update (reuse filter logic)
     * 
     * @param Builder $query
     * @param array $conditions
     * @return void
     */
    protected function applyUpdateConditions(Builder $query, array $conditions): void
    {
        $this->applyOptimizedFilters($query, $conditions);
    }

    /**
     * Get records by condition (without pagination)
     * 
     * @param array $conditions
     * @param array $relations
     * @param array $fields
     * @return array
     */
    public function getByCondition(array $conditions = [], array $relations = [], array $fields = ['*']): array
    {
        try {
            $query = $this->model->query();
            
            // Load relations if specified
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            // Select specific fields if specified
            if (!empty($fields) && $fields !== ['*']) {
                $query->select($fields);
            }
            
            // Apply conditions
            $this->applyUpdateConditions($query, $conditions);
            
            return $query->get()->toArray();
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to get records by condition: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Count records by condition
     * 
     * @param array $conditions
     * @return int
     */
    public function countByCondition(array $conditions = []): int
    {
        try {
            $query = $this->model->query();
            
            // Apply conditions
            $this->applyUpdateConditions($query, $conditions);
            
            return $query->count();
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to count records by condition: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Generate cache key for queries
     * 
     * @param string $method
     * @param mixed ...$params
     * @return string
     */
    protected function generateCacheKey(string $method, ...$params): string
    {
        $modelName = class_basename($this->model);
        $paramsHash = md5(serialize($params));
        return "repository_{$modelName}_{$method}_{$paramsHash}";
    }

    /**
     * Clear cache for specific model
     * 
     * @return void
     */
    public function clearCache(): void
    {
        $modelName = class_basename($this->model);
        $pattern = "repository_{$modelName}_*";
        
        // Clear cache by pattern (implementation depends on cache driver)
        if (method_exists(Cache::getStore(), 'flush')) {
            Cache::flush();
        }
    }

    /**
     * Get paginated results with caching
     * 
     * @param array $filters
     * @param int $perPage
     * @param array $relations
     * @param array $fields
     * @param int $cacheMinutes
     * @return array
     */
    public function allWithCache(array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*'], int $cacheMinutes = 30): array
    {
        $cacheKey = $this->generateCacheKey('all', $filters, $perPage, $relations, $fields);
        
        return Cache::remember($cacheKey, $cacheMinutes, function() use ($filters, $perPage, $relations, $fields) {
            return $this->all($filters, $perPage, $relations, $fields);
        });
    }

    /**
     * Soft delete a record (if model supports it)
     * 
     * @param mixed $id
     * @return bool
     */
    public function softDelete($id): bool
    {
        try {
            $model = $this->model->find($id);
            if ($model && method_exists($model, 'delete')) {
                return $model->delete();
            }
            return false;
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to soft delete record: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Restore a soft deleted record (if model supports it)
     * 
     * @param mixed $id
     * @return bool
     */
    public function restore($id): bool
    {
        try {
            $model = $this->model->withTrashed()->find($id);
            if ($model && method_exists($model, 'restore')) {
                return $model->restore();
            }
            return false;
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to restore record: " . $e->getMessage(), 0, $e);
        }
    }
} 