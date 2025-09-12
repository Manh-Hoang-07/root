<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\BaseController;
use App\Services\Shipping\ShippingZoneService;
use App\Http\Requests\Admin\Shipping\ShippingZoneRequest;

class ShippingZoneController extends BaseController
{
    public function __construct(ShippingZoneService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = ShippingZoneRequest::class;
        $this->updateRequestClass = ShippingZoneRequest::class;
    }
} 
