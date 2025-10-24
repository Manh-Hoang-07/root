<?php

namespace App\Http\Controllers\Api\Public\Product;

use App\Http\Controllers\Api\Core\CrudController;
use App\Services\Public\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends CrudController
{
    /**
     * @var ProductService
     */
    protected $service;
    
    /**
     * Relations to load for index operations
     */
    protected $indexRelations = ['categories:id,name,slug', 'variants:id,product_id,name,sku,price,stock_quantity,sale_price,image'];
    
    /**
     * Relations to load for show operations
     */
    protected $showRelations = ['categories:id,name,slug', 'variants:id,product_id,name,sku,price,stock_quantity,sale_price,image'];

    public function __construct(ProductService $service)
    {
        parent::__construct($service);
    }

    /**
     * Hiển thị sản phẩm theo slug
     */
    public function showBySlug(string $slug, Request $request): JsonResponse
    {
        try {
            $filters = $request ? $request->all() : [];

            // Quan hệ: ưu tiên từ request, fallback sang cấu hình mặc định cho show
            $requestRelations = $this->parseRelations($filters['relations'] ?? null);
            $relations = !empty($requestRelations) ? $requestRelations : $this->showRelations;

            // Fields: ưu tiên từ request, fallback sang mặc định cho show
            $requestFields = $filters['fields'] ?? null;
            $fields = $this->parseFields($requestFields);
            if (empty($fields) || $fields === ['*']) {
                $fields = $this->getDefaultShowFields();
            }

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
