<?php
namespace App\Services\Admin\Order;

use App\Repositories\Order\OrderRepository;
use App\Services\BaseService;

class OrderService extends BaseService
{
    public function __construct(OrderRepository $repo)
    {
        parent::__construct($repo);
    }
    // ... logic đặc thù cho đơn hàng ...
} 