<?php
namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository
{
    public function model(): string
    {
        return Product::class;
    }

    /**
     * Optimize relations for Product model
     */
    protected function optimizeRelations(array $relations): array
    {
        $optimizedRelations = [];
        foreach ($relations as $relation) {
            if (strpos($relation, ':') !== false) {
                // Nếu đã có select fields thì giữ nguyên
                $optimizedRelations[] = $relation;
            } else {
                // Tối ưu cho các relation của Product
                switch ($relation) {
                    case 'brand':
                        $optimizedRelations[] = 'brand:id,name';
                        break;
                    case 'categories':
                        $optimizedRelations[] = 'categories:id,name';
                        break;
                    case 'images':
                        $optimizedRelations[] = 'images:id,imageable_id,url';
                        break;
                    case 'variants':
                        $optimizedRelations[] = 'variants:id,product_id,sku,price,sale_price,quantity,image,status';
                        break;
                    case 'variants.attributeValues':
                        $optimizedRelations[] = 'variants.attributeValues:id,variant_id,attribute_id,value';
                        break;
                    case 'variants.attributeValues.attribute':
                        $optimizedRelations[] = 'variants.attributeValues.attribute:id,name';
                        break;
                    case 'attributeValues':
                        $optimizedRelations[] = 'attributeValues:id,attribute_id,value';
                        break;
                    case 'attributeValues.attribute':
                        $optimizedRelations[] = 'attributeValues.attribute:id,name';
                        break;
                    default:
                        $optimizedRelations[] = $relation;
                }
            }
        }
        return $optimizedRelations;
    }

    /**
     * Get products with relationships
     */
    public function getProductsWithRelations(array $filters = [])
    {
        $query = $this->model->query();

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

        // Chỉ load relationships cần thiết cho list view
        $query->with(['brand:id,name', 'categories:id,name']);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get product with all relationships
     */
    public function getProductWithRelations(int $id)
    {
        return $this->model->with([
            'brand:id,name', 
            'categories:id,name', 
            'variants:id,product_id,name,sku,barcode,price,sale_price,quantity,image,status',
            'variants.attributeValues:id,variant_id,attribute_id,value',
            'variants.attributeValues.attribute:id,name',
            'images:id,imageable_id,url'
        ])->findOrFail($id);
    }

    /**
     * Get products for dropdown/select
     */
    public function getProductsForSelect(): Collection
    {
        return $this->model->select('id', 'name', 'sku')
                          ->where('status', 'active')
                          ->orderBy('name', 'asc')
                          ->get();
    }

    /**
     * Search products by name or SKU
     */
    public function searchProducts(string $term, int $limit = 10)
    {
        return $this->model->where('name', 'like', "%{$term}%")
                          ->orWhere('sku', 'like', "%{$term}%")
                          ->limit($limit)
                          ->get();
    }

    /**
     * Override searchable fields for Product
     */
    protected function getSearchableFields(): array
    {
        return ['name', 'code', 'description', 'short_description'];
    }

    /**
     * Override search filter for Product to include brand and category search
     */
    protected function applySearchFilter(\Illuminate\Database\Eloquent\Builder $query, string $searchValue): void
    {
        $query->where(function($q) use ($searchValue) {
            // Search in main fields
            $q->orWhere('name', 'like', "%{$searchValue}%")
              ->orWhere('code', 'like', "%{$searchValue}%");
            
            // Search in brand name
            $q->orWhereHas('brand', function($brandQuery) use ($searchValue) {
                $brandQuery->where('name', 'like', "%{$searchValue}%");
            });
            
            // Search in category names
            $q->orWhereHas('categories', function($categoryQuery) use ($searchValue) {
                $categoryQuery->where('name', 'like', "%{$searchValue}%");
            });
        });
    }
} 