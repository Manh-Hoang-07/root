<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\Product\ProductCategoryRequest;
use App\Services\Admin\Product\ProductCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends CrudController
{
    /**
     * @var ProductCategoryService
     */
    protected $service;
    protected $storeRequestClass = ProductCategoryRequest::class;
    protected $updateRequestClass = ProductCategoryRequest::class;
    protected $indexRelations = ['parent:id,name', 'children:id,parent_id,name'];
    protected $showRelations = ['parent:id,name', 'children:id,parent_id,name', 'createdUser:id,username', 'updatedUser:id,username'];

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
            $result = $this->service->getCategoryTree();
            return $this->apiResponse($result['success'], $result['data'], $result['message']);
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
            $result = $this->service->getCategoryProducts($id, $request->all(), $perPage);
            return $this->apiResponse($result['success'], $result['data'], $result['message']);
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }
}
