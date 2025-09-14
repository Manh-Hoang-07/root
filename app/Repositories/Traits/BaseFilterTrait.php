<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BaseFilterTrait
{
    /**
     * Apply filters to query
     * 
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyFilters(Builder $query, array $filters): void
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
}
