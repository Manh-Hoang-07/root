<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingPromotion extends Model
{
    protected $table = 'shipping_promotions';
    protected $fillable = ['name', 'discount', 'valid_until', 'status'];
} 