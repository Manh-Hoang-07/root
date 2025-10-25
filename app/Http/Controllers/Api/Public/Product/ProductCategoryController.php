<?php

namespace App\Http\Controllers\Api\Public\Product;

use App\Http\Controllers\Api\Core\CrudController;
use App\Services\Public\Product\ProductCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends CrudController
{
    protected $indexRelations = ['parent:id,name,slug', 'children:id,name,slug,parent_id'];
    protected $showRelations = ['parent:id,name,slug', 'children:id,name,slug,parent_id'];

    public function __construct(ProductCategoryService $service)
    {
        parent::__construct($service);
    }

    /**
     * Get category tree
     */
    public function tree(): JsonResponse
    {
        /** @var ProductCategoryService $service */
        $service = $this->service;
        $result = $service->getCategoryTree();
        return $this->successResponseWithFormat($result['data'], $result['message']);
    }

    /**
     * Get products by category (slug)
     */
    public function products(Request $request, string $slug): JsonResponse
    {
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $limit = $request->get('limit', 12);

        /** @var ProductCategoryService $service */
        $service = $this->service;
        $result = $service->getCategoryProductsBySlug($slug, $sortBy, $sortOrder, $limit);
        return $this->successResponseWithFormat($result['data'], $result['message']);
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

    /**
     * Hiển thị danh mục theo slug (chỉ trả về danh mục active)
     */
    public function showBySlug(string $slug, Request $request): JsonResponse
    {
        try {
            $filters = $request ? $request->all() : [];

            // Quan hệ: ưu tiên từ request, fallback sang cấu hình mặc định cho show
            $requestRelations = $this->parseRelations($filters['relations'] ?? null);
            $relations = !empty($requestRelations) ? $requestRelations : $this->showRelations;

            // Fields: ưu tiên từ request, fallback sang mặc định cho show
            $fields = $this->parseFields($filters['fields'] ?? null);
            if (empty($fields) || $fields === ['*']) {
                $fields = $this->getDefaultShowFields();
            }

            // Bắt buộc trạng thái active khi lấy theo slug
            $item = $this->service->findOneBy(['slug' => $slug, 'status' => 'active'], $relations, $fields);
            if (!$item) {
                return $this->apiResponse(false, null, 'Không tìm thấy dữ liệu', 404);
            }

            return $this->successResponseWithFormat($item, 'Lấy thông tin chi tiết thành công', 200);
        } catch (\Exception $e) {
            $this->logError('ShowBySlug', $e, ['slug' => $slug]);
            return $this->apiResponse(false, null, 'Không thể tải thông tin chi tiết', 500);
        }
    }
}
