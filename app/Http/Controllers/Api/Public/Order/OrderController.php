<?php

namespace App\Http\Controllers\Api\Public\Order;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Public\Order\OrderStoreRequest;
use App\Http\Requests\Public\Order\GuestOrderRequest;
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
    protected $storeRequestClass = OrderStoreRequest::class;

    public function __construct(OrderService $orderService)
    {
        parent::__construct($orderService);
    }

    /**
     * Create a new order (for authenticated users)
     */
    public function store(): JsonResponse
    {
        try {
            $request = app($this->getStoreRequestClass());
            $userId = auth('api')->id();
            $order = $this->service->createOrder($request->validated(), $userId);
            return $this->successResponseWithFormat($order, 'Tạo đơn hàng thành công', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Let the framework return 422 with validation errors
        } catch (\Exception $e) {
            $this->logError('Create Order', $e);
            return $this->apiResponse(false, null, 'Không thể tạo đơn hàng', 500);
        }
    }

    /**
     * Guest checkout - Create order without authentication
     */
    public function guestCheckout(GuestOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->service->createGuestOrder($request->validated());
            return $this->successResponseWithFormat($order, 'Tạo đơn hàng khách vãng lai thành công', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Let the framework return 422 with validation errors
        } catch (\Exception $e) {
            $this->logError('Guest Checkout', $e);
            return $this->apiResponse(false, null, 'Không thể tạo đơn hàng khách vãng lai', 500);
        }
    }

    /**
     * Show order details (for authenticated users)
     */
    public function show($id, ?Request $request = null): JsonResponse
    {
        try {
            $userId = auth('api')->id();
            $order = $this->service->getUserOrder($id, $userId);

            if (!$order) {
                return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
            }

            return $this->successResponseWithFormat($order, 'Lấy chi tiết đơn hàng thành công');
        } catch (\Exception $e) {
            $this->logError('Show Order', $e, ['order_id' => $id]);
            return $this->apiResponse(false, null, 'Không thể tải thông tin đơn hàng', 500);
        }
    }

    /**
     * Show order details for guest users
     */
    public function guestShow($orderNumber, $email): JsonResponse
    {
        try {
            $order = $this->service->getGuestOrder($orderNumber, $email);

            if (!$order) {
                return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
            }

            return $this->successResponseWithFormat($order, 'Lấy chi tiết đơn hàng thành công');
        } catch (\Exception $e) {
            $this->logError('Guest Show Order', $e, ['order_number' => $orderNumber]);
            return $this->apiResponse(false, null, 'Không thể tải thông tin đơn hàng', 500);
        }
    }

    /**
     * Process payment for order
     */
    public function processPayment(PaymentRequest $request, $id): JsonResponse
    {
        try {
            $userId = auth('api')->id();
            $result = $this->service->processPayment($id, $request->validated(), $userId);

            if (!$result) {
                return $this->apiResponse(false, null, 'Không thể xử lý thanh toán', 400);
            }

            return $this->successResponseWithFormat($result, 'Xử lý thanh toán thành công');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Let the framework return 422 with validation errors
        } catch (\Exception $e) {
            $this->logError('Process Payment', $e, ['order_id' => $id]);
            return $this->apiResponse(false, null, 'Không thể xử lý thanh toán', 500);
        }
    }

    /**
     * Get order status
     */
    public function getStatus($orderNumber): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            $this->logError('Get Order Status', $e, ['order_number' => $orderNumber]);
            return $this->apiResponse(false, null, 'Không thể tải trạng thái đơn hàng', 500);
        }
    }
}
