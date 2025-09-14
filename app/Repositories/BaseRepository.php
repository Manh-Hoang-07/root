<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Traits\BaseFilterTrait;
use App\Repositories\Traits\BaseSearchTrait;
use App\Repositories\Traits\BaseSortingTrait;
use App\Repositories\Traits\BaseConditionTrait;

/**
 * Base Repository class providing common database operations
 * 
 * @package App\Repositories
 */
abstract class BaseRepository
{
    use BaseFilterTrait, BaseSearchTrait, BaseSortingTrait, BaseConditionTrait;
    
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