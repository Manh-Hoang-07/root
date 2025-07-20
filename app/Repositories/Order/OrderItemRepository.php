<?php
namespace App\Repositories\Order;

use App\Models\OrderItem;
use App\Repositories\BaseRepository;

class OrderItemRepository extends BaseRepository
{
    public function model()
    {
        return OrderItem::class;
    }
} 