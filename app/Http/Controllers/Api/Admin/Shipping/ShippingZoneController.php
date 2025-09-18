<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Shipping\ShippingZoneService;
use App\Http\Requests\Admin\Shipping\ShippingZoneRequest;

class ShippingZoneController extends BaseController
{
    /**
     * @var ShippingZoneService
     */
    protected $service;

    public function __construct(ShippingZoneService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = ShippingZoneRequest::class;
        $this->updateRequestClass = ShippingZoneRequest::class;
    }
} 
