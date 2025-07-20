<?php
namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\BaseController;
use App\Services\Warehouse\WarehouseService;
use App\Http\Resources\WarehouseResource;

class WarehouseController extends BaseController
{
    public function __construct(WarehouseService $service)
    {
        parent::__construct($service, WarehouseResource::class);
    }
    // ... các hàm đặc thù ...
} 