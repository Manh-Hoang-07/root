<?php
namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Order\OrderService;
use App\Http\Requests\Admin\Order\OrderRequest;

class OrderController extends BaseController
{
    /**
     * @var OrderService
     */
    protected $service;

    public function __construct(OrderService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = OrderRequest::class;
        $this->updateRequestClass = OrderRequest::class;
        
        // Tối ưu: Chỉ load relations cần thiết cho list
        $this->indexRelations = ['user:id,username,email'];
        
        // Load đầy đủ thông tin cho show
        $this->showRelations = [
            'user:id,username,email',
            'orderItems:id,order_id,product_id,variant_id,quantity,price,subtotal',
            'orderItems.product:id,name,sku,image',
            'orderItems.variant:id,name,sku,image',
            'orderStatusHistories:id,order_id,status,note,created_at',
            'orderStatusHistories.user:id,username',
            'payment:id,order_id,method,amount,status,transaction_id',
            'shippingInfo:id,order_id,address,city,state,country,postal_code,phone'
        ];
    }
} 
