<?php

namespace App\Repositories\Product;

use App\Models\ProductAttribute;
use App\Repositories\BaseRepository;

class ProductAttributeRepository extends BaseRepository
{
    public function model()
    {
        return ProductAttribute::class;
    }

    /**
     * Update attribute status
     */
    public function updateStatus($id, string $status): ?array
    {
        return $this->update($id, ['status' => $status]);
    }


    /**
     * Apply filters specific to attributes
     */
    protected function applyFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        parent::applyFilters($query, $filters);
        
        // Filter by type
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Filter by is_required
        if (isset($filters['is_required'])) {
            $query->where('is_required', $filters['is_required']);
        }
        
        // Filter by is_variant
        if (isset($filters['is_variant'])) {
            $query->where('is_variant', $filters['is_variant']);
        }
    }
}
