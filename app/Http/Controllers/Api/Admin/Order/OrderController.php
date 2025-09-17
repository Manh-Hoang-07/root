<?php
namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Order\OrderService;
use App\Http\Requests\Admin\Order\OrderRequest;

class OrderController extends BaseController
{
    protected static $serviceClass = OrderService::class;
    protected $storeRequestClass = OrderRequest::class;
    protected $updateRequestClass = OrderRequest::class;
    
    // Tối ưu: Chỉ load relations cần thiết cho list
    protected $indexRelations = ['user:id,username,email'];
    
    // Load đầy đủ thông tin cho show
    protected $showRelations = [
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
