<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Shipping\ShippingZoneService;
use App\Http\Requests\Admin\Shipping\ShippingZoneRequest;

class ShippingZoneController extends BaseController
{
    protected static $serviceClass = ShippingZoneService::class;
    protected $storeRequestClass = ShippingZoneRequest::class;
    protected $updateRequestClass = ShippingZoneRequest::class;
}
