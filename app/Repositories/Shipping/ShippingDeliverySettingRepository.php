<?php
namespace App\Repositories\Shipping;

use App\Models\ShippingDeliverySetting;
use App\Repositories\BaseRepository;

class ShippingDeliverySettingRepository extends BaseRepository
{
    public function model()
    {
        return ShippingDeliverySetting::class;
    }
} 