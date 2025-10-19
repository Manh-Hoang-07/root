<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\BaseRepository;

class OrderRepository extends BaseRepository
{
    public function model()
    {
        return Order::class;
    }

    /**
     * Find user order
     */
    public function findUserOrder($orderId, $userId): ?array
    {
        return $this->model->where('id', $orderId)
            ->where('user_id', $userId)
            ->with(['items.product:id,name,sku', 'items.variant:id,name,sku'])
            ->first()
            ?->toArray();
    }

    /**
     * Find guest order by order number and email
     */
    public function findGuestOrder($orderNumber, $email): ?array
    {
        return $this->model->where('order_number', $orderNumber)
            ->where('customer_email', $email)
            ->with(['items.product:id,name,sku', 'items.variant:id,name,sku'])
            ->first()
            ?->toArray();
    }

    /**
     * Find order by order number
     */
    public function findByOrderNumber($orderNumber): ?array
    {
        return $this->model->where('order_number', $orderNumber)
            ->first()
            ?->toArray();
    }

    /**
     * Get order items
     */
    public function getOrderItems($orderId): array
    {
        return OrderItem::where('order_id', $orderId)->get()->toArray();
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

    public function addItem(int $orderId, array $itemData): OrderItem
    {
        $itemData['order_id'] = $orderId;
        $itemData['total_price'] = ($itemData['unit_price'] ?? 0) * ($itemData['quantity'] ?? 1);
        return OrderItem::query()->create($itemData);
    }

    public function updateItem(int $orderId, int $itemId, array $data): bool
    {
        $item = OrderItem::query()->where('order_id', $orderId)->where('id', $itemId)->first();
        if (!$item) return false;
        $item->fill($data);
        if (array_key_exists('quantity', $data) || array_key_exists('unit_price', $data)) {
            $qty = $item->quantity;
            $price = $item->unit_price;
            $item->total_price = $qty * $price;
        }
        return $item->save();
    }

    public function removeItem(int $orderId, int $itemId): bool
    {
        return OrderItem::query()->where('order_id', $orderId)->where('id', $itemId)->delete() > 0;
    }

    public function recalculateTotals(int $orderId): ?array
    {
        /** @var Order $orderModel */
        $orderModel = $this->getModel()->newQuery()->with(['items'])->find($orderId);
        if (!$orderModel) return null;
        $subtotal = $orderModel->items->sum(function ($item) {
            return (float) $item->total_price;
        });
        $shipping = (float) ($orderModel->shipping_amount ?? 0);
        $tax = (float) ($orderModel->tax_amount ?? 0);
        $discount = (float) ($orderModel->discount_amount ?? 0);
        $total = max(0, $subtotal + $shipping + $tax - $discount);

        $orderModel->subtotal = $subtotal;
        $orderModel->total_amount = $total;
        $orderModel->save();
        $orderModel->refresh();
        return $orderModel->toArray();
    }

    public function confirmOrder(int $orderId, ?string $note = null): ?array
    {
        $order = $this->getModel()->find($orderId);
        if (!$order) return null;
        $order->status = 'confirmed';
        if ($note) {
            $order->notes = trim(($order->notes ?? '') . "\n[Confirm] " . $note);
        }
        $order->save();
        // Decrement stock quantities for each item
        $this->applyStockChange($orderId, -1);
        return $order->refresh()->toArray();
    }

    public function cancelOrder(int $orderId, string $reason): ?array
    {
        $order = $this->getModel()->find($orderId);
        if (!$order) return null;
        $order->status = 'cancelled';
        $order->notes = trim(($order->notes ?? '') . "\n[Cancel] " . $reason);
        $order->save();
        // Increment stock quantities back for each item
        $this->applyStockChange($orderId, 1);
        return $order->refresh()->toArray();
    }

    public function bulkUpdateStatus(array $ids, string $status): int
    {
        return $this->updateBy([
            function ($q) use ($ids) { $q->whereIn('id', $ids); }
        ], [
            'status' => $status
        ]);
    }

    /**
     * Apply stock change for all items of an order.
     * Multiplier: -1 to decrement on confirm, +1 to increment on cancel.
     */
    protected function applyStockChange(int $orderId, int $multiplier): void
    {
        $items = OrderItem::query()->where('order_id', $orderId)->get(['product_id', 'product_variant_id', 'quantity']);
        foreach ($items as $item) {
            $qtyChange = $multiplier * (int) $item->quantity;
            if ($item->product_variant_id) {
                $this->changeVariantStock((int) $item->product_variant_id, $qtyChange);
            } elseif ($item->product_id) {
                $this->changeProductStock((int) $item->product_id, $qtyChange);
            }
        }
    }

    protected function changeProductStock(int $productId, int $delta): void
    {
        $product = Product::query()->find($productId);
        if (!$product) return;
        $current = (int) ($product->stock_quantity ?? 0);
        $new = $current + $delta;
        if ($new < 0) { $new = 0; }
        $product->stock_quantity = $new;
        $product->save();
    }

    protected function changeVariantStock(int $variantId, int $delta): void
    {
        $variant = ProductVariant::query()->find($variantId);
        if (!$variant) return;
        $current = (int) ($variant->stock_quantity ?? 0);
        $new = $current + $delta;
        if ($new < 0) { $new = 0; }
        $variant->stock_quantity = $new;
        $variant->save();
    }

}
