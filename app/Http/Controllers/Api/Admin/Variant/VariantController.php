<?php

namespace App\Http\Controllers\Api\Admin\Variant;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Variant\VariantService;
use App\Http\Requests\Admin\Variant\VariantRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VariantController extends BaseController
{
    protected static $serviceClass = VariantService::class;
    protected $storeRequestClass = VariantRequest::class;
    protected $updateRequestClass = VariantRequest::class;

    /**
     * Get variants for a product
     */
    public function getProductVariants($product)
    {
        // Handle route model binding - $product could be a Product model or just an ID
        $productId = is_object($product) ? $product->id : $product;
        
        $variants = $this->service->getVariantsByProduct($productId);
        
        return $this->successResponseWithFormat($variants, 'Lấy danh sách biến thể thành công');
    }

    /**
     * Store a new variant
     */
    public function store(): JsonResponse
    {
        $request = app($this->getStoreRequestClass());
        $variant = $this->service->createVariant($request->validated());
        
        return $this->successResponseWithFormat($variant, 'Tạo biến thể thành công');
    }

    /**
     * Update an existing variant
     */
    public function update($id): JsonResponse
    {
        $request = app($this->getUpdateRequestClass());
        $variant = $this->service->updateVariant($id, $request->validated());
        
        return $this->successResponseWithFormat($variant, 'Cập nhật biến thể thành công');
    }
} 