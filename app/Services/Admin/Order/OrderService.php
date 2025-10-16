<?php

namespace App\Services\Admin\Order;

use App\Services\BaseService;
use App\Repositories\Order\OrderRepository;

class OrderService extends BaseService
{
    public function __construct(OrderRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Update order status
     */
    public function updateStatus($id, string $status): ?array
    {
        return $this->repo->updateStatus($id, $status);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($id, string $status): ?array
    {
        return $this->repo->updatePaymentStatus($id, $status);
    }

    /**
     * Update shipping status
     */
    public function updateShippingStatus($id, string $status, ?string $trackingNumber = null): ?array
    {
        return $this->repo->updateShippingStatus($id, $status, $trackingNumber);
    }

}

