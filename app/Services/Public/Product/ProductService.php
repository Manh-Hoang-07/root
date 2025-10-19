<?php

namespace App\Services\Public\Product;

use App\Services\BaseService;
use App\Repositories\Product\ProductRepository;
use App\Enums\ProductStatus;

class ProductService extends BaseService
{
    /**
     * @var ProductRepository
     */
    protected $repo;

    public function __construct(ProductRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Get active products with sorting and pagination
     */
    public function getActiveProducts($sortBy = 'created_at', $sortOrder = 'desc', $limit = 12)
    {
        return $this->repo->getActiveProducts($sortBy, $sortOrder, $limit);
    }

    /**
     * Get active product by ID
     */
    public function getActiveProduct($id)
    {
        return $this->repo->getActiveProduct($id);
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts($limit = 12)
    {
        return $this->repo->getFeaturedProducts($limit);
    }

    /**
     * Search products
     */
    public function searchProducts($query, $category = null, $minPrice = null, $maxPrice = null, $sortBy = 'created_at', $sortOrder = 'desc', $limit = 12)
    {
        return $this->repo->searchProducts($query, $category, $minPrice, $maxPrice, $sortBy, $sortOrder, $limit);
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory($categoryId, $sortBy = 'created_at', $sortOrder = 'desc', $limit = 12)
    {
        return $this->repo->getProductsByCategory($categoryId, $sortBy, $sortOrder, $limit);
    }

    /**
     * Get product variants
     */
    public function getProductVariants($productId)
    {
        return $this->repo->getProductVariants($productId);
    }
}
