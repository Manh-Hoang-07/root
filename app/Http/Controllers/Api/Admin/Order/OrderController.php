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

            $result = $this->service->updatePaymentStatus($id, $request->payment_status);
            
            if ($result['success']) {
                return $this->apiResponse(true, $result['data'], $result['message']);
            } else {
                $statusCode = strpos($result['message'], 'Không tìm thấy') !== false ? 404 : 500;
                return $this->apiResponse(false, null, $result['message'], $statusCode);
            }
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

            $result = $this->service->updateShippingStatus($id, $request->shipping_status, $request->tracking_number);
            
            if ($result['success']) {
                return $this->apiResponse(true, $result['data'], $result['message']);
            } else {
                $statusCode = strpos($result['message'], 'Không tìm thấy') !== false ? 404 : 500;
                return $this->apiResponse(false, null, $result['message'], $statusCode);
            }
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
        
        if ($result['success']) {
            return $this->apiResponse(true, $result['data'], $result['message']);
        } else {
            $statusCode = strpos($result['message'], 'Không tìm thấy') !== false ? 404 : 500;
            return $this->apiResponse(false, null, $result['message'], $statusCode);
        }
    }

    /**
     * Update an order item
     */
    public function updateItem(OrderItemUpdateRequest $request, $orderId, $itemId): JsonResponse
    {
        $result = $this->service->updateItem($orderId, $itemId, $request->validated());
        
        if ($result['success']) {
            return $this->apiResponse(true, $result['data'], $result['message']);
        } else {
            $statusCode = strpos($result['message'], 'Không tìm thấy') !== false ? 404 : 500;
            return $this->apiResponse(false, null, $result['message'], $statusCode);
        }
    }

    /**
     * Remove an order item
     */
    public function removeItem($orderId, $itemId): JsonResponse
    {
        $result = $this->service->removeItem($orderId, $itemId);
        
        if ($result['success']) {
            return $this->apiResponse(true, $result['data'], $result['message']);
        } else {
            $statusCode = strpos($result['message'], 'Không tìm thấy') !== false ? 404 : 500;
            return $this->apiResponse(false, null, $result['message'], $statusCode);
        }
    }

    /**
     * Recalculate totals
     */
    public function recalculate($id): JsonResponse
    {
        $result = $this->service->recalculateTotals($id);
        
        if ($result['success']) {
            return $this->apiResponse(true, $result['data'], $result['message']);
        } else {
            $statusCode = strpos($result['message'], 'Không tìm thấy') !== false ? 404 : 500;
            return $this->apiResponse(false, null, $result['message'], $statusCode);
        }
    }

    /**
     * Confirm order
     */
    public function confirm(ConfirmOrderRequest $request, $id): JsonResponse
    {
        $result = $this->service->confirmOrder($id, $request->validated()['note'] ?? null);
        
        if ($result['success']) {
            return $this->apiResponse(true, $result['data'], $result['message']);
        } else {
            $statusCode = strpos($result['message'], 'Không tìm thấy') !== false ? 404 : 500;
            return $this->apiResponse(false, null, $result['message'], $statusCode);
        }
    }

    /**
     * Cancel order
     */
    public function cancel(CancelOrderRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->cancelOrder($id, $data['reason']);
        
        if ($result['success']) {
            return $this->apiResponse(true, $result['data'], $result['message']);
        } else {
            $statusCode = strpos($result['message'], 'Không tìm thấy') !== false ? 404 : 500;
            return $this->apiResponse(false, null, $result['message'], $statusCode);
        }
    }

    /**
     * Bulk update status for orders
     */
    public function bulkUpdateStatus(BulkStatusRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->service->bulkUpdateStatus($validated['ids'], $validated['status']);
        
        if ($result['success']) {
            return $this->apiResponse(true, $result['data'], $result['message']);
        } else {
            return $this->apiResponse(false, null, $result['message'], 500);
        }
    }

}
