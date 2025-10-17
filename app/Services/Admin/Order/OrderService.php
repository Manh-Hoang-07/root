<?php

namespace App\Services\Admin\Order;

use App\Services\BaseService;
use App\Repositories\Order\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{
    public function __construct(OrderRepository $repo)
    {
        parent::__construct($repo);
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

    public function addItem($orderId, array $itemData): ?array
    {
        return DB::transaction(function () use ($orderId, $itemData) {
            $order = $this->find($orderId);
            if (!$order) return null;
            $this->repo->addItem($orderId, $itemData);
            return $this->recalculateTotals($orderId);
        });
    }

    public function updateItem($orderId, $itemId, array $data): ?array
    {
        return DB::transaction(function () use ($orderId, $itemId, $data) {
            $order = $this->find($orderId);
            if (!$order) return null;
            $ok = $this->repo->updateItem($orderId, $itemId, $data);
            if (!$ok) return null;
            return $this->recalculateTotals($orderId);
        });
    }

    public function removeItem($orderId, $itemId): ?array
    {
        return DB::transaction(function () use ($orderId, $itemId) {
            $order = $this->find($orderId);
            if (!$order) return null;
            $ok = $this->repo->removeItem($orderId, $itemId);
            if (!$ok) return null;
            return $this->recalculateTotals($orderId);
        });
    }

    public function recalculateTotals($orderId): ?array
    {
        return DB::transaction(function () use ($orderId) {
            $order = $this->repo->recalculateTotals($orderId);
            return $order;
        });
    }

    public function confirmOrder($orderId, ?string $note = null): ?array
    {
        return DB::transaction(function () use ($orderId, $note) {
            // Placeholder: stock reservations can be handled in repo in future
            $updated = $this->repo->confirmOrder($orderId, $note);
            return $updated;
        });
    }

    public function cancelOrder($orderId, string $reason): ?array
    {
        return DB::transaction(function () use ($orderId, $reason) {
            // Placeholder: restock logic can be handled in repo in future
            $updated = $this->repo->cancelOrder($orderId, $reason);
            return $updated;
        });
    }

    public function bulkUpdateStatus(array $ids, string $status): int
    {
        return $this->repo->bulkUpdateStatus($ids, $status);
    }

    protected function onCreateSuccess(array $result, array $data): void
    {
        DB::transaction(function () use ($result, $data) {
            $orderId = $result['id'];

            // Ensure order number exists
            if (empty($result['order_number'])) {
                $orderNumber = $this->generateOrderNumber($orderId);
                $this->repo->update($orderId, ['order_number' => $orderNumber]);
            }

            // Create items if provided
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    // Minimal required fields validated by request
                    $this->repo->addItem($orderId, $item);
                }
            }

            // Recalculate totals
            $this->repo->recalculateTotals($orderId);
        });
    }

    protected function onUpdateSuccess(array $result, $id, array $data): void
    {
        DB::transaction(function () use ($id, $data) {
            // If monetary fields changed, recalc total
            $monetaryKeys = ['shipping_amount', 'tax_amount', 'discount_amount'];
            foreach ($monetaryKeys as $key) {
                if (array_key_exists($key, $data)) {
                    $this->repo->recalculateTotals((int) $id);
                    break;
                }
            }
        });
    }

    private function generateOrderNumber(int $orderId): string
    {
        $datePart = date('Ymd');
        $seq = str_pad((string) ($orderId % 10000), 4, '0', STR_PAD_LEFT);
        return 'ORD-' . $datePart . '-' . $seq;
    }

}

