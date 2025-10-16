<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'title' => 'Dashboard',
                'api' => 'GET /api/admin/dashboard',
                'path' => '/admin/dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'parent_id' => null,
                'sort_order' => 1,
                'permissions' => json_encode(['view_dashboard']),
                'status' => 'active',
                'type' => 'admin',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
            [
                'title' => 'Quản lý người dùng',
                'api' => 'GET /api/admin/users',
                'path' => '/admin/users',
                'icon' => 'fas fa-users',
                'parent_id' => null,
                'sort_order' => 2,
                'permissions' => json_encode(['manage_users']),
                'status' => 'active',
                'type' => 'admin',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
            [
                'title' => 'Quản lý vai trò',
                'api' => 'GET /api/admin/roles',
                'path' => '/admin/roles',
                'icon' => 'fas fa-user-tag',
                'parent_id' => null,
                'sort_order' => 3,
                'permissions' => json_encode(['manage_roles']),
                'status' => 'active',
                'type' => 'admin',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
            [
                'title' => 'Quản lý bài viết',
                'api' => 'GET /api/admin/posts',
                'path' => '/admin/posts',
                'icon' => 'fas fa-newspaper',
                'parent_id' => null,
                'sort_order' => 4,
                'permissions' => json_encode(['manage_posts']),
                'status' => 'active',
                'type' => 'admin',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
            [
                'title' => 'Danh mục bài viết',
                'api' => 'GET /api/admin/post-categories',
                'path' => '/admin/post-categories',
                'icon' => 'fas fa-folder',
                'parent_id' => null,
                'sort_order' => 5,
                'permissions' => json_encode(['manage_categories']),
                'status' => 'active',
                'type' => 'admin',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
            [
                'title' => 'Thẻ bài viết',
                'api' => 'GET /api/admin/post-tags',
                'path' => '/admin/post-tags',
                'icon' => 'fas fa-tags',
                'parent_id' => null,
                'sort_order' => 6,
                'permissions' => json_encode(['manage_tags']),
                'status' => 'active',
                'type' => 'admin',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
            [
                'title' => 'Liên hệ',
                'api' => 'GET /api/admin/contacts',
                'path' => '/admin/contacts',
                'icon' => 'fas fa-envelope',
                'parent_id' => null,
                'sort_order' => 7,
                'permissions' => json_encode(['manage_contacts']),
                'status' => 'active',
                'type' => 'admin',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
            [
                'title' => 'Cấu hình hệ thống',
                'api' => 'GET /api/admin/system-configs',
                'path' => '/admin/system-configs',
                'icon' => 'fas fa-cog',
                'parent_id' => null,
                'sort_order' => 8,
                'permissions' => json_encode(['manage_system_configs']),
                'status' => 'active',
                'type' => 'admin',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
            [
                'title' => 'Mẫu thông báo',
                'api' => 'GET /api/admin/notification-templates',
                'path' => '/admin/notification-templates',
                'icon' => 'fas fa-bell',
                'parent_id' => null,
                'sort_order' => 9,
                'permissions' => json_encode(['manage_notification_templates']),
                'status' => 'active',
                'type' => 'admin',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
            [
                'title' => 'Quản lý menu',
                'api' => 'GET /api/admin/menus',
                'path' => '/admin/menus',
                'icon' => 'fas fa-bars',
                'parent_id' => null,
                'sort_order' => 10,
                'permissions' => json_encode(['manage_menus']),
                'status' => 'active',
                'type' => 'admin',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
            [
                'title' => 'Trang chủ',
                'api' => null,
                'path' => '/',
                'icon' => 'fas fa-home',
                'parent_id' => null,
                'sort_order' => 1,
                'permissions' => json_encode([]), // public => không cần quyền
                'status' => 'active',
                'type' => 'public',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
            [
                'title' => 'Sản phẩm',
                'api' => null,
                'path' => '/products',
                'icon' => 'fas fa-box',
                'parent_id' => null,
                'sort_order' => 2,
                'permissions' => json_encode([]), // public
                'status' => 'active',
                'type' => 'public',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ],
        ];


        foreach ($menus as $menuData) {
            Menu::firstOrCreate([
                'title' => $menuData['title'],
                'api' => $menuData['api']
            ], $menuData);
        }

        $this->command->info('Menus seeded successfully!');
    }
}
