<?php
namespace App\Http\Controllers\Api\Admin\Warehouse;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Warehouse\WarehouseService;
use App\Http\Requests\Admin\Warehouse\WarehouseRequest;

class WarehouseController extends BaseController
{
    protected $storeRequestClass = WarehouseRequest::class;
    protected $updateRequestClass = WarehouseRequest::class;
    
    /**
     * @var WarehouseService
     */
    protected $service;

    public function __construct(WarehouseService $service)
    {
        parent::__construct($service);
    }
} 
