<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingZoneService;
use App\Http\Resources\Admin\Shipping\ShippingZoneResource;

class ShippingZoneController extends BaseController
{
    public function __construct(ShippingZoneService $service)
    {
        parent::__construct($service, ShippingZoneResource::class);
    }
} 