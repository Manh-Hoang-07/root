<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\Product\ProductVariantRequest;
use App\Http\Requests\Admin\Product\VariantStatusUpdateRequest;
use App\Services\Admin\Product\ProductVariantService;
use Illuminate\Http\JsonResponse;

class ProductVariantController extends CrudController
{
    /**
     * @var ProductVariantService
     */
    protected $service;
    protected $storeRequestClass = ProductVariantRequest::class;
    protected $updateRequestClass = ProductVariantRequest::class;
    protected $statusUpdateRequestClass = VariantStatusUpdateRequest::class;
    protected $indexRelations = ['product:id,name,sku', 'attributes:id,value,product_attribute_id', 'attributes.attribute:id,name'];
    protected $showRelations = ['product:id,name,sku', 'attributes:id,value,product_attribute_id', 'attributes.attribute:id,name', 'createdUser:id,username', 'updatedUser:id,username'];

    public function __construct(ProductVariantService $service)
    {
        parent::__construct($service);
    }

    protected function getSearchFields(): array
    {
        return ['id', 'name', 'sku'];
    }

    /**
     * Get variants by product ID
     */
    public function variants($productId): JsonResponse
    {
        try {
            $variants = $this->service->getBy(['product_id' => $productId]);
            return $this->apiResponse(true, $variants, 'Lấy danh sách biến thể sản phẩm thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }
}
