<?php
namespace App\Repositories\Shipping;

use App\Models\ShippingZone;
use App\Repositories\BaseRepository;

class ShippingZoneRepository extends BaseRepository
{
    public function model(): string
    {
        return ShippingZone::class;
    }
} 