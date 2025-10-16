<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Product\ProductAttributeService;
use App\Http\Requests\Admin\Product\ProductAttributeRequest;
use App\Http\Requests\Admin\Product\AttributeStatusUpdateRequest;

class ProductAttributeController extends BaseController
{
    protected $storeRequestClass = ProductAttributeRequest::class;
    protected $updateRequestClass = ProductAttributeRequest::class;
    protected $statusUpdateRequestClass = AttributeStatusUpdateRequest::class;
    protected $indexRelations = ['values:id,product_attribute_id,value'];
    protected $showRelations = ['values:id,product_attribute_id,value', 'createdUser:id,name', 'updatedUser:id,name'];

    public function __construct(ProductAttributeService $service)
    {
        parent::__construct($service);
    }

    protected function getSearchFields(): array
    {
        return ['id', 'name'];
    }
}
