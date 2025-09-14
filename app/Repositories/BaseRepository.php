<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = app($this->model());
    }

    abstract public function model();

    public function all($filters = [], $perPage = 20, $relations = [], $fields = ['*'])
    {
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
        return $query->paginate($perPage);
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
                case 'like':
                    // Handle LIKE conditions: ['field' => 'value'] or ['field' => ['value1', 'value2']]
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
                    break;
                case 'in':
                    // Handle IN conditions: ['field' => [1,2,3]]
                    foreach ($value as $field => $inValues) {
                        if (is_array($inValues)) {
                            $query->whereIn($field, $inValues);
                        }
                    }
                    break;
                case 'between':
                    // Handle BETWEEN conditions: ['field' => ['start', 'end']]
                    foreach ($value as $field => $betweenValues) {
                        if (is_array($betweenValues) && count($betweenValues) === 2) {
                            $query->whereBetween($field, $betweenValues);
                        }
                    }
                    break;
                case 'not_null':
                    // Handle NOT NULL conditions: ['field1', 'field2']
                    if (is_array($value)) {
                        foreach ($value as $field) {
                            $query->whereNotNull($field);
                        }
                    }
                    break;
                case 'is_null':
                    // Handle IS NULL conditions: ['field1', 'field2']
                    if (is_array($value)) {
                        foreach ($value as $field) {
                            $query->whereNull($field);
                        }
                    }
                    break;
                default:
                    // Check if field exists in model
                    if (in_array($key, $this->model->getFillable()) || in_array($key, ['id', 'created_at', 'updated_at'])) {
                        if (is_array($value)) {
                            $query->whereIn($key, $value);
                        } else {
                            $query->where($key, $value);
                        }
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

    public function find($id, $relations = [], $fields = ['*'])
    {
        $query = $this->model->query();
        // Load relations if specified
        if (!empty($relations)) {
            $query->with($relations);
        }
        // Select specific fields if specified
        if (!empty($fields) && $fields !== ['*']) {
            $query->select($fields);
        }
        return $query->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $model = $this->model->find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if ($model) {
            return $model->delete();
        }
        return false;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Search records with advanced filtering
     */
    public function search(array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*'])
    {
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
        return $query->paginate($perPage);
    }


    /**
     * Update records by condition
     */
    public function updateByCondition(array $conditions, array $data): int
    {
        $query = $this->model->query();
        
        // Apply conditions
        $this->applyUpdateConditions($query, $conditions);
        
        // Update records
        return $query->update($data);
    }

    /**
     * Apply conditions for update
     */
    protected function applyUpdateConditions($query, $conditions)
    {
        foreach ($conditions as $key => $value) {
            if ($value === '' || $value === null) continue;
            
            switch ($key) {
                case 'ids':
                    $idsArray = is_array($value) ? $value : explode(',', $value);
                    $query->whereIn('id', $idsArray);
                    break;
                case 'like':
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
                    break;
                case 'in':
                    foreach ($value as $field => $inValues) {
                        if (is_array($inValues)) {
                            $query->whereIn($field, $inValues);
                        }
                    }
                    break;
                case 'between':
                    foreach ($value as $field => $betweenValues) {
                        if (is_array($betweenValues) && count($betweenValues) === 2) {
                            $query->whereBetween($field, $betweenValues);
                        }
                    }
                    break;
                case 'not_null':
                    if (is_array($value)) {
                        foreach ($value as $field) {
                            $query->whereNotNull($field);
                        }
                    }
                    break;
                case 'is_null':
                    if (is_array($value)) {
                        foreach ($value as $field) {
                            $query->whereNull($field);
                        }
                    }
                    break;
                default:
                    if (in_array($key, $this->model->getFillable()) || in_array($key, ['id', 'created_at', 'updated_at'])) {
                        if (is_array($value)) {
                            $query->whereIn($key, $value);
                        } else {
                            $query->where($key, $value);
                        }
                    }
            }
        }
    }

    /**
     * Get records by condition (without pagination)
     */
    public function getByCondition(array $conditions = [], array $relations = [], array $fields = ['*'])
    {
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
        
        return $query->get();
    }

    /**
     * Count records by condition
     */
    public function countByCondition(array $conditions = []): int
    {
        $query = $this->model->query();
        
        // Apply conditions
        $this->applyUpdateConditions($query, $conditions);
        
        return $query->count();
    }
} 