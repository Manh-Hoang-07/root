<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BaseConditionTrait
{
    /**
     * Apply conditions (reuse filter logic)
     * 
     * @param Builder $query
     * @param array $conditions
     * @return void
     */
    protected function applyConditions(Builder $query, array $conditions): void
    {
        $this->applyFilters($query, $conditions);
    }
}
