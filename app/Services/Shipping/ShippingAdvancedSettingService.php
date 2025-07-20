<?php
namespace App\Services\Shipping;

use App\Repositories\Shipping\ShippingAdvancedSettingRepository;
use App\Services\BaseService;

class ShippingAdvancedSettingService extends BaseService
{
    public function __construct(ShippingAdvancedSettingRepository $repo)
    {
        parent::__construct($repo);
    }
} 