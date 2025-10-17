<?php

namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\Order\OrderStoreRequest;
use App\Http\Requests\Admin\Order\OrderUpdateRequest;
use App\Http\Requests\Admin\Order\StatusUpdateRequest;
use App\Http\Requests\Admin\Order\OrderItemStoreRequest;
use App\Http\Requests\Admin\Order\OrderItemUpdateRequest;
use App\Http\Requests\Admin\Order\BulkStatusRequest;
use App\Http\Requests\Admin\Order\ConfirmOrderRequest;
use App\Http\Requests\Admin\Order\CancelOrderRequest;
use App\Services\Admin\Order\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends CrudController
{
    protected OrderService $service;
    protected $statusUpdateRequestClass = StatusUpdateRequest::class;
    protected $storeRequestClass = OrderStoreRequest::class;
    protected $updateRequestClass = OrderUpdateRequest::class;
    protected $indexRelations = ['user:id,name,email', 'items.product:id,name,sku', 'items.variant:id,name,sku'];
    protected $showRelations = ['user:id,name,email,phone', 'items.product:id,name,sku,image', 'items.variant:id,name,sku', 'createdUser:id,name', 'updatedUser:id,name'];

    public function __construct(OrderService $service)
    {
        parent::__construct($service);
        $this->service = $service;
    }

    protected function getSearchFields(): array
    {
        return ['id', 'order_number', 'customer_name', 'customer_email'];
    }


    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'payment_status' => 'required|in:pending,paid,failed,refunded,partially_refunded'
            ]);

            $order = $this->service->getRepo()->updatePaymentStatus($id, $request->payment_status);
            if (!$order) {
                return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
            }

            return $this->apiResponse(true, $order, 'Cập nhật trạng thái thanh toán thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Update shipping status
     */
    public function updateShippingStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'shipping_status' => 'required|in:pending,preparing,shipped,delivered,returned',
                'tracking_number' => 'nullable|string|max:100'
            ]);

            $order = $this->service->getRepo()->updateShippingStatus($id, $request->shipping_status, $request->tracking_number);
            if (!$order) {
                return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
            }

            return $this->apiResponse(true, $order, 'Cập nhật trạng thái vận chuyển thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Add an item to order
     */
    public function addItem(OrderItemStoreRequest $request, $id): JsonResponse
    {
        $result = $this->service->addItem($id, $request->validated());
        if (!$result) return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
        return $this->apiResponse(true, $result, 'Thêm sản phẩm vào đơn hàng thành công');
    }

    /**
     * Update an order item
     */
    public function updateItem(OrderItemUpdateRequest $request, $orderId, $itemId): JsonResponse
    {
        $result = $this->service->updateItem($orderId, $itemId, $request->validated());
        if (!$result) return $this->apiResponse(false, null, 'Không tìm thấy dữ liệu', 404);
        return $this->apiResponse(true, $result, 'Cập nhật sản phẩm trong đơn hàng thành công');
    }

    /**
     * Remove an order item
     */
    public function removeItem($orderId, $itemId): JsonResponse
    {
        $result = $this->service->removeItem($orderId, $itemId);
        if (!$result) return $this->apiResponse(false, null, 'Không tìm thấy dữ liệu', 404);
        return $this->apiResponse(true, $result, 'Xóa sản phẩm khỏi đơn hàng thành công');
    }

    /**
     * Recalculate totals
     */
    public function recalculate($id): JsonResponse
    {
        $result = $this->service->recalculateTotals($id);
        if (!$result) return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
        return $this->apiResponse(true, $result, 'Tính lại tổng tiền thành công');
    }

    /**
     * Confirm order
     */
    public function confirm(ConfirmOrderRequest $request, $id): JsonResponse
    {
        $result = $this->service->confirmOrder($id, $request->validated()['note'] ?? null);
        if (!$result) return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
        return $this->apiResponse(true, $result, 'Xác nhận đơn hàng thành công');
    }

    /**
     * Cancel order
     */
    public function cancel(CancelOrderRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->cancelOrder($id, $data['reason']);
        if (!$result) return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
        return $this->apiResponse(true, $result, 'Hủy đơn hàng thành công');
    }

    /**
     * Bulk update status for orders
     */
    public function bulkUpdateStatus(BulkStatusRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $count = $this->service->bulkUpdateStatus($validated['ids'], $validated['status']);
        return $this->apiResponse(true, ['updated' => $count], 'Cập nhật trạng thái hàng loạt thành công');
    }

}
