<?php
namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingApiConfigService;
use App\Http\Resources\Admin\Shipping\ShippingApiConfigResource;

class ApiController extends BaseController
{
    public function __construct(ShippingApiConfigService $service)
    {
        parent::__construct($service, ShippingApiConfigResource::class);
    }
} 