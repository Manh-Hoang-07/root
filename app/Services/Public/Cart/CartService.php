<?php

namespace App\Services\Public\Cart;

use App\Services\BaseService;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductVariantRepository;
use App\Models\CartHeader;
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
     * Get or create cart header ID based on request
     */
    public function getCartId(Request $request): string
    {
        // Try to get cart header ID from header first
        $cartHeaderId = $request->header('X-Cart-ID');

        // Try to get from cookie
        if (!$cartHeaderId) {
            $cartHeaderId = $request->cookie('cart_header_id');
        }

        // Try to get from session (if available)
        if (!$cartHeaderId && $request->hasSession()) {
            $cartHeaderId = $request->session()->get('cart_header_id');
        }

        // If no cart header ID found, generate one
        if (!$cartHeaderId) {
            // For API requests, we need to ensure we have a session
            if ($request->hasSession()) {
                $sessionId = $request->session()->getId();
                $cartHeaderId = 'session_' . $sessionId;
                
                // Store in session
                $request->session()->put('cart_header_id', $cartHeaderId);
            } else {
                // For API without session, generate a unique ID and store in cookie
                $cartHeaderId = 'guest_' . Str::random(20);
                
                // Set cookie for future requests (1 year expiry)
                cookie()->queue('cart_header_id', $cartHeaderId, 525600);
            }
        }

        // First try to find an existing cart header
        $cartHeader = CartHeader::find($cartHeaderId);
        
        // Get session ID if available
        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        
        // If not found, create a new one
        if (!$cartHeader) {
            try {
                $cartHeader = CartHeader::create([
                    'id' => $cartHeaderId,
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'session_id' => $sessionId, // Always save session ID if available
                    'currency' => 'VND',
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'shipping_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => 0,
                ]);
            } catch (\Exception $e) {
                // If creation fails (likely due to duplicate ID), try to find it again
                $cartHeader = CartHeader::find($cartHeaderId);
                if (!$cartHeader) {
                    throw $e;
                }
            }
        } else {
            // Update the cart header with current user and session info
            $updateData = [];
            
            // If the user logs in later, attach user_id to existing guest cart
            if (Auth::check() && is_null($cartHeader->user_id)) {
                $updateData['user_id'] = Auth::id();
            }
            
            // Always update session_id if it's different
            if ($sessionId && $cartHeader->session_id !== $sessionId) {
                $updateData['session_id'] = $sessionId;
            }
            
            if (!empty($updateData)) {
                $cartHeader->update($updateData);
            }
        }

        return $cartHeaderId;
    }

    /**
     * Get cart with items
     */
    public function getCart(string $cartHeaderId): array
    {
        try {
            $cart = $this->repo->getCartWithItems($cartHeaderId);

            // If cart doesn't exist, create empty cart
            if (!$cart) {
                $cart = $this->repo->createEmptyCart($cartHeaderId);
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
    public function addItem(string $cartHeaderId, array $itemData): array
    {
        try {
            $productId = $itemData['product_id'] ?? null;
            $variantId = $itemData['product_variant_id'] ?? null;
            $quantity = $itemData['quantity'];

            // Validate variant exists and is active (variant is required)
            if (!$variantId) {
                return [
                    'success' => false,
                    'message' => 'Biến thể sản phẩm là bắt buộc',
                    'data' => null
                ];
            }

            $variant = $this->variantRepo->findActive($variantId);
            if (!$variant || $variant['product_id'] != $productId) {
                return [
                    'success' => false,
                    'message' => 'Biến thể sản phẩm không hợp lệ',
                    'data' => null
                ];
            }

            if ($variant['stock_quantity'] < $quantity) {
                return [
                    'success' => false,
                    'message' => 'Số lượng sản phẩm trong kho không đủ',
                    'data' => null
                ];
            }

            // Add item to cart
            $cartItem = $this->repo->addItem($cartHeaderId, [
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity
            ]);

            $cart = $this->recalculateCart($cartHeaderId);
            
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
    public function updateItem(string $cartHeaderId, $itemId, array $data): array
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

            // Check stock (variant is required)
            if (!$item['product_variant_id']) {
                return [
                    'success' => false,
                    'message' => 'Biến thể sản phẩm là bắt buộc',
                    'data' => null
                ];
            }

            $variant = $this->variantRepo->findActive($item['product_variant_id']);
            if (!$variant || $variant['stock_quantity'] < $quantity) {
                return [
                    'success' => false,
                    'message' => 'Số lượng sản phẩm trong kho không đủ',
                    'data' => null
                ];
            }

            $this->repo->updateItem($itemId, ['quantity' => $quantity]);

            $cart = $this->recalculateCart($cartHeaderId);
            
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
    public function removeItem(string $cartHeaderId, $itemId): array
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

            $cart = $this->recalculateCart($cartHeaderId);
            
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
    public function clearCart(string $cartHeaderId): array
    {
        try {
            $this->repo->clearCart($cartHeaderId);

            $cart = $this->getCart($cartHeaderId);
            
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
    public function applyCoupon(string $cartHeaderId, string $couponCode): array
    {
        try {
            // TODO: Implement coupon logic
            // For now, just return cart without coupon
            $cart = $this->recalculateCart($cartHeaderId);
            
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
    public function removeCoupon(string $cartHeaderId): array
    {
        try {
            $cart = $this->recalculateCart($cartHeaderId);
            
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
    private function recalculateCart(string $cartHeaderId): array
    {
        try {
            $cart = $this->repo->recalculateTotals($cartHeaderId);
            
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

    /**
     * Create method for CrudController compatibility
     */
    public function create($data): array
    {
        try {
            $request = request();
            $cartId = $this->getCartId($request);
            $result = $this->addItem($cartId, $data);
            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể thêm sản phẩm vào giỏ hàng',
                'data' => null
            ];
        }
    }

    /**
     * Update method for CrudController compatibility
     */
    public function update($id, $data): array
    {
        try {
            $request = request();
            $cartId = $this->getCartId($request);
            $result = $this->updateItem($cartId, $id, $data);
            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật sản phẩm trong giỏ hàng',
                'data' => null
            ];
        }
    }

    /**
     * Delete method for CrudController compatibility
     */
    public function delete($id): array
    {
        try {
            $request = request();
            $cartId = $this->getCartId($request);
            $result = $this->removeItem($cartId, $id);
            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa sản phẩm khỏi giỏ hàng',
                'data' => null
            ];
        }
    }
}
