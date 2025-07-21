<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingApiConfigService;
use App\Http\Resources\Admin\Shipping\ShippingApiConfigResource;
use App\Http\Requests\Admin\Shipping\ShippingApiConfigRequest;

class ShippingApiConfigController extends BaseController
{
    public function __construct(ShippingApiConfigService $service)
    {
        parent::__construct($service, ShippingApiConfigResource::class);
        $this->storeRequestClass = ShippingApiConfigRequest::class;
        $this->updateRequestClass = ShippingApiConfigRequest::class;
    }
} 