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
use App\Models\Warehouse;
use App\Models\ShippingZone;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        $warehouses = Warehouse::all();
        $shippingZones = ShippingZone::all();

        if ($users->isEmpty() || $products->isEmpty() || $warehouses->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'confirmed', 'processing', 'shipping', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed'];
        $paymentMethods = ['cash', 'bank_transfer', 'credit_card', 'cod'];

        for ($i = 1; $i <= 20; $i++) {
            $user = $users->random();
            $warehouse = $warehouses->random();
            $shippingZone = $shippingZones->random();
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            $order = Order::create([
                'user_id' => $user->id,
                'warehouse_id' => $warehouse->id,
                'shipping_zone_id' => $shippingZone->id,
                'total_amount' => 0,
                'shipping_fee' => $shippingZone->base_fee,
                'discount_amount' => rand(0, 500000),
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'shipping_address' => [
                    'name' => $user->name,
                    'phone' => '0' . rand(900000000, 999999999),
                    'address' => rand(1, 999) . ' Đường ABC, Quận XYZ',
                    'province' => $shippingZone->provinces[0] ?? 'Hà Nội',
                    'district' => $shippingZone->districts[0] ?? 'Cầu Giấy',
                ],
                'notes' => rand(0, 1) ? 'Ghi chú đơn hàng ' . $i : null,
            ]);

            // Tạo order items
            $numItems = rand(1, 3);
            $totalAmount = 0;
            
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $price = $product->price;
                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'product_name' => $product->name,
                    'product_price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]);
            }

            // Cập nhật tổng tiền đơn hàng
            $order->update([
                'total_amount' => $totalAmount,
                'final_amount' => $totalAmount + $order->shipping_fee - $order->discount_amount,
            ]);

            // Tạo lịch sử trạng thái
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'note' => 'Đơn hàng được tạo',
                'changed_by' => $user->id,
            ]);

            if ($status !== 'pending') {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $status,
                    'note' => 'Cập nhật trạng thái đơn hàng',
                    'changed_by' => $user->id,
                ]);
            }

            // Tạo thông tin vận chuyển nếu đã shipping hoặc delivered
            if (in_array($status, ['shipping', 'delivered'])) {
                ShippingInfo::create([
                    'order_id' => $order->id,
                    'tracking_number' => 'TN' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
                    'shipping_company' => 'Giao hàng nhanh',
                    'shipping_method' => 'Standard',
                    'estimated_delivery' => now()->addDays($shippingZone->estimated_days),
                    'actual_delivery' => $status === 'delivered' ? now() : null,
                    'shipping_cost' => $order->shipping_fee,
                ]);
            }
        }
    }
}
