<?php
namespace App\Http\Controllers\Api\Admin\Warehouse;

use App\Http\Controllers\Api\BaseController;
use App\Services\Warehouse\WarehouseService;
use App\Http\Resources\Admin\Warehouse\WarehouseResource;
use App\Http\Resources\Admin\Warehouse\WarehouseListResource;
use App\Http\Requests\Admin\Warehouse\WarehouseRequest;

class WarehouseController extends BaseController
{
    public function __construct(WarehouseService $service)
    {
        parent::__construct($service, WarehouseResource::class);
        $this->listResource = WarehouseListResource::class;
        $this->storeRequestClass = WarehouseRequest::class;
        $this->updateRequestClass = WarehouseRequest::class;
    }
} 
