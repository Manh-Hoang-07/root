<?php

namespace App\Repositories;

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
        
        // Load relations - chỉ load khi được yêu cầu cụ thể
        if (!empty($relations)) {
            // Tối ưu: chỉ select các field cần thiết cho relationships
            $optimizedRelations = [];
            foreach ($relations as $relation) {
                if (strpos($relation, ':') !== false) {
                    // Nếu đã có select fields thì giữ nguyên
                    $optimizedRelations[] = $relation;
                } else {
                    // Tự động tối ưu cho các relation phổ biến
                    switch ($relation) {
                        case 'brand':
                            $optimizedRelations[] = 'brand:id,name';
                            break;
                        case 'categories':
                            $optimizedRelations[] = 'categories:id,name';
                            break;
                        case 'images':
                            $optimizedRelations[] = 'images:id,imageable_id,url';
                            break;
                        case 'variants':
                            $optimizedRelations[] = 'variants:id,product_id,sku,price,sale_price,quantity,image,status';
                            break;
                        case 'variants.attributeValues':
                            $optimizedRelations[] = 'variants.attributeValues:id,variant_id,attribute_id,value';
                            break;
                        case 'variants.attributeValues.attribute':
                            $optimizedRelations[] = 'variants.attributeValues.attribute:id,name';
                            break;
                        case 'attributeValues':
                            $optimizedRelations[] = 'attributeValues:id,attribute_id,value';
                            break;
                        case 'attributeValues.attribute':
                            $optimizedRelations[] = 'attributeValues.attribute:id,name';
                            break;
                        default:
                            $optimizedRelations[] = $relation;
                    }
                }
            }
            $query->with($optimizedRelations);
        }
        
        // Select fields
        if (!empty($fields) && $fields !== ['*']) {
            $query->select($fields);
        }
        
        // Apply filters
        foreach ($filters as $key => $value) {
            if ($value === '' || $value === null) continue;
            
            if ($key === 'search') {
                $this->applySearchFilter($query, $value);
            } elseif ($key === 'sort_by') {
                $this->applySorting($query, $value);
            } elseif (in_array($key, ['page', 'per_page', 'sort', 'order'])) {
                continue;
            } else {
                $query->where($key, $value);
            }
        }
        
        // Default sorting
        if (!isset($filters['sort_by'])) {
            $query->orderBy('created_at', 'desc');
        }
        
        return $query->paginate($perPage);
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

    public function find($id, $relations = [], $fields = ['*'])
    {
        $query = $this->model->query();
        if (!empty($relations)) {
            $query->with($relations);
        }
        if (!empty($fields) && $fields !== ['*']) {
            $query->select($fields);
        }
        return $query->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $item = $this->find($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        $item = $this->find($id);
        return $item->delete();
    }
} 