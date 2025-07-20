<?php
namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\BaseController;
use App\Services\Product\ProductInventoryService;
use App\Http\Resources\Admin\InventoryResource;

class ProductInventoryController extends BaseController
{
    public function __construct(ProductInventoryService $service)
    {
        parent::__construct($service, InventoryResource::class);
    }
} 