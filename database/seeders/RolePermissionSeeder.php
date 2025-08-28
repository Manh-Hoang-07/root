<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo permissions cơ bản
        $permissions = [
            'view_dashboard' => 'Xem dashboard',
            'manage_users' => 'Quản lý người dùng',
            'manage_products' => 'Quản lý sản phẩm',
            'manage_orders' => 'Quản lý đơn hàng',
            'manage_categories' => 'Quản lý danh mục',
            'manage_brands' => 'Quản lý thương hiệu',
            'manage_roles' => 'Quản lý vai trò',
            'manage_permissions' => 'Quản lý quyền hạn',
            'view_reports' => 'Xem báo cáo',
            'manage_settings' => 'Quản lý cài đặt'
        ];

        foreach ($permissions as $name => $displayName) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'sanctum'
            ], [
                'display_name' => $displayName,
                'status' => 'active'
            ]);
        }

        // Tạo roles
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'sanctum'
        ], [
            'display_name' => 'Administrator',
            'status' => 'active'
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'sanctum'
        ], [
            'display_name' => 'User',
            'status' => 'active'
        ]);

        // Gán permissions cho roles
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));
        
        $userPermissions = Permission::whereIn('name', ['view_dashboard'])->get();
        $userRole->permissions()->sync($userPermissions->pluck('id'));

        // Gán roles cho users
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->roles()->sync([$adminRole->id]);
        }

        $normalUser = User::where('email', 'user@example.com')->first();
        if ($normalUser) {
            $normalUser->roles()->sync([$userRole->id]);
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
} 