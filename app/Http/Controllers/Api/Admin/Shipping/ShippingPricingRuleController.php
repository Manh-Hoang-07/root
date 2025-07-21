<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingPricingRuleService;
use App\Http\Resources\Admin\Shipping\ShippingPricingRuleResource;
use App\Http\Requests\Admin\Shipping\ShippingPricingRuleRequest;

class ShippingPricingRuleController extends BaseController
{
    public function __construct(ShippingPricingRuleService $service)
    {
        parent::__construct($service, ShippingPricingRuleResource::class);
        $this->storeRequestClass = ShippingPricingRuleRequest::class;
        $this->updateRequestClass = ShippingPricingRuleRequest::class;
    }
} 