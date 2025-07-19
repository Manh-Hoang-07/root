<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Resources\WarehouseResource;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class WarehouseController extends BaseController
{
    public function __construct(WarehouseService $warehouseService)
    {
        parent::__construct($warehouseService, WarehouseResource::class);
    }
} 