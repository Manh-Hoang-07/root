<?php
namespace App\Repositories\Shipping;

use App\Models\ShippingApiConfig;
use App\Repositories\BaseRepository;

class ShippingApiConfigRepository extends BaseRepository
{
    public function model()
    {
        return ShippingApiConfig::class;
    }
} 