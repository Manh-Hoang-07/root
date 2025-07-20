<?php
namespace App\Http\Controllers\Api\Admin\Warehouse;

use App\Http\Controllers\BaseController;
use App\Services\Warehouse\WarehouseService;
use App\Http\Resources\Admin\WarehouseResource;

class WarehouseController extends BaseController
{
    public function __construct(WarehouseService $service)
    {
        parent::__construct($service, WarehouseResource::class);
    }
} 