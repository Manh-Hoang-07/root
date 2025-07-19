<?php

namespace App\Repositories;

use App\Models\ShippingInfo;

class ShippingInfoRepository extends BaseRepository
{
    public function model()
    {
        return ShippingInfo::class;
    }
} 