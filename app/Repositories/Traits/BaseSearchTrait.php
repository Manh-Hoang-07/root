<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BaseSearchTrait
{
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
            $searchableFields = $this->getSearchableFields();
            $modelFillable = $this->model->getFillable();
            
            foreach ($searchableFields as $field) {
                // Only search in fields that exist in the model
                if (in_array($field, $modelFillable) || in_array($field, ['id', 'name', 'title', 'description', 'content'])) {
                    $q->orWhere($field, 'like', "%{$searchValue}%");
                }
            }
            
            // Apply relationship search if needed
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
}
