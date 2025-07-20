<?php
namespace App\Http\Controllers\Order;

use App\Http\Controllers\BaseController;
use App\Services\Order\OrderItemService;
use App\Http\Resources\OrderItemResource;

class OrderItemController extends BaseController
{
    public function __construct(OrderItemService $service)
    {
        parent::__construct($service, OrderItemResource::class);
    }
} 