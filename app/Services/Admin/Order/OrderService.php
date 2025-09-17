<?php
namespace App\Services\Admin\Order;

use App\Repositories\Order\OrderRepository;
use App\Services\BaseService;

class OrderService extends BaseService
{
    public function __construct(OrderRepository $repo)
    {
        parent::__construct($repo);
    }
    
    /**
     * Tìm đơn hàng theo ID với đầy đủ thông tin liên quan
     */
    public function find($id, $relations = [], $fields = ['*']): ?array
    {
        if (empty($relations)) {
            $relations = [
                'user:id,username,email',
                'orderItems:id,order_id,product_id,variant_id,quantity,price,subtotal',
                'orderItems.product:id,name,sku,image',
                'orderItems.variant:id,name,sku,image',
                'orderStatusHistories:id,order_id,status,note,created_at',
                'orderStatusHistories.user:id,username',
                'payment:id,order_id,method,amount,status,transaction_id',
                'shippingInfo:id,order_id,address,city,state,country,postal_code,phone',
            ];
        }
        
        return parent::find($id, $relations, $fields);
    }
    
    // ... logic đặc thù cho đơn hàng ...
} 