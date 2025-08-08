<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Api\BaseController;
use App\Services\Shipping\ShippingServiceService;
use App\Http\Resources\Admin\Shipping\ShippingServiceResource;
use App\Http\Requests\Admin\Shipping\ShippingServiceRequest;

class ShippingServiceController extends BaseController
{
    public function __construct(ShippingServiceService $service)
    {
        parent::__construct($service, ShippingServiceResource::class);
        $this->storeRequestClass = ShippingServiceRequest::class;
        $this->updateRequestClass = ShippingServiceRequest::class;
    }
} 
