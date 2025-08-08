<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
    protected $model;
    
    // Cache configuration
    protected $enableCache = true;
    protected $cacheTtl = 3600; // 1 hour
    
    // Query optimization
    protected $maxRelations = 10; // Prevent too many relations
    protected $maxFields = 50; // Prevent too many fields

    public function __construct()
    {
        $this->model = app($this->model());
    }

    abstract public function model();

    public function all($filters = [], $perPage = 20, $relations = [], $fields = ['*'])
    {
        // Generate cache key
        $cacheKey = $this->generateCacheKey($filters, $perPage, $relations, $fields);
        
        // Try to get from cache
        if ($this->enableCache && !$this->shouldSkipCache($filters)) {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return $cached;
            }
        }
        
        $query = $this->model->query();
        
        // Optimize relations loading
        if (!empty($relations)) {
            $optimizedRelations = $this->optimizeRelations($relations);
            $query->with($optimizedRelations);
        }
        
        // Optimize field selection
        if (!empty($fields) && $fields !== ['*']) {
            $optimizedFields = $this->optimizeFields($fields);
            $query->select($optimizedFields);
        }
        
        // Apply filters with optimization
        $this->applyOptimizedFilters($query, $filters);
        
        // Apply sorting
        $this->applyOptimizedSorting($query, $filters);
        
        $result = $query->paginate($perPage);
        
        // Cache the result
        if ($this->enableCache && !$this->shouldSkipCache($filters)) {
            Cache::put($cacheKey, $result, $this->cacheTtl);
        }
        
        return $result;
    }
    
    /**
     * Apply optimized filters to query
     */
    protected function applyOptimizedFilters($query, $filters)
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
                    
                default:
                    // Check if field exists in model
                    if (in_array($key, $this->model->getFillable()) || in_array($key, ['id', 'created_at', 'updated_at'])) {
                        $query->where($key, $value);
                    }
            }
        }
    }
    
    /**
     * Apply IDs filter with optimization
     */
    protected function applyIdsFilter($query, $value)
    {
        $idsArray = is_array($value) ? $value : explode(',', $value);
        // Limit to prevent too many IDs
        $idsArray = array_slice($idsArray, 0, 1000);
        $query->whereIn('id', $idsArray);
    }
    
    /**
     * Apply date range filter
     */
    protected function applyDateRangeFilter($query, $value)
    {
        if (is_array($value) && isset($value['start']) && isset($value['end'])) {
            $query->whereBetween('created_at', [$value['start'], $value['end']]);
        }
    }
    
    /**
     * Apply optimized sorting
     */
    protected function applyOptimizedSorting($query, $filters)
    {
        if (isset($filters['sort_by'])) {
            $this->applySorting($query, $filters['sort_by']);
        } else {
            // Default sorting
            $query->orderBy('created_at', 'desc');
        }
    }
    
    protected function applySearchFilter($query, $searchValue)
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
    
    protected function getSearchableFields()
    {
        return ['name', 'username', 'email', 'phone', 'display_name'];
    }
    
    protected function applyRelationshipSearch($query, $searchValue)
    {
        // Override in child classes if needed
        // Example: search in profile relationship
        if (method_exists($this->model, 'profile')) {
            $query->orWhereHas('profile', function($q) use ($searchValue) {
                $q->where('name', 'like', "%$searchValue%");
            });
        }
    }
    
    protected function applySorting($query, $sortBy)
    {
        $parts = explode('_', $sortBy);
        if (count($parts) >= 2) {
            $direction = array_pop($parts);
            $field = implode('_', $parts);
            
            if (in_array($direction, ['asc', 'desc']) && 
                (in_array($field, $this->model->getFillable()) || 
                 in_array($field, ['id', 'created_at', 'updated_at', 'username', 'email']))) {
                $query->orderBy($field, $direction);
            }
        }
    }

    /**
     * Advanced relation optimization with field selection
     */
    protected function optimizeRelations($relations)
    {
        // Limit number of relations to prevent performance issues
        if (count($relations) > $this->maxRelations) {
            $relations = array_slice($relations, 0, $this->maxRelations);
        }
        
        $optimizedRelations = [];
        foreach ($relations as $relation) {
            if (strpos($relation, ':') !== false) {
                // Already has field selection
                $optimizedRelations[] = $relation;
            } else {
                // Add default field selection based on relation type
                $optimizedRelations[] = $this->getDefaultRelationFields($relation);
            }
        }
        return $optimizedRelations;
    }
    
    /**
     * Get default fields for common relations
     */
    protected function getDefaultRelationFields($relation)
    {
        $defaultFields = [
            'brand' => 'brand:id,name',
            'category' => 'category:id,name',
            'categories' => 'categories:id,name',
            'user' => 'user:id,name,email',
            'profile' => 'profile:id,user_id,name',
            'images' => 'images:id,imageable_id,url',
            'variants' => 'variants:id,product_id,name,sku,price',
            'inventory' => 'inventory:id,product_id,quantity,warehouse_id',
        ];
        
        return $defaultFields[$relation] ?? $relation;
    }
    
    /**
     * Optimize field selection
     */
    protected function optimizeFields($fields)
    {
        // Always include id and timestamps
        $requiredFields = ['id', 'created_at', 'updated_at'];
        $fields = array_merge($fields, $requiredFields);
        
        // Remove duplicates
        $fields = array_unique($fields);
        
        // Limit number of fields
        if (count($fields) > $this->maxFields) {
            $fields = array_slice($fields, 0, $this->maxFields);
        }
        
        return $fields;
    }
    
    /**
     * Generate cache key for query
     */
    protected function generateCacheKey($filters, $perPage, $relations, $fields)
    {
        $key = sprintf(
            '%s_%s_%s_%s_%s',
            get_class($this->model),
            md5(serialize($filters)),
            $perPage,
            md5(serialize($relations)),
            md5(serialize($fields))
        );
        
        return 'repository_' . $key;
    }
    
    /**
     * Determine if cache should be skipped
     */
    protected function shouldSkipCache($filters)
    {
        // Skip cache for real-time data or admin operations
        return isset($filters['skip_cache']) || 
               isset($filters['realtime']) || 
               isset($filters['admin']);
    }

    public function find($id, $relations = [], $fields = ['*'])
    {
        $cacheKey = "find_{$this->model->getTable()}_{$id}_" . md5(serialize($relations));
        
        if ($this->enableCache) {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return $cached;
            }
        }
        
        $query = $this->model->query();
        
        if (!empty($relations)) {
            $optimizedRelations = $this->optimizeRelations($relations);
            $query->with($optimizedRelations);
        }
        
        if (!empty($fields) && $fields !== ['*']) {
            $optimizedFields = $this->optimizeFields($fields);
            $query->select($optimizedFields);
        }
        
        $result = $query->findOrFail($id);
        
        if ($this->enableCache) {
            Cache::put($cacheKey, $result, $this->cacheTtl);
        }
        
        return $result;
    }

    public function create(array $data)
    {
        $result = $this->model->create($data);
        
        // Clear related caches
        $this->clearRelatedCaches();
        
        return $result;
    }

    public function update($id, array $data)
    {
        $item = $this->find($id);
        $item->update($data);
        
        // Clear related caches
        $this->clearRelatedCaches();
        
        return $item;
    }

    public function delete($id)
    {
        $item = $this->find($id);
        $result = $item->delete();
        
        // Clear related caches
        $this->clearRelatedCaches();
        
        return $result;
    }
    
    /**
     * Clear related caches
     */
    protected function clearRelatedCaches()
    {
        $pattern = 'repository_' . get_class($this->model) . '*';
        Cache::flush($pattern);
    }
    
    /**
     * Get model instance
     */
    public function getModel()
    {
        return $this->model;
    }
} 