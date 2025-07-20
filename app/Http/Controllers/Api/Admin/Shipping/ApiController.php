<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\ShippingApiConfigService;
use App\Http\Resources\Admin\Shipping\ShippingApiConfigResource;

class ApiController extends BaseController
{
    public function __construct(ShippingApiConfigService $service)
    {
        parent::__construct($service, ShippingApiConfigResource::class);
    }
} 