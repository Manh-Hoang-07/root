<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\ShippingService;
use App\Http\Resources\Admin\Shipping\ShippingServiceResource;

class ServiceController extends BaseController
{
    public function __construct(ShippingService $service)
    {
        parent::__construct($service, ShippingServiceResource::class);
    }

    public function toggleStatus(\Illuminate\Http\Request $request, $id)
    {
        $service = $this->service->toggleStatus($id, $request->input('status'));
        return new ShippingServiceResource($service);
    }
} 