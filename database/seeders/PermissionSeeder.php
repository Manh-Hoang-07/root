<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Quyền cơ bản
            'view_dashboard' => 'Xem dashboard',
            'manage_users' => 'Quản lý người dùng',
            'manage_roles' => 'Quản lý vai trò',
            'manage_permissions' => 'Quản lý quyền hạn',
            'manage_posts' => 'Quản lý bài viết',
            'manage_categories' => 'Quản lý danh mục',
            'manage_tags' => 'Quản lý thẻ',
            'manage_contacts' => 'Quản lý liên hệ',
            'manage_menus' => 'Quản lý menu',
            'manage_system_configs' => 'Quản lý cấu hình hệ thống',
            'manage_notification_templates' => 'Quản lý mẫu thông báo',
            'manage_files' => 'Quản lý tệp',
            'view_reports' => 'Xem báo cáo',
            'access_api' => 'Truy cập API',
        ];

        foreach ($permissions as $name => $displayName) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web'
            ], [
                'display_name' => $displayName,
                'status' => 'active'
            ]);
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
