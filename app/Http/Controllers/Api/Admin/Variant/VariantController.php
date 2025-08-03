<?php

namespace App\Http\Controllers\Api\Admin\Variant;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\Variant\VariantService;
use App\Http\Resources\Admin\Variant\VariantResource;
use App\Http\Requests\Admin\Variant\VariantRequest;
use Illuminate\Http\Request;

class VariantController extends BaseController
{
    public function __construct(VariantService $service)
    {
        parent::__construct($service, VariantResource::class);
        $this->storeRequestClass = VariantRequest::class;
        $this->updateRequestClass = VariantRequest::class;
    }

    /**
     * Get variants for a product
     */
    public function getProductVariants($product)
    {
        // Handle route model binding - $product could be a Product model or just an ID
        $productId = is_object($product) ? $product->id : $product;
        
        $variants = $this->service->getVariantsByProduct($productId);
        
        return $this->successResponse(
            VariantResource::collection($variants),
            'Lấy danh sách biến thể thành công'
        );
    }

    /**
     * Store a new variant
     */
    public function store()
    {
        $request = app($this->getStoreRequestClass());
        $variant = $this->service->createVariant($request->validated());
        
        return new VariantResource($variant);
    }

    /**
     * Update an existing variant
     */
    public function update($id)
    {
        $request = app($this->getUpdateRequestClass());
        $variant = $this->service->updateVariant($id, $request->validated());
        
        return new VariantResource($variant);
    }
} 