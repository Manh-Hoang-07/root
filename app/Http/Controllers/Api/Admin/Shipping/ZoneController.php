<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\ShippingZoneService;
use App\Http\Resources\Admin\Shipping\ShippingZoneResource;

class ZoneController extends BaseController
{
    public function __construct(ShippingZoneService $service)
    {
        parent::__construct($service, ShippingZoneResource::class);
    }
} 