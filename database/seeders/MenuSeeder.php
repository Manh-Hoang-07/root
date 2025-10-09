<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::truncate();

        // Top-level
        $system = Menu::create([
            'title' => 'Hệ thống',
            'route_name' => null,
            'icon' => 'fa fa-cog',
            'sort_order' => 1,
        ]);

        Menu::create([
            'title' => 'Người dùng',
            'route_name' => 'admin.users.index',
            'icon' => 'fa fa-user',
            'parent_id' => $system->id,
            'sort_order' => 1,
        ]);

        Menu::create([
            'title' => 'Phân quyền',
            'route_name' => 'admin.roles.index',
            'icon' => 'fa fa-lock',
            'parent_id' => $system->id,
            'sort_order' => 2,
        ]);

        $inventory = Menu::create([
            'title' => 'Quản lý kho',
            'route_name' => null,
            'icon' => 'fa fa-boxes',
            'sort_order' => 2,
        ]);

        Menu::create([
            'title' => 'Sản phẩm',
            'route_name' => 'admin.products.index',
            'icon' => 'fa fa-box',
            'parent_id' => $inventory->id,
            'sort_order' => 1,
        ]);
    }
}
