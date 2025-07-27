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
        // Chạy AuthSeeder trước
        $this->call([
            AuthSeeder::class,
        ]);

        // $this->call([
        //     WarehouseSeeder::class,
        //     ShippingZoneSeeder::class,
        //     ProductSeeder::class,
        //     OrderSeeder::class,
        // ]);

        // Seeder cho 100 role, 100 permission, 100 user (nếu cần)
        // \App\Models\Permission::factory()->count(100)->create([
        //     'guard_name' => 'web',
        // ]);
        // \App\Models\Role::factory()->count(100)->create([
        //     'guard_name' => 'web',
        // ]);
        // \App\Models\User::factory()->count(100)->create();

        // Gán permission cho role ngẫu nhiên
        // $permissions = \App\Models\Permission::all();
        // $roles = \App\Models\Role::all();
        // foreach ($roles as $role) {
        //     $role->permissions()->sync($permissions->random(rand(5, 20))->pluck('id')->toArray());
        // }

        // Gán role cho user ngẫu nhiên
        // $users = \App\Models\User::all();
        // foreach ($users as $user) {
        //     $user->roles()->sync($roles->random(rand(1, 3))->pluck('id')->toArray());
        // }
    }
}
