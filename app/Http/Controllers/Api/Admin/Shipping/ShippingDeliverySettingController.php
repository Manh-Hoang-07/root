<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingDeliverySettingService;
use App\Http\Resources\ShippingDeliverySettingResource;

class ShippingDeliverySettingController extends BaseController
{
    public function __construct(ShippingDeliverySettingService $service)
    {
        parent::__construct($service, ShippingDeliverySettingResource::class);
    }
} 