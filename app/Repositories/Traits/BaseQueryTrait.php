<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BaseQueryTrait
{
    /**
     * Build query with relations and fields
     * 
     * @param array $relations
     * @param array $fields
     * @return Builder
     */
    protected function buildQuery(array $relations = [], array $fields = ['*']): Builder
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
        
        return $query;
    }
    
    /**
     * Format pagination response
     * 
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @return array
     */
    protected function formatPagination(\Illuminate\Pagination\LengthAwarePaginator $paginator): array
    {
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
    }
    
}
