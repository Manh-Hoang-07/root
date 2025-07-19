<?php

namespace App\Services;

use App\Repositories\ShippingInfoRepository;

class ShippingInfoService extends BaseService
{
    public function __construct(ShippingInfoRepository $repository)
    {
        parent::__construct($repository);
    }
} 