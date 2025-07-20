<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingServiceService;
use App\Http\Resources\Admin\Shipping\ShippingServiceResource;

class ShippingServiceController extends BaseController
{
    public function __construct(ShippingServiceService $service)
    {
        parent::__construct($service, ShippingServiceResource::class);
    }
} 