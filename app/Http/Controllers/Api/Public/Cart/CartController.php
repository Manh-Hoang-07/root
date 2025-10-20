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
        $result = $this->service->getCart($cartId);
        return $this->apiResponse($result['success'], $result['data'], $result['message']);
    }

    /**
     * Add item to cart
     */
    public function store(): JsonResponse
    {
        $request = app(CartStoreRequest::class);
        $cartId = $this->service->getCartId($request);
        $result = $this->service->addItem($cartId, $request->validated());
        return $this->apiResponse($result['success'], $result['data'], $result['message']);
    }

    /**
     * Update cart item quantity
     */
    public function update($id): JsonResponse
    {
        $request = app(CartUpdateRequest::class);
        $cartId = $this->service->getCartId($request);
        $result = $this->service->updateItem($cartId, $id, $request->validated());
        return $this->apiResponse($result['success'], $result['data'], $result['message']);
    }

    /**
     * Remove item from cart
     */
    public function destroy($id): JsonResponse
    {
        $request = app(Request::class);
        $cartId = $this->service->getCartId($request);
        $result = $this->service->removeItem($cartId, $id);
        return $this->apiResponse($result['success'], $result['data'], $result['message']);
    }

    /**
     * Clear cart
     */
    public function clear(Request $request): JsonResponse
    {
        $cartId = $this->service->getCartId($request);
        $result = $this->service->clearCart($cartId);
        return $this->apiResponse($result['success'], $result['data'], $result['message']);
    }

    /**
     * Apply coupon code
     */
    public function applyCoupon(CartCouponRequest $request): JsonResponse
    {
        $cartId = $this->service->getCartId($request);
        $result = $this->service->applyCoupon($cartId, $request->validated()['code']);
        return $this->apiResponse($result['success'], $result['data'], $result['message']);
    }

    /**
     * Remove coupon code
     */
    public function removeCoupon(Request $request): JsonResponse
    {
        $cartId = $this->service->getCartId($request);
        $result = $this->service->removeCoupon($cartId);
        return $this->apiResponse($result['success'], $result['data'], $result['message']);
    }
}
