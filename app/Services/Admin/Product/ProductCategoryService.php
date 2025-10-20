<?php

namespace App\Services\Admin\Product;

use App\Services\BaseService;
use App\Repositories\Product\ProductCategoryRepository;

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
    public function getCategoryTree(array $relations = [], array $fields = ['*']): array
    {
        try {
            $result = $this->repo->getCategoryTree($relations, $fields);
            
            return [
                'success' => true,
                'message' => 'Lấy cây danh mục thành công',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy cây danh mục',
                'data' => null
            ];
        }
    }


    /**
     * Get category products
     */
    public function getCategoryProducts($categoryId, array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*']): array
    {
        try {
            $result = $this->repo->getCategoryProducts($categoryId, $filters, $perPage, $relations, $fields);
            
            return [
                'success' => true,
                'message' => 'Lấy danh sách sản phẩm theo danh mục thành công',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy danh sách sản phẩm theo danh mục',
                'data' => null
            ];
        }
    }

    /**
     * Bulk update categories
     */
    public function bulkUpdate(array $ids, string $action, $value = null): array
    {
        try {
            $result = $this->repo->bulkUpdate($ids, $action, $value);
            
            return [
                'success' => true,
                'message' => 'Cập nhật hàng loạt danh mục thành công',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật hàng loạt danh mục',
                'data' => null
            ];
        }
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
    public function update($id, $data): array
    {
        $data = $this->ensureSlug($data);
        return parent::update($id, $data);
    }
}

