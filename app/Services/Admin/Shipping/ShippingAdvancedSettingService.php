<?php
namespace App\Services\Admin\Shipping;

use App\Repositories\Shipping\ShippingAdvancedSettingRepository;
use App\Services\BaseService;

class ShippingAdvancedSettingService extends BaseService
{
    public function __construct(ShippingAdvancedSettingRepository $repo)
    {
        parent::__construct($repo);
    }
} 