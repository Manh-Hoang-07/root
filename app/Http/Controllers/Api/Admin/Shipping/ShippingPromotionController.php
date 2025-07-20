<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingPromotionService;
use App\Http\Resources\Admin\Shipping\ShippingPromotionResource;

class ShippingPromotionController extends BaseController
{
    public function __construct(ShippingPromotionService $service)
    {
        parent::__construct($service, ShippingPromotionResource::class);
    }
} 