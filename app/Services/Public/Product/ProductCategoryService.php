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
    public function getCategoryTree(): array
    {
        $result = $this->repo->getCategoryTree();
        
        return [
            'success' => true,
            'message' => 'Lấy cây danh mục sản phẩm thành công',
            'data' => $result
        ];
    }

    /**
     * Get products by category
     */
    public function getCategoryProducts($categoryId, $sortBy = 'created_at', $sortOrder = 'desc', $limit = 12): array
    {
        $result = $this->repo->getProductsByCategory($categoryId, $sortBy, $sortOrder, $limit);
        
        return [
            'success' => true,
            'message' => 'Lấy danh sách sản phẩm theo danh mục thành công',
            'data' => $result
        ];
    }
}
