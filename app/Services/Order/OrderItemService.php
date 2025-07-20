<?php
namespace App\Services\Order;

use App\Repositories\Order\OrderItemRepository;
use App\Services\BaseService;

class OrderItemService extends BaseService
{
    public function __construct(OrderItemRepository $repo)
    {
        parent::__construct($repo);
    }
} 