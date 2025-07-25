<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $table = 'shipping_zones';
    protected $fillable = ['name', 'provinces', 'base_fee', 'delivery_time', 'status'];
}
