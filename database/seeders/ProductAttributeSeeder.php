<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Enums\BasicStatus;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create product attributes
        $attributes = [
            [
                'name' => 'Màu sắc',
                'slug' => 'mau-sac',
                'type' => 'color',
                'is_required' => false,
                'is_variation' => true,
                'is_filterable' => true,
                'sort_order' => 1,
                'status' => BasicStatus::Active->value,
                'values' => [
                    ['value' => 'Đen', 'color_code' => '#000000', 'sort_order' => 1],
                    ['value' => 'Trắng', 'color_code' => '#FFFFFF', 'sort_order' => 2],
                    ['value' => 'Xanh dương', 'color_code' => '#0066CC', 'sort_order' => 3],
                    ['value' => 'Xanh lá', 'color_code' => '#00CC66', 'sort_order' => 4],
                    ['value' => 'Đỏ', 'color_code' => '#FF0000', 'sort_order' => 5],
                    ['value' => 'Vàng', 'color_code' => '#FFCC00', 'sort_order' => 6],
                    ['value' => 'Tím', 'color_code' => '#9933CC', 'sort_order' => 7],
                    ['value' => 'Hồng', 'color_code' => '#FF66CC', 'sort_order' => 8],
                    ['value' => 'Xám', 'color_code' => '#999999', 'sort_order' => 9],
                    ['value' => 'Bạc', 'color_code' => '#CCCCCC', 'sort_order' => 10],
                    ['value' => 'Vàng đồng', 'color_code' => '#D4AF37', 'sort_order' => 11],
                    ['value' => 'Xanh navy', 'color_code' => '#000080', 'sort_order' => 12],
                ]
            ],
            [
                'name' => 'Dung lượng',
                'slug' => 'dung-luong',
                'type' => 'select',
                'is_required' => false,
                'is_variation' => true,
                'is_filterable' => true,
                'sort_order' => 2,
                'status' => BasicStatus::Active->value,
                'values' => [
                    ['value' => '64GB', 'sort_order' => 1],
                    ['value' => '128GB', 'sort_order' => 2],
                    ['value' => '256GB', 'sort_order' => 3],
                    ['value' => '512GB', 'sort_order' => 4],
                    ['value' => '1TB', 'sort_order' => 5],
                    ['value' => '2TB', 'sort_order' => 6],
                ]
            ],
            [
                'name' => 'RAM',
                'slug' => 'ram',
                'type' => 'select',
                'is_required' => false,
                'is_variation' => true,
                'is_filterable' => true,
                'sort_order' => 3,
                'status' => BasicStatus::Active->value,
                'values' => [
                    ['value' => '4GB', 'sort_order' => 1],
                    ['value' => '8GB', 'sort_order' => 2],
                    ['value' => '16GB', 'sort_order' => 3],
                    ['value' => '32GB', 'sort_order' => 4],
                    ['value' => '64GB', 'sort_order' => 5],
                ]
            ],
            [
                'name' => 'Kích thước màn hình',
                'slug' => 'kich-thuoc-man-hinh',
                'type' => 'select',
                'is_required' => false,
                'is_variation' => false,
                'is_filterable' => true,
                'sort_order' => 4,
                'status' => BasicStatus::Active->value,
                'values' => [
                    ['value' => '5.5 inch', 'sort_order' => 1],
                    ['value' => '6.1 inch', 'sort_order' => 2],
                    ['value' => '6.5 inch', 'sort_order' => 3],
                    ['value' => '6.7 inch', 'sort_order' => 4],
                    ['value' => '13.3 inch', 'sort_order' => 5],
                    ['value' => '14 inch', 'sort_order' => 6],
                    ['value' => '15.6 inch', 'sort_order' => 7],
                    ['value' => '16 inch', 'sort_order' => 8],
                    ['value' => '17.3 inch', 'sort_order' => 9],
                ]
            ],
            [
                'name' => 'Hãng sản xuất',
                'slug' => 'hang-san-xuat',
                'type' => 'select',
                'is_required' => false,
                'is_variation' => false,
                'is_filterable' => true,
                'sort_order' => 5,
                'status' => BasicStatus::Active->value,
                'values' => [
                    ['value' => 'Apple', 'sort_order' => 1],
                    ['value' => 'Samsung', 'sort_order' => 2],
                    ['value' => 'Xiaomi', 'sort_order' => 3],
                    ['value' => 'Oppo', 'sort_order' => 4],
                    ['value' => 'Vivo', 'sort_order' => 5],
                    ['value' => 'Realme', 'sort_order' => 6],
                    ['value' => 'OnePlus', 'sort_order' => 7],
                    ['value' => 'ASUS', 'sort_order' => 8],
                    ['value' => 'Dell', 'sort_order' => 9],
                    ['value' => 'HP', 'sort_order' => 10],
                    ['value' => 'Lenovo', 'sort_order' => 11],
                    ['value' => 'MSI', 'sort_order' => 12],
                ]
            ],
            [
                'name' => 'Chất liệu',
                'slug' => 'chat-lieu',
                'type' => 'select',
                'is_required' => false,
                'is_variation' => false,
                'is_filterable' => true,
                'sort_order' => 6,
                'status' => BasicStatus::Active->value,
                'values' => [
                    ['value' => 'Nhôm', 'sort_order' => 1],
                    ['value' => 'Nhựa', 'sort_order' => 2],
                    ['value' => 'Kính', 'sort_order' => 3],
                    ['value' => 'Titan', 'sort_order' => 4],
                    ['value' => 'Thép không gỉ', 'sort_order' => 5],
                    ['value' => 'Da', 'sort_order' => 6],
                ]
            ],
            [
                'name' => 'Loại pin',
                'slug' => 'loai-pin',
                'type' => 'select',
                'is_required' => false,
                'is_variation' => false,
                'is_filterable' => true,
                'sort_order' => 7,
                'status' => BasicStatus::Active->value,
                'values' => [
                    ['value' => 'Li-ion', 'sort_order' => 1],
                    ['value' => 'Li-Po', 'sort_order' => 2],
                ]
            ],
            [
                'name' => 'Độ phân giải camera',
                'slug' => 'do-phan-giai-camera',
                'type' => 'select',
                'is_required' => false,
                'is_variation' => false,
                'is_filterable' => true,
                'sort_order' => 8,
                'status' => BasicStatus::Active->value,
                'values' => [
                    ['value' => '12MP', 'sort_order' => 1],
                    ['value' => '48MP', 'sort_order' => 2],
                    ['value' => '50MP', 'sort_order' => 3],
                    ['value' => '64MP', 'sort_order' => 4],
                    ['value' => '108MP', 'sort_order' => 5],
                    ['value' => '200MP', 'sort_order' => 6],
                ]
            ],
            [
                'name' => 'Loại tai nghe',
                'slug' => 'loai-tai-nghe',
                'type' => 'select',
                'is_required' => false,
                'is_variation' => false,
                'is_filterable' => true,
                'sort_order' => 9,
                'status' => BasicStatus::Active->value,
                'values' => [
                    ['value' => 'In-ear', 'sort_order' => 1],
                    ['value' => 'On-ear', 'sort_order' => 2],
                    ['value' => 'Over-ear', 'sort_order' => 3],
                    ['value' => 'True Wireless', 'sort_order' => 4],
                    ['value' => 'Có dây', 'sort_order' => 5],
                ]
            ],
            [
                'name' => 'Loại sạc',
                'slug' => 'loai-sac',
                'type' => 'select',
                'is_required' => false,
                'is_variation' => false,
                'is_filterable' => true,
                'sort_order' => 10,
                'status' => BasicStatus::Active->value,
                'values' => [
                    ['value' => 'Sạc nhanh', 'sort_order' => 1],
                    ['value' => 'Sạc không dây', 'sort_order' => 2],
                    ['value' => 'Sạc dự phòng', 'sort_order' => 3],
                    ['value' => 'Sạc thường', 'sort_order' => 4],
                ]
            ],
        ];

        // Create attributes and their values
        foreach ($attributes as $attributeData) {
            $values = $attributeData['values'];
            unset($attributeData['values']);

            $attribute = ProductAttribute::firstOrCreate(
                ['slug' => $attributeData['slug']],
                array_merge($attributeData, [
                    'created_user_id' => 1,
                    'updated_user_id' => 1,
                ])
            );

            // Create attribute values
            foreach ($values as $valueData) {
                ProductAttributeValue::firstOrCreate(
                    [
                        'product_attribute_id' => $attribute->id,
                        'value' => $valueData['value']
                    ],
                    array_merge($valueData, [
                        'created_user_id' => 1,
                        'updated_user_id' => 1,
                    ])
                );
            }
        }

        $this->command->info('ProductAttributeSeeder completed successfully!');
    }
}