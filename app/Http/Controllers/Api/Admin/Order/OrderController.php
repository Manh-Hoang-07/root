<?php

namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends BaseController
{
    protected $indexRelations = ['user:id,name,email', 'items.product:id,name,sku', 'items.variant:id,name,sku'];
    protected $showRelations = ['user:id,name,email,phone', 'items.product:id,name,sku,image', 'items.variant:id,name,sku', 'createdUser:id,name', 'updatedUser:id,name'];

    public function __construct(OrderService $service)
    {
        parent::__construct($service);
    }

    protected function getSearchFields(): array
    {
        return ['id', 'order_number', 'customer_name', 'customer_email'];
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
            ]);

            $order = $this->service->getRepo()->updateStatus($id, $request->status);
            if (!$order) {
                return $this->apiResponse(false, null, 'Không tìm thấy đơn hàng', 404);
            }
            
            return $this->apiResponse(true, $order, 'Cập nhật trạng thái đơn hàng thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
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
     * Get orders by status
     */
    public function byStatus(Request $request, $status): JsonResponse
    {
        try {
            $filters = array_merge($request->all(), ['status' => $status]);
            $perPage = min($request->get('per_page', 20), 100);
            $orders = $this->service->list($filters, $perPage, $this->indexRelations);
            
            return $this->apiResponse(true, $orders, 'Lấy danh sách đơn hàng theo trạng thái thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get orders by payment status
     */
    public function byPaymentStatus(Request $request, $status): JsonResponse
    {
        try {
            $filters = array_merge($request->all(), ['payment_status' => $status]);
            $perPage = min($request->get('per_page', 20), 100);
            $orders = $this->service->list($filters, $perPage, $this->indexRelations);
            
            return $this->apiResponse(true, $orders, 'Lấy danh sách đơn hàng theo trạng thái thanh toán thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get orders by shipping status
     */
    public function byShippingStatus(Request $request, $status): JsonResponse
    {
        try {
            $filters = array_merge($request->all(), ['shipping_status' => $status]);
            $perPage = min($request->get('per_page', 20), 100);
            $orders = $this->service->list($filters, $perPage, $this->indexRelations);
            
            return $this->apiResponse(true, $orders, 'Lấy danh sách đơn hàng theo trạng thái vận chuyển thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Bulk update orders
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:orders,id',
                'action' => 'required|in:confirm,process,ship,deliver,cancel,mark_paid,mark_failed',
                'value' => 'sometimes|string'
            ]);

            $result = $this->service->getRepo()->bulkUpdate($request->ids, $request->action, $request->value ?? null);
            return $this->apiResponse(true, $result, 'Cập nhật đơn hàng hàng loạt thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }
}
