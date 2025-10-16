<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Product\ProductCategoryService;
use App\Http\Requests\Admin\Product\ProductCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends BaseController
{
    protected $storeRequestClass = ProductCategoryRequest::class;
    protected $updateRequestClass = ProductCategoryRequest::class;
    protected $indexRelations = ['parent:id,name', 'children:id,parent_id,name'];
    protected $showRelations = ['parent:id,name', 'children:id,parent_id,name', 'createdUser:id,name', 'updatedUser:id,name'];

    public function __construct(ProductCategoryService $service)
    {
        parent::__construct($service);
    }

    protected function getSearchFields(): array
    {
        return ['id', 'name'];
    }

    /**
     * Get category tree
     */
    public function tree(): JsonResponse
    {
        try {
            $tree = $this->service->getRepo()->getCategoryTree();
            return $this->apiResponse(true, $tree, 'Lấy cây danh mục thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }


    /**
     * Get category products
     */
    public function products($id, Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 20), 100);
            $products = $this->service->getRepo()->getCategoryProducts($id, $request->all(), $perPage);
            return $this->apiResponse(true, $products, 'Lấy danh sách sản phẩm theo danh mục thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

}
