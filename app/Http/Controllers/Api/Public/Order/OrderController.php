<?php

namespace App\Http\Controllers\Api\Public\Order;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Public\Order\UnifiedOrderRequest;
use App\Http\Requests\Public\Order\PaymentRequest;
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
     * Create a new order (for both authenticated users and guests)
     */
    public function createOrder(UnifiedOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $userId = auth('api')->id();

        // If user is authenticated, use their ID
        if ($userId) {
            // For authenticated users, we can pre-fill address from their profile
            // but allow them to modify it for this order only
            $order = $this->service->createOrder($data, $userId);
            $message = 'Tạo đơn hàng thành công';
        }
        // Otherwise, create a guest order
        else {
            $order = $this->service->createOrder($data);
            $message = 'Tạo đơn hàng khách vãng lai thành công';
        }

        return $this->successResponseWithFormat($order, $message, 201);
    }

    /**
     * Show order details (for authenticated users)
     */
    public function show($id, ?Request $request = null): JsonResponse
    {
        $userId = auth('api')->id();
        $order = $this->service->getUserOrder($id, $userId);

        if (!$order) {
            return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
        }

        return $this->successResponseWithFormat($order, 'Lấy chi tiết đơn hàng thành công');
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
        $userId = auth('api')->id();
        $result = $this->service->processPayment($id, $request->validated(), $userId);

        if (!$result) {
            return $this->apiResponse(false, null, 'Không thể xử lý thanh toán', 400);
        }

        return $this->successResponseWithFormat($result, 'Xử lý thanh toán thành công');
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
        $userId = auth('api')->id();

        if (!$userId) {
            return $this->apiResponse(false, null, 'Người dùng chưa đăng nhập', 401);
        }

        $addressInfo = $this->service->getUserAddressInfo($userId);

        return $this->successResponseWithFormat($addressInfo, 'Lấy thông tin địa chỉ thành công');
    }
}
