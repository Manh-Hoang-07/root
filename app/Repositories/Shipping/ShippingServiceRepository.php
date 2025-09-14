<?php
namespace App\Repositories\Shipping;

use App\Models\ShippingService;
use App\Repositories\BaseRepository;

class ShippingServiceRepository extends BaseRepository
{
    public function model(): string
    {
        return ShippingService::class;
    }
} 