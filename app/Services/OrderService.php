<?php

namespace App\Services;

use App\Repositories\OrderRepository;

class OrderService extends BaseService
{
    public function __construct(OrderRepository $repository)
    {
        parent::__construct($repository);
    }

    public function filter(array $filters = [], $perPage = 15)
    {
        return $this->repository->filter($filters, $perPage);
    }
} 