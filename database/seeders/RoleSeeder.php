<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo roles đơn giản
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ], [
            'display_name' => 'Administrator',
            'status' => 'active'
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web'
        ], [
            'display_name' => 'User',
            'status' => 'active'
        ]);

        // Gán tất cả quyền cho admin
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Gán quyền cơ bản cho user
        $userPermissions = Permission::whereIn('name', [
            'view_dashboard',
            'access_api'
        ])->get();
        $userRole->permissions()->sync($userPermissions->pluck('id'));

        // Gán roles cho users
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
            $this->command->info('Admin role assigned to admin@example.com');
        }

        $normalUser = User::where('email', 'user@example.com')->first();
        if ($normalUser) {
            $normalUser->assignRole('user');
            $this->command->info('User role assigned to user@example.com');
        }

        $this->command->info('Roles created and assigned successfully!');
    }

} 