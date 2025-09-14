<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Shipping\ShippingPricingRuleService;
use App\Http\Requests\Admin\Shipping\ShippingPricingRuleRequest;

class ShippingPricingRuleController extends BaseController
{
    public function __construct(ShippingPricingRuleService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = ShippingPricingRuleRequest::class;
        $this->updateRequestClass = ShippingPricingRuleRequest::class;
    }
} 
