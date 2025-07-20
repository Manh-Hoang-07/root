<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingPricingRule extends Model
{
    protected $table = 'shipping_pricing_rules';
    protected $fillable = ['markup_percent', 'markup_fixed', 'min_fee'];
} 