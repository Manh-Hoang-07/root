<?php
namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Api\BaseController;
use App\Services\Order\OrderService;
use App\Http\Resources\Admin\Order\OrderResource;
use App\Http\Requests\Admin\Order\OrderRequest;

class OrderController extends BaseController
{
    public function __construct(OrderService $service)
    {
        parent::__construct($service, OrderResource::class);
        $this->storeRequestClass = OrderRequest::class;
        $this->updateRequestClass = OrderRequest::class;
        
        // Tối ưu: Chỉ load relations cần thiết cho list
        $this->indexRelations = ['user:id,name,email'];
        
        // Load đầy đủ thông tin cho show
        $this->showRelations = [
            'user:id,name,email',
            'orderItems:id,order_id,product_id,quantity,price',
            'orderItems.product:id,name,sku'
        ];
    }

    /**
     * Override getDefaultListFields cho Order
     */
    protected function getDefaultListFields()
    {
        return ['id', 'order_number', 'user_id', 'total_amount', 'status', 'created_at'];
    }
} 
