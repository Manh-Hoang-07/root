<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;
use App\Enums\ProductStatus;

class ProductRepository extends BaseRepository
{
    public function model()
    {
        return Product::class;
    }

    /**
     * Get active products with sorting and pagination
     */
    public function getActiveProducts($sortBy = 'created_at', $sortOrder = 'desc', $limit = 12)
    {
        return $this->model->where('status', ProductStatus::ACTIVE)
            ->with(['categories:id,name,slug', 'variants:id,name,sku,price,stock_quantity,sale_price'])
            ->orderBy($sortBy, $sortOrder)
            ->paginate($limit)
            ->toArray();
    }

    /**
     * Get active product by ID
     */
    public function getActiveProduct($id)
    {
        return $this->model->where('id', $id)
            ->where('status', ProductStatus::ACTIVE)
            ->with(['categories:id,name,slug', 'variants:id,name,sku,price,stock_quantity,sale_price,images'])
            ->first()
            ?->toArray();
    }

    /**
     * Find active product by ID
     */
    public function findActive($id)
    {
        return $this->model->where('id', $id)
            ->where('status', ProductStatus::ACTIVE)
            ->first()
            ?->toArray();
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts($limit = 12)
    {
        return $this->model->where('status', ProductStatus::ACTIVE)
            ->where('is_featured', true)
            ->with(['categories:id,name,slug', 'variants:id,name,sku,price,stock_quantity,sale_price'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Search products
     */
    public function searchProducts($query, $category = null, $minPrice = null, $maxPrice = null, $sortBy = 'created_at', $sortOrder = 'desc', $limit = 12)
    {
        $products = $this->model->where('status', ProductStatus::ACTIVE)
            ->with(['categories:id,name,slug', 'variants:id,name,sku,price,stock_quantity,sale_price']);

        // Search query
        if ($query) {
            $products->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            });
        }

        // Category filter
        if ($category) {
            $products->whereHas('categories', function ($query) use ($category) {
                $query->where('product_categories.id', $category);
            });
        }

        // Price range filter
        if ($minPrice !== null) {
            $products->where(function ($q) use ($minPrice) {
                $q->where('price', '>=', $minPrice)
                    ->orWhere('sale_price', '>=', $minPrice);
            });
        }

        if ($maxPrice !== null) {
            $products->where(function ($q) use ($maxPrice) {
                $q->where('price', '<=', $maxPrice)
                    ->orWhere('sale_price', '<=', $maxPrice);
            });
        }

        return $products->orderBy($sortBy, $sortOrder)
            ->paginate($limit)
            ->toArray();
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory($categoryId, $sortBy = 'created_at', $sortOrder = 'desc', $limit = 12)
    {
        return $this->model->where('status', ProductStatus::ACTIVE)
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('product_categories.id', $categoryId);
            })
            ->with(['categories:id,name,slug', 'variants:id,name,sku,price,stock_quantity,sale_price'])
            ->orderBy($sortBy, $sortOrder)
            ->paginate($limit)
            ->toArray();
    }

    /**
     * Get product variants
     */
    public function getProductVariants($productId)
    {
        $product = $this->model->find($productId);
        if (!$product) {
            return null;
        }

        return $product->variants()
            ->where('status', ProductStatus::ACTIVE)
            ->get()
            ->toArray();
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
