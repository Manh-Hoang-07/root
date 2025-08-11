<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderStatusHistory;
use App\Models\Order;

class OrderStatusHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderStatusHistories = [
            // Order 1 - iPhone 15 Pro Max
            [
                'order_id' => 1,
                'status' => 'pending',
                'note' => 'Đơn hàng được tạo',
                'changed_at' => '2024-07-15 09:00:00',
                'changed_by' => 'system',
            ],
            [
                'order_id' => 1,
                'status' => 'confirmed',
                'note' => 'Đơn hàng đã được xác nhận',
                'changed_at' => '2024-07-15 09:30:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 1,
                'status' => 'shipping',
                'note' => 'Đang xử lý đơn hàng',
                'changed_at' => '2024-07-15 10:00:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 1,
                'status' => 'shipping',
                'note' => 'Đơn hàng đã được gửi',
                'changed_at' => '2024-07-15 10:30:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 1,
                'status' => 'delivered',
                'note' => 'Giao hàng thành công',
                'changed_at' => '2024-07-16 10:00:00',
                'changed_by' => 'system',
            ],

            // Order 2 - Samsung Galaxy S24 Ultra
            [
                'order_id' => 2,
                'status' => 'pending',
                'note' => 'Đơn hàng được tạo',
                'changed_at' => '2024-07-16 13:00:00',
                'changed_by' => 'system',
            ],
            [
                'order_id' => 2,
                'status' => 'confirmed',
                'note' => 'Đơn hàng đã được xác nhận',
                'changed_at' => '2024-07-16 13:30:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 2,
                'status' => 'shipping',
                'note' => 'Đang xử lý đơn hàng',
                'changed_at' => '2024-07-16 14:00:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 2,
                'status' => 'shipping',
                'note' => 'Đơn hàng đã được gửi',
                'changed_at' => '2024-07-16 14:20:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 2,
                'status' => 'delivered',
                'note' => 'Giao hàng thành công',
                'changed_at' => '2024-07-18 14:00:00',
                'changed_by' => 'system',
            ],

            // Order 3 - MacBook Pro 14 inch M3
            [
                'order_id' => 3,
                'status' => 'pending',
                'note' => 'Đơn hàng được tạo',
                'changed_at' => '2024-07-17 08:00:00',
                'changed_by' => 'system',
            ],
            [
                'order_id' => 3,
                'status' => 'confirmed',
                'note' => 'Đơn hàng đã được xác nhận',
                'changed_at' => '2024-07-17 08:30:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 3,
                'status' => 'shipping',
                'note' => 'Đang xử lý đơn hàng',
                'changed_at' => '2024-07-17 09:00:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 3,
                'status' => 'shipping',
                'note' => 'Đơn hàng đã được gửi',
                'changed_at' => '2024-07-17 09:30:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 3,
                'status' => 'delivered',
                'note' => 'Giao hàng thành công',
                'changed_at' => '2024-07-19 10:00:00',
                'changed_by' => 'system',
            ],

            // Order 4 - Dell XPS 13 Plus
            [
                'order_id' => 4,
                'status' => 'pending',
                'note' => 'Đơn hàng được tạo',
                'changed_at' => '2024-07-18 15:00:00',
                'changed_by' => 'system',
            ],
            [
                'order_id' => 4,
                'status' => 'confirmed',
                'note' => 'Đơn hàng đã được xác nhận',
                'changed_at' => '2024-07-18 15:30:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 4,
                'status' => 'shipping',
                'note' => 'Đang xử lý đơn hàng',
                'changed_at' => '2024-07-18 16:00:00',
                'changed_by' => 'admin',
            ],

            // Order 5 - AirPods Pro 2
            [
                'order_id' => 5,
                'status' => 'pending',
                'note' => 'Đơn hàng được tạo',
                'changed_at' => '2024-07-19 11:00:00',
                'changed_by' => 'system',
            ],
            [
                'order_id' => 5,
                'status' => 'confirmed',
                'note' => 'Đơn hàng đã được xác nhận',
                'changed_at' => '2024-07-19 11:30:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 5,
                'status' => 'shipping',
                'note' => 'Đang xử lý đơn hàng',
                'changed_at' => '2024-07-19 12:00:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 5,
                'status' => 'shipping',
                'note' => 'Đơn hàng đã được gửi',
                'changed_at' => '2024-07-19 12:30:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 5,
                'status' => 'delivered',
                'note' => 'Giao hàng thành công',
                'changed_at' => '2024-07-20 13:00:00',
                'changed_by' => 'system',
            ],

            // Order 6 - Sony WH-1000XM5
            [
                'order_id' => 6,
                'status' => 'pending',
                'note' => 'Đơn hàng được tạo',
                'changed_at' => '2024-07-20 14:00:00',
                'changed_by' => 'system',
            ],
            [
                'order_id' => 6,
                'status' => 'cancelled',
                'note' => 'Đơn hàng bị hủy do khách hàng yêu cầu',
                'changed_at' => '2024-07-20 14:30:00',
                'changed_by' => 'customer',
            ],

            // Order 7 - Samsung Galaxy Tab S9
            [
                'order_id' => 7,
                'status' => 'pending',
                'note' => 'Đơn hàng được tạo',
                'changed_at' => '2024-07-21 09:00:00',
                'changed_by' => 'system',
            ],
            [
                'order_id' => 7,
                'status' => 'confirmed',
                'note' => 'Đơn hàng đã được xác nhận',
                'changed_at' => '2024-07-21 09:30:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 7,
                'status' => 'shipping',
                'note' => 'Đang xử lý đơn hàng',
                'changed_at' => '2024-07-21 10:00:00',
                'changed_by' => 'admin',
            ],

            // Order 8 - Lenovo ThinkPad X1 Carbon
            [
                'order_id' => 8,
                'status' => 'pending',
                'note' => 'Đơn hàng được tạo',
                'changed_at' => '2024-07-22 16:00:00',
                'changed_by' => 'system',
            ],
            [
                'order_id' => 8,
                'status' => 'confirmed',
                'note' => 'Đơn hàng đã được xác nhận',
                'changed_at' => '2024-07-22 16:30:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 8,
                'status' => 'shipping',
                'note' => 'Đang xử lý đơn hàng',
                'changed_at' => '2024-07-22 17:00:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 8,
                'status' => 'shipping',
                'note' => 'Đơn hàng đã được gửi',
                'changed_at' => '2024-07-22 17:30:00',
                'changed_by' => 'admin',
            ],
            [
                'order_id' => 8,
                'status' => 'delivered',
                'note' => 'Giao hàng thành công',
                'changed_at' => '2024-07-24 18:00:00',
                'changed_by' => 'system',
            ],
        ];

        foreach ($orderStatusHistories as $orderStatusHistory) {
            OrderStatusHistory::create($orderStatusHistory);
        }
    }
}
