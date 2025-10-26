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
    
    protected $storeRequestClass = CartStoreRequest::class;
    protected $updateRequestClass = CartUpdateRequest::class;
    
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
        
        $response = $this->apiResponse($result['success'], $result['data'], $result['message']);
        
        // Add cart ID to response headers for client to store
        $response->header('X-Cart-ID', $cartId);
        
        return $response;
    }

    /**
     * Add item to cart
     */
    public function store(): JsonResponse
    {
        try {
            $request = app($this->getStoreRequestClass());
            $cartId = $this->service->getCartId($request);
            $result = $this->service->create($request->validated());
            
            if ($result['success']) {
                $response = $this->successResponseWithFormat($result['data'], $result['message'], 201);
            } else {
                $response = $this->apiResponse(false, null, $result['message'], 500);
            }
            
            // Add cart ID to response headers for client to store
            $response->header('X-Cart-ID', $cartId);
            
            return $response;
        } catch (\Exception $e) {
            $this->logError('Store', $e);
            return $this->apiResponse(false, null, 'Không thể thêm sản phẩm vào giỏ hàng', 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update($id): JsonResponse
    {
        try {
            $request = app($this->getUpdateRequestClass());
            $result = $this->service->update($id, $request->validated());
            
            if ($result['success']) {
                return $this->successResponseWithFormat($result['data'], $result['message'], 200);
            } else {
                $statusCode = strpos($result['message'], 'Không tìm thấy') !== false ? 404 : 500;
                return $this->apiResponse(false, null, $result['message'], $statusCode);
            }
        } catch (\Exception $e) {
            $this->logError('Update', $e, ['id' => $id]);
            return $this->apiResponse(false, null, 'Không thể cập nhật sản phẩm trong giỏ hàng', 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy($id): JsonResponse
    {
        try {
            $result = $this->service->delete($id);
            
            if ($result['success']) {
                return $this->apiResponse(true, null, $result['message'], 200);
            } else {
                $statusCode = strpos($result['message'], 'Không tìm thấy') !== false ? 404 : 500;
                return $this->apiResponse(false, null, $result['message'], $statusCode);
            }
        } catch (\Exception $e) {
            $this->logError('Destroy', $e, ['id' => $id]);
            return $this->apiResponse(false, null, 'Không thể xóa sản phẩm khỏi giỏ hàng', 500);
        }
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
