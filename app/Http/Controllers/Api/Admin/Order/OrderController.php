<?php

namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Order\OrderService;
use App\Http\Requests\Admin\Order\StatusUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends BaseController
{
    protected $statusUpdateRequestClass = StatusUpdateRequest::class;
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



}
