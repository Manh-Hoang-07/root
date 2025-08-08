<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\BaseController;
use App\Services\Shipping\ShippingAdvancedSettingService;
use App\Http\Resources\Admin\Shipping\ShippingAdvancedSettingResource;
use App\Http\Requests\Admin\Shipping\ShippingAdvancedSettingRequest;

class ShippingAdvancedSettingController extends BaseController
{
    public function __construct(ShippingAdvancedSettingService $service)
    {
        parent::__construct($service, ShippingAdvancedSettingResource::class);
        $this->storeRequestClass = ShippingAdvancedSettingRequest::class;
        $this->updateRequestClass = ShippingAdvancedSettingRequest::class;
    }
} 
