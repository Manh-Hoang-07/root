<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        // Tạo attribute Màu sắc
        $colorAttribute = Attribute::create([
            'name' => 'Màu sắc',
            'description' => 'Màu sắc của sản phẩm',
            'type' => 'select',
            'is_required' => false,
            'is_unique' => false,
            'is_filterable' => true,
            'is_searchable' => true,
            'status' => 'active'
        ]);

        // Tạo các giá trị màu sắc
        $colors = ['Đỏ', 'Xanh dương', 'Xanh lá', 'Vàng', 'Đen', 'Trắng', 'Hồng', 'Tím', 'Cam', 'Nâu'];
        foreach ($colors as $index => $color) {
            AttributeValue::create([
                'attribute_id' => $colorAttribute->id,
                'value' => $color,
                'sort_order' => $index + 1,
                'status' => 'active'
            ]);
        }

        // Tạo attribute Kích thước
        $sizeAttribute = Attribute::create([
            'name' => 'Kích thước',
            'description' => 'Kích thước của sản phẩm',
            'type' => 'select',
            'is_required' => false,
            'is_unique' => false,
            'is_filterable' => true,
            'is_searchable' => true,
            'status' => 'active'
        ]);

        // Tạo các giá trị kích thước
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        foreach ($sizes as $index => $size) {
            AttributeValue::create([
                'attribute_id' => $sizeAttribute->id,
                'value' => $size,
                'sort_order' => $index + 1,
                'status' => 'active'
            ]);
        }

        // Tạo attribute Chất liệu
        $materialAttribute = Attribute::create([
            'name' => 'Chất liệu',
            'description' => 'Chất liệu của sản phẩm',
            'type' => 'select',
            'is_required' => false,
            'is_unique' => false,
            'is_filterable' => true,
            'is_searchable' => true,
            'status' => 'active'
        ]);

        // Tạo các giá trị chất liệu
        $materials = ['Cotton', 'Polyester', 'Linen', 'Silk', 'Wool', 'Denim', 'Leather', 'Synthetic'];
        foreach ($materials as $index => $material) {
            AttributeValue::create([
                'attribute_id' => $materialAttribute->id,
                'value' => $material,
                'sort_order' => $index + 1,
                'status' => 'active'
            ]);
        }
    }
} 