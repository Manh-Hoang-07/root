<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingPricingRuleService;
use App\Http\Resources\ShippingPricingRuleResource;

class ShippingPricingRuleController extends BaseController
{
    public function __construct(ShippingPricingRuleService $service)
    {
        parent::__construct($service, ShippingPricingRuleResource::class);
    }
} 