<?php
namespace App\Repositories\Shipping;

use App\Models\ShippingPricingRule;
use App\Repositories\BaseRepository;

class ShippingPricingRuleRepository extends BaseRepository
{
    public function model(): string
    {
        return ShippingPricingRule::class;
    }
} 