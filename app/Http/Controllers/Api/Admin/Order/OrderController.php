<?php
namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\Order\OrderService;
use App\Http\Resources\Admin\Order\OrderResource;
use App\Http\Requests\Admin\Order\OrderRequest;

class OrderController extends BaseController
{
    public function __construct(OrderService $service)
    {
        parent::__construct($service, OrderResource::class);
        $this->storeRequestClass = OrderRequest::class;
        $this->updateRequestClass = OrderRequest::class;
    }
} 
