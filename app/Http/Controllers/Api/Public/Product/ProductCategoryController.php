<?php

namespace App\Http\Controllers\Api\Public\Product;

use App\Http\Controllers\Api\Core\CrudController;
use App\Services\Public\Product\ProductCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends CrudController
{
    protected ProductCategoryService $service;
    protected $indexRelations = ['parent:id,name,slug', 'children:id,name,slug,parent_id'];
    protected $showRelations = ['parent:id,name,slug', 'children:id,name,slug,parent_id'];

    public function __construct(ProductCategoryService $service)
    {
        parent::__construct($service);
        $this->service = $service;
    }

    /**
     * Get category tree
     */
    public function tree(): JsonResponse
    {
        $tree = $this->service->getCategoryTree();
        return $this->successResponseWithFormat($tree, 'Lấy cây danh mục thành công');
    }

    /**
     * Get products by category
     */
    public function products(Request $request, $id): JsonResponse
    {
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $limit = $request->get('limit', 12);

        $products = $this->service->getCategoryProducts($id, $sortBy, $sortOrder, $limit);
        return $this->successResponseWithFormat($products, 'Lấy sản phẩm theo danh mục thành công');
    }

    /**
     * Override show to only show active categories
     */
    public function show($id, ?Request $request = null): JsonResponse
    {
        // Add status filter to request if not already present
        if ($request) {
            $request->merge(['status' => 'active']);
        } else {
            $request = new Request(['status' => 'active']);
        }
        return parent::show($id, $request);
    }

    /**
     * Process filters to only show active categories
     */
    protected function processFilters(array $filters, string $context = 'index'): array
    {
        $filters['status'] = 'active';
        return $filters;
    }

    protected function getSearchFields(): array
    {
        return ['name', 'description'];
    }
}
