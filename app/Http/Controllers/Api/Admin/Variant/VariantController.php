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
    public function getProductVariants($productId)
    {
        $variants = $this->service->getVariantsByProduct($productId);
        return VariantResource::collection($variants);
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