<?php

namespace App\Services\Public\Order;

use App\Services\BaseService;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductVariantRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService extends BaseService
{
    protected CartRepository $cartRepo;
    protected ProductRepository $productRepo;
    protected ProductVariantRepository $variantRepo;

    /**
     * @var OrderRepository
     */
    protected $repo;

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
     * Create order for authenticated user or guest
     */
    public function createOrder(array $data, ?int $userId = null): array
    {
        // If userId is not provided, get it from Auth
        if ($userId === null) {
            $userId = Auth::id();
        }
        // Get stored address information
        $addressInfo = $this->getStoredAddressInfo($userId);
        if (!$addressInfo) {
            return [
                'success' => false,
                'message' => 'Vui lòng cập nhật thông tin địa chỉ trước khi tạo đơn hàng',
                'error_code' => 'ADDRESS_REQUIRED'
            ];
        }

        $items = [];
        $subtotal = 0;
        $taxAmount = 0;
        $discountAmount = 0;

        // If cart_header_id is provided, get items from cart
        if (isset($data['cart_header_id'])) {
            $cartHeaderId = $data['cart_header_id'];
            $cart = $this->cartRepo->getCartWithItems($cartHeaderId);

            if (!$cart || empty($cart['items'])) {
                // Check if this is a legacy unified order request and provide more helpful error
                $hasCartId = isset($data['cart_header_id']);
                $hasItems = isset($data['items']) && !empty($data['items']);

                if (!$hasCartId && !$hasItems) {
                    return [
                        'success' => false,
                        'message' => 'Vui lòng cung cấp giỏ hàng hoặc danh sách sản phẩm để tạo đơn hàng',
                        'error_code' => 'CART_OR_ITEMS_REQUIRED'
                    ];
                } elseif ($hasCartId) {
                    return [
                        'success' => false,
                        'message' => 'Giỏ hàng trống. Vui lòng thêm sản phẩm vào giỏ hàng trước khi đặt hàng',
                        'error_code' => 'CART_EMPTY'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Giỏ hàng trống',
                        'error_code' => 'CART_EMPTY'
                    ];
                }
            }

            // Process cart items to add unit_price
            $items = [];
            foreach ($cart['items'] as $item) {
                // Calculate unit price from product or variant
                $unitPrice = 0;
                if (isset($item['variant']) && $item['variant']) {
                    $unitPrice = $item['variant']['sale_price'] ?? $item['variant']['price'] ?? 0;
                } elseif (isset($item['product']) && $item['product']) {
                    $unitPrice = $item['product']['sale_price'] ?? $item['product']['price'] ?? 0;
                }

                // Add unit_price to item
                $item['unit_price'] = $unitPrice;
                $items[] = $item;
            }

            $subtotal = $cart['subtotal'];
            $taxAmount = $cart['tax_amount'];
            $discountAmount = $cart['discount_amount'];

            // Clear cart after getting items
            $this->cartRepo->clearCart($cartHeaderId);
        }
        // Otherwise, use items directly from the request
        elseif (isset($data['items'])) {
            $items = $data['items'];

            // Calculate totals from items
            foreach ($items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
        }

        // Validate stock
        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            $variantId = $item['product_variant_id'] ?? null;
            $quantity = $item['quantity'];

            if ($variantId) {
                $variant = $this->variantRepo->find($variantId);
                if (!$variant || $variant['stock_quantity'] < $quantity) {
                    $productName = $item['product_name'] ?? 'N/A';
                    return [
                        'success' => false,
                        'message' => "Sản phẩm {$productName} không đủ hàng trong kho",
                        'error_code' => 'INSUFFICIENT_STOCK'
                    ];
                }
            } else {
                $product = $this->productRepo->find($productId);
                if (!$product || $product['stock_quantity'] < $quantity) {
                    $productName = $item['product_name'] ?? 'N/A';
                    return [
                        'success' => false,
                        'message' => "Sản phẩm {$productName} không đủ hàng trong kho",
                        'error_code' => 'INSUFFICIENT_STOCK'
                    ];
                }
            }
        }

        return DB::transaction(function () use ($data, $userId, $items, $subtotal, $taxAmount, $discountAmount, $addressInfo) {
            // Get next order ID to generate order number
            $nextOrderId = $this->getNextOrderId();
            $orderNumber = $this->generateOrderNumber($nextOrderId);

            // Create order
            $orderData = [
                'order_number' => $orderNumber,
                'user_id' => $userId,
                'customer_name' => $addressInfo['customer_name'],
                'customer_email' => $addressInfo['customer_email'],
                'customer_phone' => $addressInfo['customer_phone'],
                'shipping_address' => $addressInfo['shipping_address'],
                'billing_address' => $addressInfo['billing_address'],
                'currency' => 'VND',
                'notes' => $addressInfo['notes'],
                'payment_method' => $data['payment_method'],
                'shipping_method' => $data['shipping_method'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $this->calculateShipping($data['shipping_method']),
                'discount_amount' => $discountAmount,
                'total_amount' => $subtotal + $taxAmount + $this->calculateShipping($data['shipping_method']) - $discountAmount,
                'created_user_id' => $userId,
            ];

            $order = $this->repo->create($orderData);

            // Create order items
            foreach ($items as $item) {
                // Extract product information from cart item
                $productName = $item['product_name'] ?? null;
                $productSku = $item['product_sku'] ?? null;
                $variantName = $item['variant_name'] ?? null;

                // If product name is not set, try to get it from product or variant data
                if (!$productName && isset($item['product'])) {
                    $productName = $item['product']['name'] ?? null;
                }
                if (!$productName && isset($item['variant'])) {
                    $productName = $item['variant']['name'] ?? null;
                }

                // If product SKU is not set, try to get it from product or variant data
                if (!$productSku && isset($item['product'])) {
                    $productSku = $item['product']['sku'] ?? null;
                }
                if (!$productSku && isset($item['variant'])) {
                    $productSku = $item['variant']['sku'] ?? null;
                }

                // If variant name is not set, try to get it from variant data
                if (!$variantName && isset($item['variant'])) {
                    $variantName = $item['variant']['name'] ?? null;
                }

                $this->repo->addItem($order['id'], [
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'product_name' => $productName ?? $this->getProductName($item),
                    'product_sku' => $productSku ?? $this->getProductSku($item),
                    'variant_name' => $variantName ?? $this->getVariantName($item),
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'product_attributes' => $item['product_attributes'] ?? null,
                ]);
            }

            // Clear stored address information after creating order
            $this->clearStoredAddressInfo($userId);

            return [
                'success' => true,
                'data' => $this->repo->find($order['id']),
                'message' => $userId ? 'Tạo đơn hàng thành công' : 'Tạo đơn hàng khách vãng lai thành công'
            ];
        });
    }

    /**
     * Get product name from item data
     */
    private function getProductName(array $item): string
    {
        if (isset($item['product_name'])) {
            return $item['product_name'];
        }

        if (isset($item['product_id'])) {
            $product = $this->productRepo->find($item['product_id']);
            return $product['name'] ?? 'Unknown Product';
        }

        return 'Unknown Product';
    }

    /**
     * Get product SKU from item data
     */
    private function getProductSku(array $item): string
    {
        if (isset($item['product_sku'])) {
            return $item['product_sku'];
        }

        if (isset($item['product_variant_id'])) {
            $variant = $this->variantRepo->find($item['product_variant_id']);
            return $variant['sku'] ?? '';
        }

        if (isset($item['product_id'])) {
            $product = $this->productRepo->find($item['product_id']);
            return $product['sku'] ?? '';
        }

        return '';
    }

    /**
     * Get variant name from item data
     */
    private function getVariantName(array $item): ?string
    {
        if (isset($item['variant_name'])) {
            return $item['variant_name'];
        }

        if (isset($item['product_variant_id'])) {
            $variant = $this->variantRepo->find($item['product_variant_id']);
            return $variant['name'] ?? null;
        }

        return null;
    }

    /**
     * Create guest order
     */
    public function createGuestOrder(array $data): array
    {
        return $this->createOrder($data);
    }

    /**
     * Get user order
     */
    public function getUserOrder($orderId, $userId = null): array
    {
        // If userId is not provided, get it from Auth
        if ($userId === null) {
            $userId = Auth::id();
        }
        
        $order = $this->repo->findUserOrder($orderId, $userId);
        
        if (!$order) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng',
                'data' => null
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Lấy chi tiết đơn hàng thành công',
            'data' => $order
        ];
    }

    /**
     * Get guest order
     */
    public function getGuestOrder($orderNumber, $email): array
    {
        try {
            $order = $this->repo->findGuestOrder($orderNumber, $email);
            
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng',
                    'data' => null
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Lấy chi tiết đơn hàng thành công',
                'data' => $order
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy thông tin đơn hàng',
                'data' => null
            ];
        }
    }

    /**
     * Get order by number
     */
    public function getOrderByNumber($orderNumber): array
    {
        try {
            $order = $this->repo->findByOrderNumber($orderNumber);
            
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng',
                    'data' => null
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Lấy chi tiết đơn hàng thành công',
                'data' => $order
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy thông tin đơn hàng',
                'data' => null
            ];
        }
    }

    /**
     * Process payment
     */
    public function processPayment($orderId, array $paymentData, ?int $userId = null): array
    {
        // If userId is not provided, get it from Auth
        if ($userId === null) {
            $userId = Auth::id();
        }
        
        try {
            $result = DB::transaction(function () use ($orderId, $paymentData, $userId) {
                $order = $this->repo->find($orderId);

                if (!$order) {
                    return [
                        'success' => false,
                        'message' => 'Đơn hàng không tồn tại',
                        'data' => null
                    ];
                }

                if ($order['payment_status'] === 'paid') {
                    return [
                        'success' => false,
                        'message' => 'Đơn hàng đã được thanh toán',
                        'data' => null
                    ];
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

                $updatedOrder = $this->repo->find($orderId);
                
                return [
                    'success' => true,
                    'message' => 'Xử lý thanh toán thành công',
                    'data' => $updatedOrder
                ];
            });
            
            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xử lý thanh toán',
                'data' => null
            ];
        }
    }

    /**
     * Calculate shipping cost
     */
    private function calculateShipping($shippingMethod): float
    {
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
     * Get next order ID
     */
    private function getNextOrderId(): int
    {
        // Get the maximum ID from the orders table and add 1
        $maxId = DB::table('orders')->max('id') ?? 0;
        return $maxId + 1;
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
            } else {
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

    /**
     * Get user address information for checkout
     */
    public function getUserAddressInfo(?int $userId = null): array
    {
        // If userId is not provided, get it from Auth
        if ($userId === null) {
            $userId = Auth::id();
        }
        
        if (!$userId) {
            return [
                'success' => false,
                'message' => 'Người dùng chưa đăng nhập',
                'data' => null
            ];
        }
        
        $user = $this->repo->findUser($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng',
                'data' => null
            ];
        }

        // Get user profile if it exists
        $profile = $user['profile'] ?? null;

        // Prepare address information
        $addressInfo = [
            'customer_name' => $profile['name'] ?? $user['name'] ?? '',
            'customer_email' => $user['email'] ?? '',
            'customer_phone' => $user['phone'] ?? '',
            'shipping_address' => [
                'address' => '',
                'city' => '',
                'postal_code' => '',
                'country' => 'Việt Nam', // Default country
            ],
            'billing_address' => null,
        ];

        // Parse address from profile if available
        if ($profile && !empty($profile['address'])) {
            // Try to parse address string into components
            $addressString = $profile['address'];
            $addressInfo['shipping_address']['address'] = $addressString;
        }

        return [
            'success' => true,
            'message' => 'Lấy thông tin địa chỉ thành công',
            'data' => $addressInfo
        ];
    }

    /**
     * Store address information in session for checkout
     */
    public function storeAddressInfo(array $data, ?int $userId = null): array
    {
        // If userId is not provided, get it from Auth
        if ($userId === null) {
            $userId = Auth::id();
        }
        $addressKey = $userId ? "order_address_user_{$userId}" : 'order_address_guest';

        // Store address information in session
        session([$addressKey => [
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'shipping_address' => $data['shipping_address'],
            'billing_address' => $data['billing_address'] ?? $data['shipping_address'],
            'notes' => $data['notes'] ?? null,
        ]]);

        $message = $userId ? 'Cập nhật thông tin địa chỉ thành công' : 'Lưu thông tin địa chỉ thành công';
        
        return [
            'success' => true,
            'message' => $message
        ];
    }

    /**
     * Get stored address information from session
     */
    public function getStoredAddressInfo(?int $userId = null): ?array
    {
        // If userId is not provided, get it from Auth
        if ($userId === null) {
            $userId = Auth::id();
        }
        $addressKey = $userId ? "order_address_user_{$userId}" : 'order_address_guest';
        return session($addressKey);
    }

    /**
     * Clear stored address information from session
     */
    public function clearStoredAddressInfo(?int $userId = null): bool
    {
        // If userId is not provided, get it from Auth
        if ($userId === null) {
            $userId = Auth::id();
        }
        $addressKey = $userId ? "order_address_user_{$userId}" : 'order_address_guest';
        session()->forget($addressKey);
        return true;
    }
}
