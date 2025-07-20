<?php
namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingDeliverySettingService;
use App\Http\Resources\Admin\Shipping\ShippingDeliverySettingResource;

class DeliveryController extends BaseController
{
    public function __construct(ShippingDeliverySettingService $service)
    {
        parent::__construct($service, ShippingDeliverySettingResource::class);
    }
} 