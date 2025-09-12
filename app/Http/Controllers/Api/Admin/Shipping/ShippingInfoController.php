<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\BaseController;
use App\Services\Shipping\ShippingInfoService;

class ShippingInfoController extends BaseController
{
    public function __construct(ShippingInfoService $service)
    {
        parent::__construct($service);
    }
} 
