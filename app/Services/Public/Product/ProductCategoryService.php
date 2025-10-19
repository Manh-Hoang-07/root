<?php

namespace App\Services\Public\Product;

use App\Services\BaseService;
use App\Repositories\Product\ProductCategoryRepository;
use App\Enums\BasicStatus;

class ProductCategoryService extends BaseService
{
    /**
     * @var ProductCategoryRepository
     */
    protected $repo;

    public function __construct(ProductCategoryRepository $repo)
    {
        parent::__construct($repo);
    }


    /**
     * Get category tree
     */
    public function getCategoryTree()
    {
        return $this->repo->getCategoryTree();
    }

    /**
     * Get products by category
     */
    public function getCategoryProducts($categoryId, $sortBy = 'created_at', $sortOrder = 'desc', $limit = 12)
    {
        return $this->repo->getProductsByCategory($categoryId, $sortBy, $sortOrder, $limit);
    }
}
