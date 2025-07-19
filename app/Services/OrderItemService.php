<?php

namespace App\Services;

use App\Repositories\OrderItemRepository;

class OrderItemService extends BaseService
{
    public function __construct(OrderItemRepository $repository)
    {
        parent::__construct($repository);
    }
} 