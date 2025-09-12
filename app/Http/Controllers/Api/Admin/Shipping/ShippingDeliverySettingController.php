<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\BaseController;
use App\Services\Shipping\ShippingDeliverySettingService;
use App\Http\Requests\Admin\Shipping\ShippingDeliverySettingRequest;

class ShippingDeliverySettingController extends BaseController
{
    public function __construct(ShippingDeliverySettingService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = ShippingDeliverySettingRequest::class;
        $this->updateRequestClass = ShippingDeliverySettingRequest::class;
    }
} 
