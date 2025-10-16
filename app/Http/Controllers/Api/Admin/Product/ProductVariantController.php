<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Product\ProductVariantService;
use App\Http\Requests\Admin\Product\ProductVariantRequest;
use App\Http\Requests\Admin\Product\VariantStatusUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductVariantController extends BaseController
{
    protected $storeRequestClass = ProductVariantRequest::class;
    protected $updateRequestClass = ProductVariantRequest::class;
    protected $statusUpdateRequestClass = VariantStatusUpdateRequest::class;
    protected $indexRelations = ['product:id,name,sku', 'attributes.attribute:id,name', 'attributes.value:id,value'];
    protected $showRelations = ['product:id,name,sku', 'attributes.attribute:id,name', 'attributes.value:id,value', 'createdUser:id,name', 'updatedUser:id,name'];

    public function __construct(ProductVariantService $service)
    {
        parent::__construct($service);
    }

    protected function getSearchFields(): array
    {
        return ['id', 'name', 'sku'];
    }



}
