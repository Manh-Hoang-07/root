<?php

namespace App\Repositories;

use App\Models\ShippingPromotion;

class ShippingPromotionRepository extends BaseRepository
{
    public function model()
    {
        return ShippingPromotion::class;
    }
} 