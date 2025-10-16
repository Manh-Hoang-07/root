<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\BaseRepository;

class OrderRepository extends BaseRepository
{
    public function model()
    {
        return Order::class;
    }

    /**
     * Update order status
     */
    public function updateStatus($id, string $status): ?array
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($id, string $status): ?array
    {
        return $this->update($id, ['payment_status' => $status]);
    }

    /**
     * Update shipping status
     */
    public function updateShippingStatus($id, string $status, ?string $trackingNumber = null): ?array
    {
        $data = ['shipping_status' => $status];
        
        if ($trackingNumber) {
            $data['tracking_number'] = $trackingNumber;
        }
        
        // Set shipped_at when status is shipped
        if ($status === 'shipped') {
            $data['shipped_at'] = now();
        }
        
        // Set delivered_at when status is delivered
        if ($status === 'delivered') {
            $data['delivered_at'] = now();
        }
        
        return $this->update($id, $data);
    }



}
