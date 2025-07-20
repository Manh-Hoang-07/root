<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingInfoService;
use App\Http\Resources\ShippingInfoResource;

class ShippingInfoController extends BaseController
{
    public function __construct(ShippingInfoService $service)
    {
        parent::__construct($service, ShippingInfoResource::class);
    }
} 