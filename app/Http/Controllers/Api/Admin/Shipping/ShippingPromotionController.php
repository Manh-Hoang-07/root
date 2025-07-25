<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\ShippingPromotionService;
use App\Http\Resources\Admin\Shipping\ShippingPromotionResource;
use App\Http\Requests\Admin\Shipping\ShippingPromotionRequest;

class ShippingPromotionController extends BaseController
{
    public function __construct(ShippingPromotionService $service)
    {
        parent::__construct($service, ShippingPromotionResource::class);
        $this->storeRequestClass = ShippingPromotionRequest::class;
        $this->updateRequestClass = ShippingPromotionRequest::class;
    }
} 