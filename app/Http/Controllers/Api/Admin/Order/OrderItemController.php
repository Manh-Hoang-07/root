<?php
namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Api\BaseController;
use App\Services\Order\OrderItemService;

class OrderItemController extends BaseController
{
    public function __construct(OrderItemService $service)
    {
        parent::__construct($service);
    }
} 
