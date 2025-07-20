<?php
namespace App\Services\Shipping;

use App\Repositories\Shipping\ShippingServiceRepository;
use App\Services\BaseService;

class ShippingService extends BaseService
{
    public function __construct(ShippingServiceRepository $repo)
    {
        parent::__construct($repo);
    }
    // ... logic đặc thù ...
} 