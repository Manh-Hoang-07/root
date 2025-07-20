<?php
namespace App\Repositories\Shipping;

use App\Models\ShippingAdvancedSetting;
use App\Repositories\BaseRepository;

class ShippingAdvancedSettingRepository extends BaseRepository
{
    public function model()
    {
        return ShippingAdvancedSetting::class;
    }
} 