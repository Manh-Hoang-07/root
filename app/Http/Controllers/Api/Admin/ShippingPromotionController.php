<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ShippingPromotionResource;
use App\Services\ShippingPromotionService;
use Illuminate\Http\Request;

class ShippingPromotionController extends BaseController
{
    public function __construct(ShippingPromotionService $shippingPromotionService)
    {
        parent::__construct($shippingPromotionService, ShippingPromotionResource::class);
    }
} 