<?php

namespace App\Services\Public\Cart;

use App\Services\BaseService;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductVariantRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartService extends BaseService
{
    protected ProductRepository $productRepo;
    protected ProductVariantRepository $variantRepo;

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
            if ($request->user()) {
                // For authenticated users, use user_id
                $cartId = 'user_' . $request->user()->id;
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
    public function getCart(string $cartId): ?array
    {
        $cart = $this->repo->getCartWithItems($cartId);

        // If cart doesn't exist, create empty cart
        if (!$cart) {
            $cart = $this->repo->createEmptyCart($cartId);
        }

        return $cart;
    }

    /**
     * Add item to cart
     */
    public function addItem(string $cartId, array $itemData): ?array
    {
        $productId = $itemData['product_id'] ?? null;
        $variantId = $itemData['product_variant_id'] ?? null;
        $quantity = $itemData['quantity'];

        // Validate product/variant exists and is active
        if ($variantId) {
            $variant = $this->variantRepo->findActive($variantId);
            if (!$variant || $variant['product_id'] != $productId) {
                throw new \Exception('Biến thể sản phẩm không hợp lệ');
            }
            $price = $variant['sale_price'] ?? $variant['price'];
            $stock = $variant['stock_quantity'];
        } else {
            $product = $this->productRepo->findActive($productId);
            if (!$product) {
                throw new \Exception('Sản phẩm không tồn tại hoặc không hoạt động');
            }
            $price = $product['sale_price'] ?? $product['price'];
            $stock = $product['stock_quantity'];
        }

        if ($stock < $quantity) {
            throw new \Exception('Số lượng sản phẩm trong kho không đủ');
        }

        // Add item to cart
        $cartItem = $this->repo->addItem($cartId, [
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'quantity' => $quantity
        ]);

        return $this->recalculateCart($cartId);
    }

    /**
     * Update cart item quantity
     */
    public function updateItem(string $cartId, $itemId, array $data): ?array
    {
        $item = $this->repo->getCartItem($itemId);

        if (!$item) {
            throw new \Exception('Sản phẩm không tồn tại trong giỏ hàng');
        }

        $quantity = $data['quantity'];

        // Check stock
        if ($item['product_variant_id']) {
            $variant = $this->variantRepo->findActive($item['product_variant_id']);
            if (!$variant || $variant['stock_quantity'] < $quantity) {
                throw new \Exception('Số lượng sản phẩm trong kho không đủ');
            }
        } else {
            $product = $this->productRepo->findActive($item['product_id']);
            if (!$product || $product['stock_quantity'] < $quantity) {
                throw new \Exception('Số lượng sản phẩm trong kho không đủ');
            }
        }

        $this->repo->updateItem($itemId, ['quantity' => $quantity]);

        return $this->recalculateCart($cartId);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(string $cartId, $itemId): ?array
    {
        $item = $this->repo->getCartItem($itemId);

        if (!$item) {
            throw new \Exception('Sản phẩm không tồn tại trong giỏ hàng');
        }

        $this->repo->removeItem($itemId);

        return $this->recalculateCart($cartId);
    }

    /**
     * Clear cart
     */
    public function clearCart(string $cartId): ?array
    {
        $this->repo->clearCart($cartId);

        return $this->getCart($cartId);
    }

    /**
     * Apply coupon code
     */
    public function applyCoupon(string $cartId, string $couponCode): ?array
    {
        // TODO: Implement coupon logic
        // For now, just return cart without coupon
        return $this->recalculateCart($cartId);
    }

    /**
     * Remove coupon code
     */
    public function removeCoupon(string $cartId): ?array
    {
        return $this->recalculateCart($cartId);
    }

    /**
     * Recalculate cart totals
     */
    private function recalculateCart(string $cartId): ?array
    {
        return $this->repo->recalculateTotals($cartId);
    }
}
