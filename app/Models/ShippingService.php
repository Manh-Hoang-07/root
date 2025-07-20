<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingService extends Model
{
    protected $table = 'shipping_services';
    protected $fillable = ['name', 'code', 'provider_id', 'base_price', 'weight_fee', 'estimated_days', 'status', 'image'];
} 