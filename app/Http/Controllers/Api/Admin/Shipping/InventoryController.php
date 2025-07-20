<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\InventoryService;
use App\Http\Resources\InventoryResource;

class InventoryController extends BaseController
{
    public function __construct(InventoryService $service)
    {
        parent::__construct($service, InventoryResource::class);
    }
} 