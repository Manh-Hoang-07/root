<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingDeliverySetting extends Model
{
    protected $table = 'shipping_delivery_settings';
    protected $fillable = ['key', 'value'];
} 