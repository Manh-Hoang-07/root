<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingAdvancedSettingService;
use App\Http\Resources\ShippingAdvancedSettingResource;

class ShippingAdvancedSettingController extends BaseController
{
    public function __construct(ShippingAdvancedSettingService $service)
    {
        parent::__construct($service, ShippingAdvancedSettingResource::class);
    }
} 