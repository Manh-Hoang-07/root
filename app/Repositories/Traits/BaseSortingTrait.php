<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BaseSortingTrait
{
    /**
     * Apply sorting
     * 
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    protected function applySorting(Builder $query, array $filters): void
    {
        if (isset($filters['sort_by'])) {
            $this->applySort($query, $filters['sort_by']);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Apply sort to query
     * 
     * @param Builder $query
     * @param string $sortBy
     * @return void
     */
    protected function applySort(Builder $query, string $sortBy): void
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
        $modelClass = get_class($this->model);
        static $allowedFieldsCache = [];
        
        if (!isset($allowedFieldsCache[$modelClass])) {
            $allowedFieldsCache[$modelClass] = array_merge(
                $this->model->getFillable(),
                ['id', 'created_at', 'updated_at', 'username', 'name', 'title', 'email', 'status']
            );
        }
        
        $allowedFields = $allowedFieldsCache[$modelClass];
        
        if (in_array($field, $allowedFields)) {
            $query->orderBy($field, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }
}
