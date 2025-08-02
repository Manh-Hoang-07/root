<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả sản phẩm và kho hàng
        $products = Product::all();
        $warehouses = Warehouse::all();

        if ($products->isEmpty() || $warehouses->isEmpty()) {
            $this->command->info('Không có sản phẩm hoặc kho hàng nào để tạo inventory!');
            return;
        }

        // Tạo dữ liệu inventory mẫu
        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                // Tạo hoặc cập nhật inventory với số lượng ngẫu nhiên
                Inventory::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                    ],
                    [
                        'quantity' => rand(0, 100), // Số lượng từ 0-100
                    ]
                );
            }
        }

        $this->command->info('Đã tạo dữ liệu inventory mẫu thành công!');
    }
}
