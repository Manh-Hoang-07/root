<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Shipping\ShippingServiceService;
use App\Http\Requests\Admin\Shipping\ShippingServiceRequest;

class ShippingServiceController extends BaseController
{
    public function __construct(ShippingServiceService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = ShippingServiceRequest::class;
} 
