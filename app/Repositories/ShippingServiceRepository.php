<?php
namespace App\Repositories;

use App\Models\ShippingService;

class ShippingServiceRepository extends BaseRepository
{
    public function model()
    {
        return ShippingService::class;
    }
} 