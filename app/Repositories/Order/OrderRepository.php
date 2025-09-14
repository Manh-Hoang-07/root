<?php
namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\BaseRepository;

class OrderRepository extends BaseRepository
{
    public function model(): string
    {
        return Order::class;
    }

    /**
     * Optimize relations for Order model
     */
    protected function optimizeRelations(array $relations): array
    {
        $optimizedRelations = [];
        foreach ($relations as $relation) {
            if (strpos($relation, ':') !== false) {
                // Nếu đã có select fields thì giữ nguyên
                $optimizedRelations[] = $relation;
            } else {
                // Tối ưu cho các relation của Order
                switch ($relation) {
                    case 'user':
                        $optimizedRelations[] = 'user:id,username,email';
                        break;
                    case 'orderItems':
                        $optimizedRelations[] = 'orderItems:id,order_id,product_id,quantity,price';
                        break;
                    case 'orderItems.product':
                        $optimizedRelations[] = 'orderItems.product:id,name,image';
                        break;
                    default:
                        $optimizedRelations[] = $relation;
                }
            }
        }
        return $optimizedRelations;
    }
} 