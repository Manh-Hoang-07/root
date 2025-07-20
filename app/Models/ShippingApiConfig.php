<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingApiConfig extends Model
{
    protected $table = 'shipping_api_configs';
    protected $fillable = ['name', 'api_key', 'secret_key', 'env', 'status'];
} 