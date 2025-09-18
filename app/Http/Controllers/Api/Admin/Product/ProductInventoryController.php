<?php
namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Product\ProductInventoryService;
use App\Http\Requests\Admin\Product\ProductInventoryRequest;

class ProductInventoryController extends BaseController
{
    protected $storeRequestClass = ProductInventoryRequest::class;
    protected $updateRequestClass = ProductInventoryRequest::class;
    
    /**
     * @var ProductInventoryService
     */
    protected $service;

    public function __construct(ProductInventoryService $service)
    {
        parent::__construct($service);
    }
} 
