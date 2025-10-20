<?php

namespace App\Services\Public\Cart;

use App\Services\BaseService;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductVariantRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CartService extends BaseService
{
    protected ProductRepository $productRepo;
    protected ProductVariantRepository $variantRepo;

    /**
     * @var CartRepository
     */
    protected $repo;
    public function __construct(
        CartRepository $repo,
        ProductRepository $productRepo,
        ProductVariantRepository $variantRepo
    ) {
        parent::__construct($repo);
        $this->productRepo = $productRepo;
        $this->variantRepo = $variantRepo;
    }

    /**
     * Get or create cart ID based on request
     */
    public function getCartId(Request $request): string
    {
        // Try to get cart ID from header first
        $cartId = $request->header('X-Cart-ID');

        // Try to get from cookie
        if (!$cartId) {
            $cartId = $request->cookie('cart_id');
        }

        // Try to get from session (if available)
        if (!$cartId && $request->hasSession()) {
            $cartId = $request->session()->get('cart_id');
        }

        // If no cart ID found, create one
        if (!$cartId) {
            // Check if user is authenticated (middleware global đã xử lý)
            if (Auth::check()) {
                // For authenticated users, use user_id
                $cartId = 'user_' . Auth::id();
            } else {
                // For non-authenticated users, use browser session ID
                if ($request->hasSession()) {
                    $sessionId = $request->session()->getId();
                    $cartId = 'session_' . $sessionId;
                } else {
                    // Fallback to random string if no session available
                    $cartId = 'cart_' . Str::random(20);
                }
            }

            // Store in session if available
            if ($request->hasSession()) {
                $request->session()->put('cart_id', $cartId);
            }
        }

        return $cartId;
    }

    /**
     * Get cart with items
     */
    public function getCart(string $cartId): array
    {
        try {
            $cart = $this->repo->getCartWithItems($cartId);

            // If cart doesn't exist, create empty cart
            if (!$cart) {
                $cart = $this->repo->createEmptyCart($cartId);
            }

            return [
                'success' => true,
                'message' => 'Lấy giỏ hàng thành công',
                'data' => $cart
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy giỏ hàng',
                'data' => null
            ];
        }
    }

    /**
     * Add item to cart
     */
    public function addItem(string $cartId, array $itemData): array
    {
        try {
            $productId = $itemData['product_id'] ?? null;
            $variantId = $itemData['product_variant_id'] ?? null;
            $quantity = $itemData['quantity'];

            // Validate product/variant exists and is active
            if ($variantId) {
                $variant = $this->variantRepo->findActive($variantId);
                if (!$variant || $variant['product_id'] != $productId) {
                    return [
                        'success' => false,
                        'message' => 'Biến thể sản phẩm không hợp lệ',
                        'data' => null
                    ];
                }
                $price = $variant['sale_price'] ?? $variant['price'];
                $stock = $variant['stock_quantity'];
            } else {
                $product = $this->productRepo->findActive($productId);
                if (!$product) {
                    return [
                        'success' => false,
                        'message' => 'Sản phẩm không tồn tại hoặc không hoạt động',
                        'data' => null
                    ];
                }
                $price = $product['sale_price'] ?? $product['price'];
                $stock = $product['stock_quantity'];
            }

            if ($stock < $quantity) {
                return [
                    'success' => false,
                    'message' => 'Số lượng sản phẩm trong kho không đủ',
                    'data' => null
                ];
            }

            // Add item to cart
            $cartItem = $this->repo->addItem($cartId, [
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity
            ]);

            $cart = $this->recalculateCart($cartId);
            
            return [
                'success' => true,
                'message' => 'Thêm sản phẩm vào giỏ hàng thành công',
                'data' => $cart['data'] ?? null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể thêm sản phẩm vào giỏ hàng',
                'data' => null
            ];
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateItem(string $cartId, $itemId, array $data): array
    {
        try {
            $item = $this->repo->getCartItem($itemId);

            if (!$item) {
                return [
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại trong giỏ hàng',
                    'data' => null
                ];
            }

            $quantity = $data['quantity'];

            // Check stock
            if ($item['product_variant_id']) {
                $variant = $this->variantRepo->findActive($item['product_variant_id']);
                if (!$variant || $variant['stock_quantity'] < $quantity) {
                    return [
                        'success' => false,
                        'message' => 'Số lượng sản phẩm trong kho không đủ',
                        'data' => null
                    ];
                }
            } else {
                $product = $this->productRepo->findActive($item['product_id']);
                if (!$product || $product['stock_quantity'] < $quantity) {
                    return [
                        'success' => false,
                        'message' => 'Số lượng sản phẩm trong kho không đủ',
                        'data' => null
                    ];
                }
            }

            $this->repo->updateItem($itemId, ['quantity' => $quantity]);

            $cart = $this->recalculateCart($cartId);
            
            return [
                'success' => true,
                'message' => 'Cập nhật số lượng sản phẩm thành công',
                'data' => $cart['data'] ?? null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật số lượng sản phẩm',
                'data' => null
            ];
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem(string $cartId, $itemId): array
    {
        try {
            $item = $this->repo->getCartItem($itemId);

            if (!$item) {
                return [
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại trong giỏ hàng',
                    'data' => null
                ];
            }

            $this->repo->removeItem($itemId);

            $cart = $this->recalculateCart($cartId);
            
            return [
                'success' => true,
                'message' => 'Xóa sản phẩm khỏi giỏ hàng thành công',
                'data' => $cart['data'] ?? null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa sản phẩm khỏi giỏ hàng',
                'data' => null
            ];
        }
    }

    /**
     * Clear cart
     */
    public function clearCart(string $cartId): array
    {
        try {
            $this->repo->clearCart($cartId);

            $cart = $this->getCart($cartId);
            
            return [
                'success' => true,
                'message' => 'Xóa giỏ hàng thành công',
                'data' => $cart['data'] ?? null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa giỏ hàng',
                'data' => null
            ];
        }
    }

    /**
     * Apply coupon code
     */
    public function applyCoupon(string $cartId, string $couponCode): array
    {
        try {
            // TODO: Implement coupon logic
            // For now, just return cart without coupon
            $cart = $this->recalculateCart($cartId);
            
            return [
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công',
                'data' => $cart['data'] ?? null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể áp dụng mã giảm giá',
                'data' => null
            ];
        }
    }

    /**
     * Remove coupon code
     */
    public function removeCoupon(string $cartId): array
    {
        try {
            $cart = $this->recalculateCart($cartId);
            
            return [
                'success' => true,
                'message' => 'Xóa mã giảm giá thành công',
                'data' => $cart['data'] ?? null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa mã giảm giá',
                'data' => null
            ];
        }
    }

    /**
     * Recalculate cart totals
     */
    private function recalculateCart(string $cartId): array
    {
        try {
            $cart = $this->repo->recalculateTotals($cartId);
            
            return [
                'success' => true,
                'message' => 'Tính toán lại giỏ hàng thành công',
                'data' => $cart
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể tính toán lại giỏ hàng',
                'data' => null
            ];
        }
    }
}
