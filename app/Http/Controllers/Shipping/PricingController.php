<?php
namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingPricingRuleService;
use App\Http\Resources\Admin\Shipping\ShippingPricingRuleResource;

class PricingController extends BaseController
{
    public function __construct(ShippingPricingRuleService $service)
    {
        parent::__construct($service, ShippingPricingRuleResource::class);
    }
} 