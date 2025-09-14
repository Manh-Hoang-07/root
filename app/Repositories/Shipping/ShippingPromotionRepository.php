<?php
namespace App\Repositories\Shipping;

use App\Models\ShippingPromotion;
use App\Repositories\BaseRepository;

class ShippingPromotionRepository extends BaseRepository
{
    public function model(): string
    {
        return ShippingPromotion::class;
    }
} 