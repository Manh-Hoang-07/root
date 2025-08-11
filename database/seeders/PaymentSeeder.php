<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Order;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payments = [
            [
                'order_id' => 1,
                'payment_method' => 'credit_card',
                'payment_status' => 'paid',
                'transaction_id' => 'TXN001',
                'amount' => 29990000,
                'paid_at' => '2024-07-15 10:30:00',
                'note' => 'Thanh toán thành công qua VNPay',
            ],
            [
                'order_id' => 2,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'paid',
                'transaction_id' => 'TXN002',
                'amount' => 27990000,
                'paid_at' => '2024-07-16 14:20:00',
                'note' => 'Chuyển khoản qua MoMo',
            ],
            [
                'order_id' => 3,
                'payment_method' => 'credit_card',
                'payment_status' => 'paid',
                'transaction_id' => 'TXN003',
                'amount' => 45990000,
                'paid_at' => '2024-07-17 09:15:00',
                'note' => 'Thanh toán thành công qua VNPay',
            ],
            [
                'order_id' => 4,
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'transaction_id' => null,
                'amount' => 35990000,
                'paid_at' => null,
                'note' => 'Thanh toán khi nhận hàng',
            ],
            [
                'order_id' => 5,
                'payment_method' => 'credit_card',
                'payment_status' => 'paid',
                'transaction_id' => 'TXN005',
                'amount' => 5990000,
                'paid_at' => '2024-07-18 16:45:00',
                'note' => 'Thanh toán thành công qua VNPay',
            ],
            [
                'order_id' => 6,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'failed',
                'transaction_id' => 'TXN006',
                'amount' => 7990000,
                'paid_at' => null,
                'note' => 'Chuyển khoản thất bại - Không đủ tiền',
            ],
            [
                'order_id' => 7,
                'payment_method' => 'credit_card',
                'payment_status' => 'paid',
                'transaction_id' => 'TXN007',
                'amount' => 8990000,
                'paid_at' => '2024-07-19 11:30:00',
                'note' => 'Thanh toán thành công qua VNPay',
            ],
            [
                'order_id' => 8,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'paid',
                'transaction_id' => 'TXN008',
                'amount' => 10990000,
                'paid_at' => '2024-07-20 15:20:00',
                'note' => 'Chuyển khoản qua MoMo',
            ],
        ];

        foreach ($payments as $payment) {
            Payment::create($payment);
        }
    }
}
