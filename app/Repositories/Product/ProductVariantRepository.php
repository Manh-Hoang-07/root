<?php

namespace App\Repositories\Product;

use App\Models\ProductVariant;
use App\Repositories\BaseRepository;
use App\Enums\ProductStatus;

class ProductVariantRepository extends BaseRepository
{
    public function model()
    {
        return ProductVariant::class;
    }

    /**
     * Find active variant by ID
     */
    public function findActive($id)
    {
        return $this->model->where('id', $id)
            ->where('status', ProductStatus::ACTIVE)
            ->with('product:id,name,price,sale_price')
            ->first()
            ?->toArray();
    }

    /**
     * Apply filters specific to variants
     */
    protected function applyFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        parent::applyFilters($query, $filters);
        
        // Filter by product ID
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        
        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Filter by stock status
        if (!empty($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'in_stock':
                    $query->where('stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', '=', 0);
                    break;
                case 'low_stock':
                    $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5);
                    break;
            }
        }
        
        // Filter by price range
        if (!empty($filters['price_from'])) {
            $query->where('price', '>=', $filters['price_from']);
        }
        
        if (!empty($filters['price_to'])) {
            $query->where('price', '<=', $filters['price_to']);
        }
    }
}
