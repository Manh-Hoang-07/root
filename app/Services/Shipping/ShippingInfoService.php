<?php
namespace App\Services\Shipping;

use App\Repositories\Shipping\ShippingInfoRepository;
use App\Services\BaseService;

class ShippingInfoService extends BaseService
{
    public function __construct(ShippingInfoRepository $repo)
    {
        parent::__construct($repo);
    }
} 