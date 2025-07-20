<?php
namespace App\Http\Controllers\Order;

use App\Http\Controllers\BaseController;
use App\Services\Order\OrderService;
use App\Http\Resources\OrderResource;

class OrderController extends BaseController
{
    public function __construct(OrderService $service)
    {
        parent::__construct($service, OrderResource::class);
    }
    // ... các hàm đặc thù ...
} 