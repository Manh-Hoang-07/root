<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Shipping\ShippingAdvancedSettingService;
use App\Http\Requests\Admin\Shipping\ShippingAdvancedSettingRequest;

class ShippingAdvancedSettingController extends BaseController
{
    public function __construct(ShippingAdvancedSettingService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = ShippingAdvancedSettingRequest::class;
} 
