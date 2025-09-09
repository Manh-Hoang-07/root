<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\BaseController;
use App\Services\Shipping\ShippingApiConfigService;
use App\Http\Resources\Admin\Shipping\ShippingApiConfigResource;
use App\Http\Requests\Admin\Shipping\ShippingApiConfigRequest;

class ShippingApiConfigController extends BaseController
{
    public function __construct(ShippingApiConfigService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = ShippingApiConfigRequest::class;
        $this->updateRequestClass = ShippingApiConfigRequest::class;
    }
} 
