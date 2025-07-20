<?php
namespace App\Services\Shipping;

use App\Repositories\Shipping\ShippingApiConfigRepository;
use App\Services\BaseService;

class ShippingApiConfigService extends BaseService
{
    public function __construct(ShippingApiConfigRepository $repo)
    {
        parent::__construct($repo);
    }
} 