<?php
namespace App\Services\Admin\Shipping;

use App\Repositories\Shipping\ShippingPromotionRepository;
use App\Services\BaseService;

class ShippingPromotionService extends BaseService
{
    public function __construct(ShippingPromotionRepository $repo)
    {
        parent::__construct($repo);
    }
} 