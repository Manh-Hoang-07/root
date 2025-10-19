<?php

namespace App\Http\Controllers\Api\Public\Cart;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Public\Cart\CartStoreRequest;
use App\Http\Requests\Public\Cart\CartUpdateRequest;
use App\Http\Requests\Public\Cart\CartCouponRequest;
use App\Services\Public\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends CrudController
{
    /**
     * @var CartService
     */
    protected $service;
    public function __construct(CartService $cartService)
    {
        parent::__construct($cartService);
    }

    /**
     * Get cart items
     */
    public function index(Request $request): JsonResponse
    {
        $cartId = $this->service->getCartId($request);
        $cart = $this->service->getCart($cartId);
        return $this->successResponseWithFormat($cart, 'Lấy giỏ hàng thành công');
    }

    /**
     * Add item to cart
     */
    public function store(): JsonResponse
    {
        $request = app(CartStoreRequest::class);
        $cartId = $this->service->getCartId($request);
        $cart = $this->service->addItem($cartId, $request->validated());
        return $this->successResponseWithFormat($cart, 'Thêm sản phẩm vào giỏ hàng thành công');
    }

    /**
     * Update cart item quantity
     */
    public function update($id): JsonResponse
    {
        $request = app(CartUpdateRequest::class);
        $cartId = $this->service->getCartId($request);
        $cart = $this->service->updateItem($cartId, $id, $request->validated());
        return $this->successResponseWithFormat($cart, 'Cập nhật giỏ hàng thành công');
    }

    /**
     * Remove item from cart
     */
    public function destroy($id): JsonResponse
    {
        $request = app(Request::class);
        $cartId = $this->service->getCartId($request);
        $cart = $this->service->removeItem($cartId, $id);
        return $this->successResponseWithFormat($cart, 'Xóa sản phẩm khỏi giỏ hàng thành công');
    }

    /**
     * Clear cart
     */
    public function clear(Request $request): JsonResponse
    {
        $cartId = $this->service->getCartId($request);
        $cart = $this->service->clearCart($cartId);
        return $this->successResponseWithFormat($cart, 'Xóa giỏ hàng thành công');
    }

    /**
     * Apply coupon code
     */
    public function applyCoupon(CartCouponRequest $request): JsonResponse
    {
        $cartId = $this->service->getCartId($request);
        $cart = $this->service->applyCoupon($cartId, $request->validated()['code']);
        return $this->successResponseWithFormat($cart, 'Áp dụng mã giảm giá thành công');
    }

    /**
     * Remove coupon code
     */
    public function removeCoupon(Request $request): JsonResponse
    {
        $cartId = $this->service->getCartId($request);
        $cart = $this->service->removeCoupon($cartId);
        return $this->successResponseWithFormat($cart, 'Xóa mã giảm giá thành công');
    }
}
