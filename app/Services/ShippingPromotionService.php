<?php

namespace App\Services;

use App\Repositories\ShippingPromotionRepository;

class ShippingPromotionService extends BaseService
{
    public function __construct(ShippingPromotionRepository $repository)
    {
        parent::__construct($repository);
    }
} 