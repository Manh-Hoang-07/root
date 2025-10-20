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
    public function getActiveProducts($sortBy = 'created_at', $sortOrder = 'desc', $limit = 12): array
    {
        $result = $this->repo->getActiveProducts($sortBy, $sortOrder, $limit);
        
        return [
            'success' => true,
            'message' => 'Lấy danh sách sản phẩm thành công',
            'data' => $result
        ];
    }

    /**
     * Get active product by ID
     */
    public function getActiveProduct($id): array
    {
        $result = $this->repo->getActiveProduct($id);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Lấy thông tin sản phẩm thành công',
                'data' => $result
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm',
                'data' => null
            ];
        }
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts($limit = 12): array
    {
        $result = $this->repo->getFeaturedProducts($limit);
        
        return [
            'success' => true,
            'message' => 'Lấy danh sách sản phẩm nổi bật thành công',
            'data' => $result
        ];
    }

    /**
     * Search products
     */
    public function searchProducts($query, $category = null, $minPrice = null, $maxPrice = null, $sortBy = 'created_at', $sortOrder = 'desc', $limit = 12): array
    {
        $result = $this->repo->searchProducts($query, $category, $minPrice, $maxPrice, $sortBy, $sortOrder, $limit);
        
        return [
            'success' => true,
            'message' => 'Tìm kiếm sản phẩm thành công',
            'data' => $result
        ];
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory($categoryId, $sortBy = 'created_at', $sortOrder = 'desc', $limit = 12): array
    {
        $result = $this->repo->getProductsByCategory($categoryId, $sortBy, $sortOrder, $limit);
        
        return [
            'success' => true,
            'message' => 'Lấy danh sách sản phẩm theo danh mục thành công',
            'data' => $result
        ];
    }

    /**
     * Get product variants
     */
    public function getProductVariants($productId): array
    {
        $result = $this->repo->getProductVariants($productId);
        
        return [
            'success' => true,
            'message' => 'Lấy danh sách biến thể sản phẩm thành công',
            'data' => $result
        ];
    }
}
