<?php

namespace App\Services\Admin\Product;

use App\Services\BaseService;
use App\Repositories\Product\ProductCategoryRepository;

class ProductCategoryService extends BaseService
{
    public function __construct(ProductCategoryRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Get category tree
     */
    public function getCategoryTree(array $relations = [], array $fields = ['*']): array
    {
        return $this->repo->getCategoryTree($relations, $fields);
    }


    /**
     * Get category products
     */
    public function getCategoryProducts($categoryId, array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*']): array
    {
        return $this->repo->getCategoryProducts($categoryId, $filters, $perPage, $relations, $fields);
    }

    /**
     * Bulk update categories
     */
    public function bulkUpdate(array $ids, string $action, $value = null): array
    {
        return $this->repo->bulkUpdate($ids, $action, $value);
    }

    /**
     * Override create to ensure slug generation
     */
    public function create($data): array
    {
        $data = $this->ensureSlug($data);
        return parent::create($data);
    }

    /**
     * Override update to ensure slug generation
     */
    public function update($id, $data): ?array
    {
        $data = $this->ensureSlug($data);
        return parent::update($id, $data);
    }
}

