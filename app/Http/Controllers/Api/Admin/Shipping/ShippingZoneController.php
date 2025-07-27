<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\Shipping\ShippingZoneService;
use App\Http\Resources\Admin\Shipping\ShippingZoneResource;
use App\Http\Requests\Admin\Shipping\ShippingZoneRequest;

class ShippingZoneController extends BaseController
{
    public function __construct(ShippingZoneService $service)
    {
        parent::__construct($service, ShippingZoneResource::class);
        $this->storeRequestClass = ShippingZoneRequest::class;
        $this->updateRequestClass = ShippingZoneRequest::class;
    }
} 
