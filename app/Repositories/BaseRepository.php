<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
    protected $model;
    
    // Cache configuration - tối ưu cho development
    protected $enableCache = true;
    protected $cacheTtl = 300; // Giảm từ 1 giờ xuống 5 phút
    
    // Query optimization
    protected $maxRelations = 5; // Giảm từ 10 xuống 5
    protected $maxFields = 20; // Giảm từ 50 xuống 20

    public function __construct()
    {
        $this->model = app($this->model());
    }

    abstract public function model();

    public function all($filters = [], $perPage = 20, $relations = [], $fields = ['*'])
    {
        // Generate cache key
        $cacheKey = $this->generateCacheKey($filters, $perPage, $relations, $fields);
        
        // Try to get from cache - chỉ cache cho production
        if ($this->enableCache && !$this->shouldSkipCache($filters) && app()->environment('production')) {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return $cached;
            }
        }
        
        $query = $this->model->query();
        
        // Optimize relations loading - giới hạn số lượng
        if (!empty($relations)) {
            $optimizedRelations = $this->optimizeRelations(array_slice($relations, 0, $this->maxRelations));
            $query->with($optimizedRelations);
        }
        
        // Optimize field selection
        if (!empty($fields) && $fields !== ['*']) {
            $optimizedFields = $this->optimizeFields(array_slice($fields, 0, $this->maxFields));
            $query->select($optimizedFields);
        }
        
        // Apply filters with optimization
        $this->applyOptimizedFilters($query, $filters);
        
        // Apply sorting
        $this->applyOptimizedSorting($query, $filters);
        
        $result = $query->paginate($perPage);
        
        // Cache the result - chỉ cache cho production
        if ($this->enableCache && !$this->shouldSkipCache($filters) && app()->environment('production')) {
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
        $idsArray = array_slice($idsArray, 0, 500); // Giảm từ 1000 xuống 500
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
        return ['name', 'description', 'title', 'content'];
    }

    protected function applyRelationshipSearch($query, $searchValue)
    {
        // Override in child classes if needed
    }

    protected function applySorting($query, $sortBy)
    {
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_at_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'updated_at_asc':
                $query->orderBy('updated_at', 'asc');
                break;
            case 'updated_at_desc':
                $query->orderBy('updated_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
    }

    protected function optimizeRelations($relations)
    {
        $optimizedRelations = [];
        foreach ($relations as $relation) {
            if (strpos($relation, ':') !== false) {
                // Nếu đã có select fields thì giữ nguyên
                $optimizedRelations[] = $relation;
            } else {
                // Tối ưu cho các relation chung
                $optimizedRelations[] = $this->getDefaultRelationFields($relation);
            }
        }
        return $optimizedRelations;
    }

    protected function getDefaultRelationFields($relation)
    {
        // Default optimization cho các relation chung
        switch ($relation) {
            case 'brand':
                return 'brand:id,name';
            case 'category':
                return 'category:id,name';
            case 'categories':
                return 'categories:id,name';
            case 'user':
                return 'user:id,name,email';
            case 'created_by':
                return 'created_by:id,name,email';
            case 'updated_by':
                return 'updated_by:id,name,email';
            default:
                return $relation;
        }
    }

    protected function optimizeFields($fields)
    {
        $optimizedFields = [];
        foreach ($fields as $field) {
            if (strpos($field, '.') !== false) {
                // Nếu là field của relation thì giữ nguyên
                $optimizedFields[] = $field;
            } else {
                // Kiểm tra field có tồn tại trong model không
                if (in_array($field, $this->model->getFillable()) || in_array($field, ['id', 'created_at', 'updated_at'])) {
                    $optimizedFields[] = $field;
                }
            }
        }
        return $optimizedFields;
    }

    protected function generateCacheKey($filters, $perPage, $relations, $fields)
    {
        $key = get_class($this->model) . '_' . md5(serialize([
            'filters' => $filters,
            'per_page' => $perPage,
            'relations' => $relations,
            'fields' => $fields
        ]));
        
        return 'repository_' . $key;
    }

    protected function shouldSkipCache($filters)
    {
        // Skip cache cho các filter động
        return isset($filters['search']) || isset($filters['date_range']);
    }

    public function find($id, $relations = [], $fields = ['*'])
    {
        $cacheKey = "repository_find_{$id}_" . md5(serialize([$relations, $fields]));
        
        // Try to get from cache - chỉ cache cho production
        if ($this->enableCache && app()->environment('production')) {
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
        
        $result = $query->find($id);
        
        // Cache the result - chỉ cache cho production
        if ($this->enableCache && app()->environment('production')) {
            Cache::put($cacheKey, $result, $this->cacheTtl);
        }
        
        return $result;
    }

    public function create(array $data)
    {
        $result = $this->model->create($data);
        $this->clearRelatedCaches();
        return $result;
    }

    public function update($id, array $data)
    {
        $model = $this->model->find($id);
        if ($model) {
            $model->update($data);
            $this->clearRelatedCaches();
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if ($model) {
            $result = $model->delete();
            $this->clearRelatedCaches();
            return $result;
        }
        return false;
    }

    protected function clearRelatedCaches()
    {
        // Clear cache khi có thay đổi dữ liệu
        if (app()->environment('production')) {
            Cache::flush();
        }
    }

    public function getModel()
    {
        return $this->model;
    }
} 