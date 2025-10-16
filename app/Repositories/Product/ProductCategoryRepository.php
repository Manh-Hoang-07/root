<?php

namespace App\Repositories\Product;

use App\Models\ProductCategory;
use App\Repositories\BaseRepository;

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
     * Get category products
     */
    public function getCategoryProducts($categoryId, array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*']): array
    {
        $category = $this->model->find($categoryId);
        if (!$category) {
            return [];
        }
        
        $query = $category->products();
        
        // Apply relations and fields
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        if (!empty($fields) && $fields !== ['*']) {
            $query->select($fields);
        }
        
        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        
        return $this->formatPagination($query->paginate($perPage));
    }


}

