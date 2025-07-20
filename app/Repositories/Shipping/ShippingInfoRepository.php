<?php

namespace App\Repositories\Shipping;

use App\Models\ShippingInfo;
use App\Repositories\BaseRepository;

class ShippingInfoRepository extends BaseRepository
{
    public function model()
    {
        return ShippingInfo::class;
    }
} 