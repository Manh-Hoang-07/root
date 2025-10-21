<?php

namespace App\Http\Controllers\Api\Public\Order;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Public\Order\UnifiedOrderRequest;
use App\Http\Requests\Public\Order\PaymentRequest;
use App\Http\Requests\Public\Order\UpdateAddressRequest;
use App\Http\Requests\Public\Order\CreateOrderRequest;
use App\Services\Public\Order\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends CrudController
{
    /**
     * @var OrderService
     */
    protected $service;

    public function __construct(OrderService $orderService)
    {
        parent::__construct($orderService);
    }

    /**
     * Update address information for checkout
     */
    public function updateAddress(UpdateAddressRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Store address information - service will handle user ID internally and return result
        $result = $this->service->storeAddressInfo($data);

        return $this->successResponseWithFormat(null, $result['message']);
    }

    /**
     * Create a new order with payment and shipping methods
     */
    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $userId = \Illuminate\Support\Facades\Auth::id();

        // If user is authenticated and no cart_header_id provided, get it automatically
        if ($userId && !isset($data['cart_header_id'])) {
            $cartService = app(\App\Services\Public\Cart\CartService::class);
            $cartHeaderId = $cartService->getCartId($request);
            $data['cart_header_id'] = $cartHeaderId;
        }

        // Create order with stored address information - service will handle user ID internally
        $result = $this->service->createOrder($data);

        if ($result['success']) {
            return $this->successResponseWithFormat($result['data'], $result['message'], 201);
        } else {
            return $this->apiResponse(false, null, $result['message'], 400, ['error_code' => $result['error_code']]);
        }
    }

    /**
     * Create a new order (legacy method for backward compatibility)
     */
    public function createUnifiedOrder(UnifiedOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Store address information first - service will handle user ID internally
        $addressData = [
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'shipping_address' => $data['shipping_address'],
            'billing_address' => $data['billing_address'] ?? $data['shipping_address'],
            'notes' => $data['notes'] ?? null,
        ];
        $this->service->storeAddressInfo($addressData);

        $userId = \Illuminate\Support\Facades\Auth::id();
        // If user is authenticated and no cart_header_id provided, get it automatically
        if ($userId && !isset($data['cart_header_id'])) {
            $cartService = app(\App\Services\Public\Cart\CartService::class);
            $cartHeaderId = $cartService->getCartId($request);
            $data['cart_header_id'] = $cartHeaderId;
        }

        // Create order - service will handle user ID internally
        $result = $this->service->createOrder($data);

        if ($result['success']) {
            return $this->successResponseWithFormat($result['data'], $result['message'], 201);
        } else {
            return $this->apiResponse(false, null, $result['message'], 400, ['error_code' => $result['error_code']]);
        }
    }

    /**
     * Show order details (for authenticated users)
     */
    public function show($id, ?Request $request = null): JsonResponse
    {
        // Get user order - service will handle user ID internally and return result
        $result = $this->service->getUserOrder($id);

        if (!$result['success']) {
            return $this->apiResponse(false, null, $result['message'], 404);
        }

        return $this->successResponseWithFormat($result['data'], $result['message']);
    }

    /**
     * Show order details for guest users
     */
    public function guestShow($orderNumber, $email): JsonResponse
    {
        $order = $this->service->getGuestOrder($orderNumber, $email);

        if (!$order) {
            return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
        }

        return $this->successResponseWithFormat($order, 'Lấy chi tiết đơn hàng thành công');
    }

    /**
     * Process payment for order
     */
    public function processPayment(PaymentRequest $request, $id): JsonResponse
    {
        // Process payment - service will handle user ID internally and return result
        $result = $this->service->processPayment($id, $request->validated());

        if (!$result['success']) {
            return $this->apiResponse(false, null, $result['message'], 400);
        }

        return $this->successResponseWithFormat($result['data'], $result['message']);
    }

    /**
     * Get order status
     */
    public function getStatus($orderNumber): JsonResponse
    {
        $order = $this->service->getOrderByNumber($orderNumber);

        if (!$order) {
            return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
        }

        return $this->successResponseWithFormat([
            'order_number' => $order['order_number'],
            'status' => $order['status'],
            'payment_status' => $order['payment_status'],
            'shipping_status' => $order['shipping_status']
        ], 'Lấy trạng thái đơn hàng thành công');
    }

    /**
     * Get user address information for checkout
     */
    public function getUserAddress(): JsonResponse
    {
        // Get user address information - service will handle user ID internally and return result
        $result = $this->service->getUserAddressInfo();

        if (!$result['success']) {
            return $this->apiResponse(false, null, $result['message'], 401);
        }

        return $this->successResponseWithFormat($result['data'], $result['message']);
    }
}
