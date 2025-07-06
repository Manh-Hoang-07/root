<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            WarehouseSeeder::class,
            ShippingZoneSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
