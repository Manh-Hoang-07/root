<?php
namespace App\Services\Shipping;

use App\Repositories\Shipping\ShippingDeliverySettingRepository;
use App\Services\BaseService;

class ShippingDeliverySettingService extends BaseService
{
    public function __construct(ShippingDeliverySettingRepository $repo)
    {
        parent::__construct($repo);
    }
} 