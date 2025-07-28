<?php
namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository extends BaseRepository
{
    public function model()
    {
        return Product::class;
    }

    /**
     * Get products with relationships
     */
    public function getProductsWithRelations($filters = [])
    {
        $query = $this->model->with(['brand', 'categories', 'variants']);

        // Apply filters
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['brand_id']) && !empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['category_id']) && !empty($filters['category_id'])) {
            $query->whereHas('categories', function (Builder $q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        // Apply sorting
        if (isset($filters['sort_by'])) {
            switch ($filters['sort_by']) {
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'created_at_desc':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get product with all relationships
     */
    public function getProductWithRelations($id)
    {
        return $this->model->with([
            'brand', 
            'categories', 
            'variants.attributeValues.attribute',
            'inventory.warehouse',
            'images'
        ])->findOrFail($id);
    }

    /**
     * Get products for dropdown/select
     */
    public function getProductsForSelect()
    {
        return $this->model->select('id', 'name', 'sku')
                          ->where('status', 'active')
                          ->orderBy('name', 'asc')
                          ->get();
    }

    /**
     * Search products by name or SKU
     */
    public function searchProducts($term, $limit = 10)
    {
        return $this->model->where('name', 'like', "%{$term}%")
                          ->orWhere('sku', 'like', "%{$term}%")
                          ->limit($limit)
                          ->get();
    }
} 