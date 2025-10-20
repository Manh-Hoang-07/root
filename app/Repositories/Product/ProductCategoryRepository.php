<?php

namespace App\Repositories\Product;

use App\Models\ProductCategory;
use App\Repositories\BaseRepository;
use App\Enums\BasicStatus;

class ProductCategoryRepository extends BaseRepository
{
    public function model()
    {
        return ProductCategory::class;
    }


    /**
     * Get category tree (hierarchical structure)
     */
    public function getCategoryTree(array $relations = [], array $fields = ['*']): array
    {
        $query = $this->buildQuery($relations, $fields);
        $query->whereNull('parent_id')
            ->where('status', BasicStatus::Active->value)
            ->orderBy('sort_order')
            ->orderBy('name');

        $parentCategories = $query->get();
        $tree = [];

        foreach ($parentCategories as $parent) {
            $parentArray = $parent->toArray();
            $parentArray['children'] = $this->getChildrenCategories($parent->id, $relations, $fields);
            $tree[] = $parentArray;
        }

        return $tree;
    }

    /**
     * Get children categories recursively
     */
    private function getChildrenCategories($parentId, array $relations = [], array $fields = ['*']): array
    {
        $query = $this->buildQuery($relations, $fields);
        $query->where('parent_id', $parentId)
            ->where('status', BasicStatus::Active->value)
            ->orderBy('sort_order')
            ->orderBy('name');

        $children = $query->get();
        $result = [];

        foreach ($children as $child) {
            $childArray = $child->toArray();
            $childArray['children'] = $this->getChildrenCategories($child->id, $relations, $fields);
            $result[] = $childArray;
        }

        return $result;
    }


    /**
     * Get products by category with sorting
     */
    public function getProductsByCategory($categoryId, $sortBy = 'created_at', $sortOrder = 'desc', $limit = 12)
    {
        $category = $this->model->find($categoryId);
        if (!$category) {
            return [];
        }

        return $category->products()
            ->where('status', \App\Enums\ProductStatus::ACTIVE)
            ->with(['category:id,name,slug', 'variants:id,name,sku,price,stock_quantity,sale_price'])
            ->orderBy($sortBy, $sortOrder)
            ->paginate($limit)
            ->toArray();
    }

    /**
     * Get category products with filters and pagination
     */
    public function getCategoryProducts($categoryId, $filters = [], $perPage = 20, array $relations = [], array $fields = ['*'])
    {
        $category = $this->model->find($categoryId);
        if (!$category) {
            return [];
        }

        $query = $category->products()->with($relations);
        
        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }
        
        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }
        
        if (isset($filters['sort_by'])) {
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        }
        
        return $query->paginate($perPage)->toArray();
    }

    /**
     * Bulk update categories
     */
    public function bulkUpdate(array $ids, string $action, $value): array
    {
        $updated = 0;
        
        switch ($action) {
            case 'status':
                $updated = $this->model->whereIn('id', $ids)->update(['status' => $value]);
                break;
            case 'delete':
                $updated = $this->model->whereIn('id', $ids)->delete();
                break;
            // Add more bulk actions as needed
        }
        
        return [
            'updated_count' => $updated,
            'total_ids' => count($ids)
        ];
    }
}
