<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShippingInfo;
use App\Models\Order;

class ShippingInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shippingInfos = [
            [
                'order_id' => 1,
                'shipping_method' => 'express',
                'provider' => 'Giao hàng nhanh',
                'service_code' => 'EXP',
                'provider_order_code' => 'PO001',
                'shipping_fee' => 50000,
                'shipping_discount' => 0,
                'raw_fee_response' => '{}',
                'tracking_number' => 'GHN001234567',
                'carrier' => 'Giao hàng nhanh',
                'status' => 'delivered',
                'shipped_at' => '2024-07-15 10:00:00',
                'delivered_at' => '2024-07-16 09:00:00',
                'expected_delivery_time' => '2024-07-17 10:00:00',
                'note' => 'Giao hàng vào buổi sáng',
            ],
            [
                'order_id' => 2,
                'shipping_method' => 'standard',
                'provider' => 'Giao hàng tiêu chuẩn',
                'service_code' => 'STD',
                'provider_order_code' => 'PO002',
                'shipping_fee' => 30000,
                'shipping_discount' => 0,
                'raw_fee_response' => '{}',
                'tracking_number' => 'GHT002345678',
                'carrier' => 'Giao hàng tiêu chuẩn',
                'status' => 'delivered',
                'shipped_at' => '2024-07-16 14:00:00',
                'delivered_at' => '2024-07-18 15:00:00',
                'expected_delivery_time' => '2024-07-19 16:00:00',
                'note' => 'Giao hàng vào buổi chiều',
            ],
            [
                'order_id' => 3,
                'shipping_method' => 'express',
                'provider' => 'Giao hàng nhanh',
                'service_code' => 'EXP',
                'provider_order_code' => 'PO003',
                'shipping_fee' => 50000,
                'shipping_discount' => 0,
                'raw_fee_response' => '{}',
                'tracking_number' => 'GHN003456789',
                'carrier' => 'Giao hàng nhanh',
                'status' => 'delivered',
                'shipped_at' => '2024-07-17 09:00:00',
                'delivered_at' => '2024-07-19 10:00:00',
                'expected_delivery_time' => '2024-07-20 11:00:00',
                'note' => 'Giao hàng vào buổi sáng',
            ],
            [
                'order_id' => 4,
                'shipping_method' => 'standard',
                'provider' => 'Giao hàng tiêu chuẩn',
                'service_code' => 'STD',
                'provider_order_code' => 'PO004',
                'shipping_fee' => 40000,
                'shipping_discount' => 0,
                'raw_fee_response' => '{}',
                'tracking_number' => 'GHT004567890',
                'carrier' => 'Giao hàng tiêu chuẩn',
                'status' => 'shipping',
                'shipped_at' => '2024-07-18 16:00:00',
                'delivered_at' => null,
                'expected_delivery_time' => '2024-07-22 17:00:00',
                'note' => 'Đang vận chuyển',
            ],
            [
                'order_id' => 5,
                'shipping_method' => 'express',
                'provider' => 'Giao hàng nhanh',
                'service_code' => 'EXP',
                'provider_order_code' => 'PO005',
                'shipping_fee' => 45000,
                'shipping_discount' => 0,
                'raw_fee_response' => '{}',
                'tracking_number' => 'GHN005678901',
                'carrier' => 'Giao hàng nhanh',
                'status' => 'delivered',
                'shipped_at' => '2024-07-19 11:00:00',
                'delivered_at' => '2024-07-20 12:00:00',
                'expected_delivery_time' => '2024-07-21 13:00:00',
                'note' => 'Giao hàng thành công',
            ],
            [
                'order_id' => 6,
                'shipping_method' => 'standard',
                'provider' => 'Giao hàng tiêu chuẩn',
                'service_code' => 'STD',
                'provider_order_code' => 'PO006',
                'shipping_fee' => 35000,
                'shipping_discount' => 0,
                'raw_fee_response' => '{}',
                'tracking_number' => 'GHT006789012',
                'carrier' => 'Giao hàng tiêu chuẩn',
                'status' => 'pending',
                'shipped_at' => null,
                'delivered_at' => null,
                'expected_delivery_time' => '2024-07-25 14:00:00',
                'note' => 'Chờ xử lý',
            ],
        ];

        foreach ($shippingInfos as $shippingInfo) {
            ShippingInfo::create($shippingInfo);
        }
    }
}
