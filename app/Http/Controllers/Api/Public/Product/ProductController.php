<?php

namespace App\Http\Controllers\Api\Public\Product;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Controllers\Api\Core\ListController;
use App\Services\Public\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends CrudController
{
    /**
     * @var ProductService
     */
    protected $service;
    protected $indexRelations = ['categories:id,name,slug', 'variants:id,name,sku,price,stock_quantity,sale_price'];
    protected $showRelations = ['categories:id,name,slug', 'variants:id,name,sku,price,stock_quantity,sale_price,images'];

    public function __construct(ProductService $service)
    {
        parent::__construct($service);
    }

    /**
     * Get featured products
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 12);
            $products = $this->service->getFeaturedProducts($limit);
            return $this->successResponseWithFormat($products, 'Lấy sản phẩm nổi bật thành công');
        } catch (\Exception $e) {
            $this->logError('Featured Products', $e);
            return $this->apiResponse(false, null, 'Không thể tải sản phẩm nổi bật', 500);
        }
    }

    /**
     * Search products
     */
    public function search(Request $request): JsonResponse
    {
        try {
            // Use parent search method with custom filters
            $filters = $this->parseRequestData($request);
            $filters['search_query'] = $request->get('q');
            $filters['category'] = $request->get('category');
            $filters['min_price'] = $request->get('min_price');
            $filters['max_price'] = $request->get('max_price');
            $filters['sort_by'] = $request->get('sort_by', 'created_at');
            $filters['sort_order'] = $request->get('sort_order', 'desc');

            $limit = min($request->get('limit', 12), $this->maxPerPage);
            $fields = $this->getSearchFields();
            $relations = $this->getSearchRelations();

            $products = $this->service->list($filters, $limit, $relations, $fields);
            return $this->successResponseWithFormat($products, 'Tìm kiếm sản phẩm thành công');
        } catch (\Exception $e) {
            $this->logError('Search Products', $e);
            return $this->apiResponse(false, null, 'Không thể tìm kiếm sản phẩm', 500);
        }
    }

    /**
     * Get products by category
     */
    public function byCategory(Request $request, $categoryId): JsonResponse
    {
        try {
            // Add category_id filter and use parent index method
            $request->merge(['category_id' => $categoryId]);
            return $this->index($request);
        } catch (\Exception $e) {
            $this->logError('Products by Category', $e, ['category_id' => $categoryId]);
            return $this->apiResponse(false, null, 'Không thể tải sản phẩm theo danh mục', 500);
        }
    }

    /**
     * Get product variants
     */
    public function variants($id): JsonResponse
    {
        try {
            $variants = $this->service->getProductVariants($id);
            if (!$variants) {
                return $this->apiResponse(false, null, 'Không tìm thấy sản phẩm', 404);
            }
            return $this->successResponseWithFormat($variants, 'Lấy biến thể sản phẩm thành công');
        } catch (\Exception $e) {
            $this->logError('Product Variants', $e, ['product_id' => $id]);
            return $this->apiResponse(false, null, 'Không thể tải biến thể sản phẩm', 500);
        }
    }

    /**
     * Override show to only show active products
     */
    public function show($id, ?Request $request = null): JsonResponse
    {
        try {
            $product = $this->service->getActiveProduct($id);
            if (!$product) {
                return $this->apiResponse(false, null, 'Không tìm thấy sản phẩm', 404);
            }
            return $this->successResponseWithFormat($product, 'Lấy chi tiết sản phẩm thành công');
        } catch (\Exception $e) {
            $this->logError('Show Product', $e, ['product_id' => $id]);
            return $this->apiResponse(false, null, 'Không thể tải thông tin sản phẩm', 500);
        }
    }

    /**
     * Process filters to only show active products
     */
    protected function processFilters(array $filters, string $context = 'index'): array
    {
        $filters['status'] = 'active';
        return $filters;
    }

    protected function getSearchFields(): array
    {
        return ['name', 'description', 'sku', 'content'];
    }
}
