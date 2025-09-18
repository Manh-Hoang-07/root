<?php
namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Order\OrderItemService;

class OrderItemController extends BaseController
{
    /**
     * @var OrderItemService
     */
    protected $service;

    public function __construct(OrderItemService $service)
    {
        parent::__construct($service);
    }
} 
