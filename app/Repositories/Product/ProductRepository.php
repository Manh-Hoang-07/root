<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository
{
    public function model()
    {
        return Product::class;
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(array $relations = [], array $fields = ['*']): array
    {
        $query = $this->buildQuery($relations, $fields);
        $query->whereRaw('stock_quantity <= min_stock_level');
        
        return $query->get()->toArray();
    }


    /**
     * Update product status
     */
    public function updateStatus($id, string $status): ?array
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id): ?array
    {
        $product = $this->model->find($id);
        if (!$product) {
            return null;
        }
        
        return $this->update($id, ['is_featured' => !$product->is_featured]);
    }



    /**
     * Apply filters specific to products
     */
    protected function applyFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        parent::applyFilters($query, $filters);
        
        // Handle category filter
        if (!empty($filters['category_id'])) {
            $categoryIds = is_array($filters['category_id']) ? $filters['category_id'] : explode(',', $filters['category_id']);
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('product_categories.id', $categoryIds);
            });
        }
    }
}
