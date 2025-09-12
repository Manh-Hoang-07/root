<?php
namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\BaseController;
use App\Services\Product\ProductInventoryService;
use App\Http\Requests\Admin\Product\ProductInventoryRequest;

class ProductInventoryController extends BaseController
{
    public function __construct(ProductInventoryService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = ProductInventoryRequest::class;
        $this->updateRequestClass = ProductInventoryRequest::class;
    }
} 
