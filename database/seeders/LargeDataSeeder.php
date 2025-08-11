<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Order;
use App\Models\User;
use App\Models\Image;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\Promotion;
use App\Models\Address;
use App\Models\Payment;
use App\Models\ShippingInfo;
use App\Models\OrderStatusHistory;

class LargeDataSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Bắt đầu tạo dữ liệu lớn...');

        // Tạo 1000 Brands
        $this->command->info('Tạo 1000 Brands...');
        Brand::factory(1000)->create();

        // Tạo 1000 Categories
        $this->command->info('Tạo 1000 Categories...');
        Category::factory(1000)->create();

        // Tạo 1000 Products
        $this->command->info('Tạo 1000 Products...');
        Product::factory(1000)->create();

        // Tạo 1000 Variants
        $this->command->info('Tạo 1000 Variants...');
        Variant::factory(1000)->create();

        // Tạo 1000 Users (thêm vào 2 user có sẵn)
        $this->command->info('Tạo 1000 Users...');
        User::factory(1000)->create();

        // Tạo 1000 Addresses (trước Orders)
        $this->command->info('Tạo 1000 Addresses...');
        $this->createAddresses();

        // Tạo 1000 Orders
        $this->command->info('Tạo 1000 Orders...');
        Order::factory(1000)->create();

        // Tạo 1000 Images
        $this->command->info('Tạo 1000 Images...');
        $this->createImages();

        // Tạo 1000 Warehouses
        $this->command->info('Tạo 1000 Warehouses...');
        $this->createWarehouses();

        // Tạo 1000 Inventories
        $this->command->info('Tạo 1000 Inventories...');
        $this->createInventories();

        // Tạo 1000 Promotions
        $this->command->info('Tạo 1000 Promotions...');
        $this->createPromotions();

        // Tạo 1000 Payments
        $this->command->info('Tạo 1000 Payments...');
        $this->createPayments();

        // Tạo 1000 ShippingInfos
        $this->command->info('Tạo 1000 ShippingInfos...');
        $this->createShippingInfos();

        // Tạo 1000 OrderStatusHistories
        $this->command->info('Tạo 1000 OrderStatusHistories...');
        $this->createOrderStatusHistories();

        $this->command->info('Hoàn thành tạo dữ liệu lớn!');
    }

    private function createImages()
    {
        $imageableTypes = ['App\Models\Product', 'App\Models\Category', 'App\Models\Brand'];
        
        for ($i = 0; $i < 1000; $i++) {
            $imageableType = $imageableTypes[array_rand($imageableTypes)];
            $imageableId = rand(1, 1000);
            
            Image::create([
                'imageable_type' => $imageableType,
                'imageable_id' => $imageableId,
                'url' => 'images/' . strtolower(str_replace('App\Models\\', '', $imageableType)) . '/' . $imageableId . '-' . rand(1, 5) . '.jpg',
            ]);
        }
    }

    private function createWarehouses()
    {
        $cities = ['Hà Nội', 'TP.HCM', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ', 'Nha Trang', 'Huế', 'Vũng Tàu'];
        
        for ($i = 0; $i < 1000; $i++) {
            Warehouse::create([
                'name' => 'Kho ' . $cities[array_rand($cities)] . ' ' . ($i + 1),
                'address' => $this->faker->address(),
                'city' => $cities[array_rand($cities)],
                'province' => $this->faker->state(),
                'manager_id' => rand(1, 1002), // User ID
                'phone' => $this->faker->phoneNumber(),
                'email' => $this->faker->email(),
                'status' => $this->faker->randomElement(['active', 'inactive']),
            ]);
        }
    }

    private function createInventories()
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 1000; $i++) {
            $quantity = rand(0, 1000);
            $reservedQuantity = rand(0, min(100, $quantity));
            
            Inventory::create([
                'product_id' => rand(1, 1000),
                'variant_id' => rand(1, 1000),
                'warehouse_id' => rand(1, 1000),
                'quantity' => $quantity,
                'batch_no' => 'BATCH-' . strtoupper(substr(md5($i), 0, 8)),
                'lot_no' => 'LOT-' . strtoupper(substr(md5($i), 0, 6)),
                'expiry_date' => $faker->optional(0.7)->dateTimeBetween('now', '+2 years'),
                'reserved_quantity' => $reservedQuantity,
                'available_quantity' => $quantity - $reservedQuantity,
                'cost_price' => rand(50000, 25000000),
            ]);
        }
    }

    private function createPromotions()
    {
        $types = ['product', 'category', 'order', 'ship'];
        $discountTypes = ['percent', 'fixed', 'free'];
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 1000; $i++) {
            Promotion::create([
                'name' => 'Khuyến mãi ' . ($i + 1),
                'type' => $types[array_rand($types)],
                'discount_type' => $discountTypes[array_rand($discountTypes)],
                'discount_value' => rand(5, 50),
                'min_order_value' => rand(100000, 5000000),
                'start_at' => $faker->dateTimeBetween('-1 year', '+1 year'),
                'end_at' => $faker->dateTimeBetween('now', '+2 years'),
                'status' => $faker->randomElement(['active', 'inactive']),
            ]);
        }
    }

    private function createAddresses()
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 1000; $i++) {
            Address::create([
                'user_id' => rand(1, 1002), // 1000 + 2 users có sẵn
                'name' => $faker->name(),
                'phone' => $faker->phoneNumber(),
                'address_line' => $faker->address(),
                'city' => $faker->city(),
                'province' => $faker->state(),
                'district' => $faker->city(),
                'ward' => $faker->city(),
                'postal_code' => $faker->postcode(),
                'is_default' => $faker->boolean(20),
            ]);
        }
    }

    private function createPayments()
    {
        $methods = ['cod', 'bank_transfer', 'credit_card', 'paypal', 'momo', 'vnpay'];
        $statuses = ['pending', 'paid', 'failed'];
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 1000; $i++) {
            Payment::create([
                'order_id' => rand(1, 1000),
                'payment_method' => $methods[array_rand($methods)],
                'payment_status' => $statuses[array_rand($statuses)],
                'transaction_id' => 'TXN-' . strtoupper(substr(md5($i), 0, 10)),
                'paid_at' => $faker->optional(0.7)->dateTimeBetween('-1 year', 'now'),
                'amount' => rand(100000, 50000000),
                'note' => $faker->optional(0.3)->sentence(5),
            ]);
        }
    }

    private function createShippingInfos()
    {
        $carriers = ['Giao Hang Nhanh', 'Giao Hang Tiet Kiem', 'Viettel Post', 'EMS', 'DHL', 'FedEx'];
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 1000; $i++) {
            ShippingInfo::create([
                'order_id' => rand(1, 1000),
                'shipping_method' => $faker->randomElement(['standard', 'express', 'overnight']),
                'provider' => $carriers[array_rand($carriers)],
                'service_code' => 'SVC-' . strtoupper(substr(md5($i), 0, 6)),
                'provider_order_code' => 'PROV-' . strtoupper(substr(md5($i), 0, 8)),
                'shipping_fee' => rand(0, 500000),
                'shipping_discount' => $faker->optional(0.3)->numberBetween(0, 100000),
                'raw_fee_response' => json_encode(['fee' => rand(0, 500000), 'discount' => rand(0, 100000)]),
                'tracking_number' => 'TRK-' . strtoupper(substr(md5($i), 0, 8)),
                'carrier' => $carriers[array_rand($carriers)],
                'status' => $faker->randomElement(['pending', 'shipping', 'delivered', 'cancelled']),
                'shipped_at' => $faker->optional(0.7)->dateTimeBetween('now', '+1 week'),
                'delivered_at' => $faker->optional(0.5)->dateTimeBetween('now', '+2 weeks'),
                'expected_delivery_time' => $faker->dateTimeBetween('now', '+2 weeks'),
                'note' => $faker->optional(0.2)->sentence(5),
            ]);
        }
    }

    private function createOrderStatusHistories()
    {
        $statuses = ['pending', 'confirmed', 'shipping', 'delivered', 'cancelled', 'completed'];
        $changedBy = ['system', 'admin', 'customer'];
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 1000; $i++) {
            OrderStatusHistory::create([
                'order_id' => rand(1, 1000),
                'status' => $statuses[array_rand($statuses)],
                'note' => $faker->sentence(5),
                'changed_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'changed_by' => $changedBy[array_rand($changedBy)],
            ]);
        }
    }
}
