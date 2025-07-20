<?php
namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingService;
use App\Http\Resources\Admin\Shipping\ShippingServiceResource;

class ServiceController extends BaseController
{
    public function __construct(ShippingService $service)
    {
        parent::__construct($service, ShippingServiceResource::class);
    }
    // ... các hàm đặc thù ...
} 