<?php

namespace App\Repositories\Slider;

use App\Repositories\BaseRepository;
use App\Models\Slider;

class SliderRepository extends BaseRepository
{
    public function model()
    {
        return Slider::class;
    }

    /**
     * Extend filters to support visible_only flag and default ordering.
     */
    protected function applyFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        parent::applyFilters($query, $filters);
        if (!empty($filters['visible_only'])) {
            $currentAt = !empty($filters['current_at']) ? \Carbon\Carbon::parse($filters['current_at']) : now();
            $query->where('status', 'active')
                ->where(function ($q) use ($currentAt) {
                    $q->whereNull('start_time')
                        ->orWhere('start_time', '<=', $currentAt);
                })
                ->where(function ($q) use ($currentAt) {
                    $q->whereNull('end_time')
                        ->orWhere('end_time', '>=', $currentAt);
                });
        }
        // Always order by sort_order asc, then created_at desc unless caller specifies otherwise
        $query->ordered();
    }
}
