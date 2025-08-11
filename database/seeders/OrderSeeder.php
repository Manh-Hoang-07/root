<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ShippingInfo;
use App\Models\User;
use App\Models\Product;
use App\Models\Address;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'confirmed', 'shipping', 'delivered', 'cancelled', 'completed'];
        $paymentStatuses = ['pending', 'paid', 'failed'];
        $paymentMethods = ['cash', 'bank_transfer', 'credit_card', 'cod'];

        for ($i = 1; $i <= 20; $i++) {
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];
            
            // Tạo địa chỉ giao hàng trước
            $shippingAddress = Address::create([
                'user_id' => $user->id,
                'name' => $user->name ?? 'Khách hàng',
                'phone' => '0' . rand(900000000, 999999999),
                'address_line' => rand(1, 999) . ' Đường ABC, Quận XYZ',
                'city' => 'Hà Nội',
                'province' => 'Hà Nội',
                'district' => 'Cầu Giấy',
                'ward' => 'Phường ABC',
                'is_default' => false,
            ]);

            $order = Order::create([
                'user_id' => $user->id,
                'status' => $status,
                'total_price' => 0,
                'shipping_fee' => rand(20000, 50000),
                'shipping_discount' => rand(0, 10000),
                'promotion_discount' => rand(0, 100000),
                'final_price' => 0,
                'shipping_address_id' => $shippingAddress->id,
                'note' => rand(0, 1) ? 'Ghi chú đơn hàng ' . $i : null,
            ]);

            // Tạo order items
            $numItems = rand(1, 3);
            $totalPrice = 0;
            
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $price = $product->price;
                $subtotal = $price * $quantity;
                $totalPrice += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'discount' => 0,
                    'final_price' => $price,
                    'total' => $subtotal,
                ]);
            }

            // Cập nhật tổng tiền đơn hàng
            $finalPrice = $totalPrice + $order->shipping_fee - $order->shipping_discount - $order->promotion_discount;
            $order->update([
                'total_price' => $totalPrice,
                'final_price' => $finalPrice,
            ]);

            // Tạo lịch sử trạng thái
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'note' => 'Đơn hàng được tạo',
                'changed_by' => $user->id,
                'changed_at' => now(),
            ]);

            if ($status !== 'pending') {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $status,
                    'note' => 'Cập nhật trạng thái đơn hàng',
                    'changed_by' => $user->id,
                    'changed_at' => now(),
                ]);
            }

            // Tạo thông tin vận chuyển nếu đã shipping hoặc delivered
            if (in_array($status, ['shipping', 'delivered'])) {
                ShippingInfo::create([
                    'order_id' => $order->id,
                    'shipping_method' => 'Standard',
                    'provider' => 'Giao hàng nhanh',
                    'service_code' => 'STD',
                    'provider_order_code' => 'PO' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
                    'shipping_fee' => $order->shipping_fee,
                    'shipping_discount' => $order->shipping_discount,
                    'raw_fee_response' => '{}',
                    'tracking_number' => 'TN' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
                    'carrier' => 'Giao hàng nhanh',
                    'status' => $status === 'delivered' ? 'delivered' : 'shipping',
                    'shipped_at' => $status === 'shipping' ? now() : null,
                    'delivered_at' => $status === 'delivered' ? now() : null,
                    'expected_delivery_time' => now()->addDays(rand(1, 5)),
                    'note' => 'Ghi chú vận chuyển',
                ]);
            }
        }
    }
}
