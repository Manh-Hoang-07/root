<?php
namespace App\Http\Controllers\Api\Admin\Inventory;

use App\Http\Controllers\BaseController;
use App\Services\Inventory\InventoryService;
use App\Http\Resources\InventoryResource;

class InventoryController extends BaseController
{
    public function __construct(InventoryService $service)
    {
        parent::__construct($service, InventoryResource::class);
    }
} 