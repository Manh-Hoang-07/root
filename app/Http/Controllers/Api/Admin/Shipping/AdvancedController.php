<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\ShippingAdvancedSettingService;
use App\Http\Resources\Admin\Shipping\ShippingAdvancedSettingResource;

class AdvancedController extends BaseController
{
    public function __construct(ShippingAdvancedSettingService $service)
    {
        parent::__construct($service, ShippingAdvancedSettingResource::class);
    }
} 