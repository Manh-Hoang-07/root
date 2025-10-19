<?php

namespace App\Services\Public\Order;

use App\Services\BaseService;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductVariantRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{
    protected CartRepository $cartRepo;
    protected ProductRepository $productRepo;
    protected ProductVariantRepository $variantRepo;

    public function __construct(
        OrderRepository $repo,
        CartRepository $cartRepo,
        ProductRepository $productRepo,
        ProductVariantRepository $variantRepo
    ) {
        parent::__construct($repo);
        $this->cartRepo = $cartRepo;
        $this->productRepo = $productRepo;
        $this->variantRepo = $variantRepo;
    }

    /**
     * Create order for authenticated user
     */
    public function createOrder(array $data, ?int $userId = null): ?array
    {
        return DB::transaction(function () use ($data, $userId) {
            $cartId = $data['cart_id'];
            $cart = $this->cartRepo->getCartWithItems($cartId);
            
            if (!$cart || empty($cart['items'])) {
                throw new \Exception('Giỏ hàng trống');
            }
            
            // Validate stock
            foreach ($cart['items'] as $item) {
                if ($item['product_variant_id']) {
                    $variant = $this->variantRepo->find($item['product_variant_id']);
                    if (!$variant || $variant['stock_quantity'] < $item['quantity']) {
                        throw new \Exception("Sản phẩm {$item['product_name']} không đủ hàng trong kho");
                    }
                } else {
                    $product = $this->productRepo->find($item['product_id']);
                    if (!$product || $product['stock_quantity'] < $item['quantity']) {
                        throw new \Exception("Sản phẩm {$item['product_name']} không đủ hàng trong kho");
                    }
                }
            }
            
            // Create order
            $orderData = [
                'user_id' => $userId,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'billing_address' => $data['billing_address'] ?? $data['shipping_address'],
                'currency' => 'VND',
                'notes' => $data['notes'] ?? null,
                'payment_method' => $data['payment_method'],
                'shipping_method' => $data['shipping_method'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_status' => 'pending',
                'subtotal' => $cart['subtotal'],
                'tax_amount' => $cart['tax_amount'],
                'shipping_amount' => $this->calculateShipping($data['shipping_method']),
                'discount_amount' => $cart['discount_amount'],
                'total_amount' => $cart['total_amount'] + $this->calculateShipping($data['shipping_method']),
                'created_user_id' => $userId,
            ];
            
            $order = $this->repo->create($orderData);
            
            // Generate order number
            $orderNumber = $this->generateOrderNumber($order['id']);
            $this->repo->update($order['id'], ['order_number' => $orderNumber]);
            
            // Create order items
            foreach ($cart['items'] as $item) {
                $this->repo->addItem($order['id'], [
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'],
                    'variant_name' => $item['variant_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'product_attributes' => $item['product_attributes'] ?? null,
                ]);
            }
            
            // Clear cart
            $this->cartRepo->clearCart($cartId);
            
            return $this->repo->find($order['id']);
        });
    }

    /**
     * Create guest order
     */
    public function createGuestOrder(array $data): ?array
    {
        return $this->createOrder($data);
    }

    /**
     * Get user order
     */
    public function getUserOrder($orderId, $userId): ?array
    {
        return $this->repo->findUserOrder($orderId, $userId);
    }

    /**
     * Get guest order
     */
    public function getGuestOrder($orderNumber, $email): ?array
    {
        return $this->repo->findGuestOrder($orderNumber, $email);
    }

    /**
     * Get order by number
     */
    public function getOrderByNumber($orderNumber): ?array
    {
        return $this->repo->findByOrderNumber($orderNumber);
    }

    /**
     * Process payment
     */
    public function processPayment($orderId, array $paymentData, ?int $userId = null): ?array
    {
        return DB::transaction(function () use ($orderId, $paymentData, $userId) {
            $order = $this->repo->find($orderId);
            
            if (!$order) {
                throw new \Exception('Đơn hàng không tồn tại');
            }
            
            if ($order['payment_status'] === 'paid') {
                throw new \Exception('Đơn hàng đã được thanh toán');
            }
            
            // Update payment status
            $updateData = [
                'payment_status' => 'paid',
                'updated_user_id' => $userId,
            ];
            
            // Store payment details if needed
            if (isset($paymentData['transaction_id'])) {
                $updateData['notes'] = trim(($order['notes'] ?? '') . "\n[Payment] Transaction ID: " . $paymentData['transaction_id']);
            }
            
            $this->repo->update($orderId, $updateData);
            
            // Update order status if payment is successful
            if ($order['status'] === 'pending') {
                $this->repo->update($orderId, ['status' => 'confirmed']);
                
                // Apply stock changes
                $this->applyStockChange($orderId, -1);
            }
            
            return $this->repo->find($orderId);
        });
    }

    /**
     * Calculate shipping cost
     */
    private function calculateShipping($shippingMethod): float
    {
        // TODO: Implement shipping calculation based on method and address
        switch ($shippingMethod) {
            case 'standard':
                return 30000; // 30,000 VND
            case 'express':
                return 50000; // 50,000 VND
            default:
                return 30000;
        }
    }

    /**
     * Generate order number
     */
    private function generateOrderNumber(int $orderId): string
    {
        $datePart = date('Ymd');
        $seq = str_pad((string) ($orderId % 10000), 4, '0', STR_PAD_LEFT);
        return 'ORD-' . $datePart . '-' . $seq;
    }

    /**
     * Apply stock change for all items of an order
     */
    private function applyStockChange(int $orderId, int $multiplier): void
    {
        $items = $this->repo->getOrderItems($orderId);
        
        foreach ($items as $item) {
            $qtyChange = $multiplier * (int) $item['quantity'];
            
            if ($item['product_variant_id']) {
                $this->changeVariantStock((int) $item['product_variant_id'], $qtyChange);
            } elseif ($item['product_id']) {
                $this->changeProductStock((int) $item['product_id'], $qtyChange);
            }
        }
    }

    /**
     * Change product stock
     */
    private function changeProductStock(int $productId, int $delta): void
    {
        $product = $this->productRepo->find($productId);
        if (!$product) return;
        
        $current = (int) ($product['stock_quantity'] ?? 0);
        $new = max(0, $current + $delta);
        
        $this->productRepo->update($productId, ['stock_quantity' => $new]);
    }

    /**
     * Change variant stock
     */
    private function changeVariantStock(int $variantId, int $delta): void
    {
        $variant = $this->variantRepo->find($variantId);
        if (!$variant) return;
        
        $current = (int) ($variant['stock_quantity'] ?? 0);
        $new = max(0, $current + $delta);
        
        $this->variantRepo->update($variantId, ['stock_quantity' => $new]);
    }
}