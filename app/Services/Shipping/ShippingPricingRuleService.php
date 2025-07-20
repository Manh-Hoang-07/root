<?php
namespace App\Services\Shipping;

use App\Repositories\Shipping\ShippingPricingRuleRepository;
use App\Services\BaseService;

class ShippingPricingRuleService extends BaseService
{
    public function __construct(ShippingPricingRuleRepository $repo)
    {
        parent::__construct($repo);
    }
} 