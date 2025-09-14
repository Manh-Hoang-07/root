<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BaseConditionTrait
{
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
}
