<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingApiConfigService;
use App\Http\Resources\ShippingApiConfigResource;

class ShippingApiConfigController extends BaseController
{
    public function __construct(ShippingApiConfigService $service)
    {
        parent::__construct($service, ShippingApiConfigResource::class);
    }
} 