<?php
namespace App\Services\Admin\Shipping;

use App\Repositories\Shipping\ShippingZoneRepository;
use App\Services\BaseService;

class ShippingZoneService extends BaseService
{
    public function __construct(ShippingZoneRepository $repo)
    {
        parent::__construct($repo);
    }
} 