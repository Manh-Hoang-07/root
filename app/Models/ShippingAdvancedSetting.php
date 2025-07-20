<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAdvancedSetting extends Model
{
    protected $table = 'shipping_advanced_settings';
    protected $fillable = ['key', 'value'];
} 