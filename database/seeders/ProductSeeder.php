<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductCategory;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariantAttribute;
use App\Enums\ProductStatus;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $phoneCategory = ProductCategory::where('slug', 'dien-thoai')->first();
        $iphoneCategory = ProductCategory::where('slug', 'iphone')->first();
        $laptopCategory = ProductCategory::where('slug', 'laptop')->first();
        $macbookCategory = ProductCategory::where('slug', 'macbook')->first();
        $tabletCategory = ProductCategory::where('slug', 'tablet')->first();
        $accessoryCategory = ProductCategory::where('slug', 'phu-kien')->first();
        $headphoneCategory = ProductCategory::where('slug', 'tai-nghe')->first();
        $smartwatchCategory = ProductCategory::where('slug', 'smartwatch')->first();

        // Get attributes
        $colorAttr = ProductAttribute::where('slug', 'mau-sac')->first();
        $storageAttr = ProductAttribute::where('slug', 'dung-luong')->first();
        $ramAttr = ProductAttribute::where('slug', 'ram')->first();
        $screenAttr = ProductAttribute::where('slug', 'kich-thuoc-man-hinh')->first();
        $brandAttr = ProductAttribute::where('slug', 'hang-san-xuat')->first();
        $materialAttr = ProductAttribute::where('slug', 'chat-lieu')->first();
        $headphoneTypeAttr = ProductAttribute::where('slug', 'loai-tai-nghe')->first();

        // Get attribute values
        $colors = ProductAttributeValue::where('product_attribute_id', $colorAttr->id)->get();
        $storages = ProductAttributeValue::where('product_attribute_id', $storageAttr->id)->get();
        $rams = ProductAttributeValue::where('product_attribute_id', $ramAttr->id)->get();
        $screenSizes = ProductAttributeValue::where('product_attribute_id', $screenAttr->id)->get();
        $brands = ProductAttributeValue::where('product_attribute_id', $brandAttr->id)->get();
        $materials = ProductAttributeValue::where('product_attribute_id', $materialAttr->id)->get();
        $headphoneTypes = ProductAttributeValue::where('product_attribute_id', $headphoneTypeAttr->id)->get();

        // iPhone 15 Pro
        $iphone15Pro = Product::firstOrCreate(
            ['sku' => 'IP15P'],
            [
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'description' => 'iPhone 15 Pro với chip A17 Pro, thiết kế titan',
                'short_description' => 'iPhone 15 Pro - Chip A17 Pro mạnh mẽ',
                'price' => 99900000,
                'sale_price' => 94900000,
                'cost_price' => 75000000,
                'stock_quantity' => 50,
                'min_stock_level' => 10,
                'weight' => 187,
                'dimensions' => json_encode(['length' => 146.6, 'width' => 70.6, 'height' => 8.25]),
                'image' => 'products/iphone-15-pro.jpg',
                'gallery' => json_encode([
                    'products/iphone-15-pro-1.jpg',
                    'products/iphone-15-pro-2.jpg',
                    'products/iphone-15-pro-3.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => true,
                'is_variable' => true,
                'is_digital' => false,
                'meta_title' => 'iPhone 15 Pro chính hãng',
                'meta_description' => 'Mua iPhone 15 Pro giá tốt nhất, bảo hành 12 tháng',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );

        // Attach categories to iPhone 15 Pro
        $iphone15Pro->categories()->attach([$phoneCategory->id, $iphoneCategory->id]);

        // Create variants for iPhone 15 Pro
        $this->createIPhoneVariants($iphone15Pro, $colors, $storages);

        // iPhone 15 Pro Max
        $iphone15ProMax = Product::firstOrCreate(
            ['sku' => 'IP15PM'],
            [
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'description' => 'iPhone 15 Pro Max với chip A17 Pro, camera 5x zoom',
                'short_description' => 'iPhone 15 Pro Max - Camera zoom 5x',
                'price' => 119900000,
                'sale_price' => 114900000,
                'cost_price' => 90000000,
                'stock_quantity' => 30,
                'min_stock_level' => 5,
                'weight' => 221,
                'dimensions' => json_encode(['length' => 159.9, 'width' => 76.7, 'height' => 8.25]),
                'image' => 'products/iphone-15-pro-max.jpg',
                'gallery' => json_encode([
                    'products/iphone-15-pro-max-1.jpg',
                    'products/iphone-15-pro-max-2.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => true,
                'is_variable' => true,
                'is_digital' => false,
                'meta_title' => 'iPhone 15 Pro Max chính hãng',
                'meta_description' => 'Mua iPhone 15 Pro Max giá tốt nhất, bảo hành 12 tháng',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );

        // Attach categories to iPhone 15 Pro Max
        $iphone15ProMax->categories()->attach([$phoneCategory->id, $iphoneCategory->id]);

        // Create variants for iPhone 15 Pro Max
        $this->createIPhoneVariants($iphone15ProMax, $colors, $storages);

        // MacBook Pro 14"
        $macbookPro14 = Product::firstOrCreate(
            ['sku' => 'MBP14-M3'],
            [
                'name' => 'MacBook Pro 14" M3',
                'slug' => 'macbook-pro-14-m3',
                'description' => 'MacBook Pro 14 inch với chip M3 mới nhất',
                'short_description' => 'MacBook Pro 14" - Hiệu năng vượt trội',
                'price' => 49990000,
                'sale_price' => 47990000,
                'cost_price' => 38000000,
                'stock_quantity' => 20,
                'min_stock_level' => 5,
                'weight' => 1550,
                'dimensions' => json_encode(['length' => 312.6, 'width' => 220.7, 'height' => 15.5]),
                'image' => 'products/macbook-pro-14.jpg',
                'gallery' => json_encode([
                    'products/macbook-pro-14-1.jpg',
                    'products/macbook-pro-14-2.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => true,
                'is_variable' => true,
                'is_digital' => false,
                'meta_title' => 'MacBook Pro 14" M3 chính hãng',
                'meta_description' => 'Mua MacBook Pro 14" M3 giá tốt nhất, bảo hành 12 tháng',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );

        // Attach categories to MacBook Pro
        $macbookPro14->categories()->attach([$laptopCategory->id, $macbookCategory->id]);

        // Create variants for MacBook Pro
        $this->createMacBookVariants($macbookPro14, $colors, $rams, $storages);

        // Samsung Galaxy S24 Ultra
        $samsungS24 = Product::firstOrCreate(
            ['sku' => 'SSS24U'],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'description' => 'Samsung Galaxy S24 Ultra với S Pen và camera 200MP',
                'short_description' => 'Galaxy S24 Ultra - Camera 200MP',
                'price' => 28990000,
                'sale_price' => 26990000,
                'cost_price' => 22000000,
                'stock_quantity' => 25,
                'min_stock_level' => 5,
                'weight' => 233,
                'dimensions' => json_encode(['length' => 162.3, 'width' => 78.1, 'height' => 8.6]),
                'image' => 'products/samsung-s24-ultra.jpg',
                'gallery' => json_encode([
                    'products/samsung-s24-ultra-1.jpg',
                    'products/samsung-s24-ultra-2.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => true,
                'is_variable' => true,
                'is_digital' => false,
                'meta_title' => 'Samsung Galaxy S24 Ultra chính hãng',
                'meta_description' => 'Mua Samsung Galaxy S24 Ultra giá tốt nhất, bảo hành 12 tháng',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );

        // Attach categories to Samsung S24
        $samsungS24->categories()->attach([$phoneCategory->id]);

        // Create variants for Samsung S24
        $this->createSamsungVariants($samsungS24, $colors, $storages);

        // AirPods Pro 2
        $airpodsPro = Product::firstOrCreate(
            ['sku' => 'APP2'],
            [
                'name' => 'AirPods Pro 2',
                'slug' => 'airpods-pro-2',
                'description' => 'AirPods Pro thế hệ 2 với Active Noise Cancellation',
                'short_description' => 'AirPods Pro 2 - Chống ồn chủ động',
                'price' => 6990000,
                'sale_price' => 6490000,
                'cost_price' => 5000000,
                'stock_quantity' => 50,
                'min_stock_level' => 10,
                'weight' => 50.8,
                'dimensions' => json_encode(['length' => 45.2, 'width' => 60.9, 'height' => 21.7]),
                'image' => 'products/airpods-pro-2.jpg',
                'gallery' => json_encode([
                    'products/airpods-pro-2-1.jpg',
                    'products/airpods-pro-2-2.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => true,
                'is_variable' => false,
                'is_digital' => false,
                'meta_title' => 'AirPods Pro 2 chính hãng',
                'meta_description' => 'Mua AirPods Pro 2 giá tốt nhất, bảo hành 12 tháng',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );

        // Attach categories to AirPods
        $airpodsPro->categories()->attach([$accessoryCategory->id, $headphoneCategory->id]);

        // Apple Watch Series 9
        $appleWatch = Product::firstOrCreate(
            ['sku' => 'AWS9'],
            [
                'name' => 'Apple Watch Series 9',
                'slug' => 'apple-watch-series-9',
                'description' => 'Apple Watch Series 9 với chip S9 SiP',
                'short_description' => 'Apple Watch Series 9 - Sức khỏe theo dõi',
                'price' => 11990000,
                'sale_price' => 10990000,
                'cost_price' => 8500000,
                'stock_quantity' => 30,
                'min_stock_level' => 5,
                'weight' => 42.3,
                'dimensions' => json_encode(['length' => 45, 'width' => 38, 'height' => 10.7]),
                'image' => 'products/apple-watch-s9.jpg',
                'gallery' => json_encode([
                    'products/apple-watch-s9-1.jpg',
                    'products/apple-watch-s9-2.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => true,
                'is_variable' => true,
                'is_digital' => false,
                'meta_title' => 'Apple Watch Series 9 chính hãng',
                'meta_description' => 'Mua Apple Watch Series 9 giá tốt nhất, bảo hành 12 tháng',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );

        // Attach categories to Apple Watch
        $appleWatch->categories()->attach([$smartwatchCategory->id]);

        // Create variants for Apple Watch
        $this->createAppleWatchVariants($appleWatch, $colors, $screenSizes);

        // Create products with many variants
        $this->createMoreVariantProducts($phoneCategory, $laptopCategory, $accessoryCategory, $headphoneCategory, $tabletCategory, $colors, $storages, $rams, $materials, $headphoneTypes);
        
        // Create some simple products without variants
        $this->createSimpleProducts($phoneCategory, $laptopCategory, $accessoryCategory);

        $this->command->info('ProductSeeder completed successfully!');
    }

    private function createIPhoneVariants($product, $colors, $storages)
    {
        $colorValues = ['Đen', 'Trắng', 'Xanh dương', 'Xám tự nhiên', 'Vàng đồng'];
        $storageValues = ['128GB', '256GB', '512GB', '1TB'];
        
        $basePrice = $product->price;
        
        foreach ($colorValues as $index => $colorName) {
            foreach ($storageValues as $storageIndex => $storageName) {
                $color = $colors->where('value', $colorName)->first();
                $storage = $storages->where('value', $storageName)->first();
                
                if ($color && $storage) {
                    $priceAdjustment = $storageIndex * 5000000; // Add 5M for each storage upgrade
                    $variantPrice = $basePrice + $priceAdjustment;
                    $salePrice = $variantPrice - 5000000; // 5M discount
                    
                    $variant = ProductVariant::firstOrCreate(
                        ['sku' => $product->sku . '-' . $colorName . '-' . $storageName],
                        [
                            'product_id' => $product->id,
                            'name' => $product->name . ' ' . $colorName . ' ' . $storageName,
                            'price' => $variantPrice,
                            'sale_price' => $salePrice,
                            'stock_quantity' => rand(5, 20),
                            'weight' => $product->weight,
                            'image' => 'products/' . $product->slug . '-' . strtolower(str_replace(' ', '-', $colorName)) . '.jpg',
                            'status' => 'active',
                            'created_user_id' => 1,
                            'updated_user_id' => 1,
                        ]
                    );
                    
                    // Attach attributes to variant
                    ProductVariantAttribute::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'product_attribute_id' => $color->product_attribute_id,
                        ],
                        [
                            'product_attribute_value_id' => $color->id,
                        ]
                    );
                    
                    ProductVariantAttribute::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'product_attribute_id' => $storage->product_attribute_id,
                        ],
                        [
                            'product_attribute_value_id' => $storage->id,
                        ]
                    );
                }
            }
        }
    }

    private function createMacBookVariants($product, $colors, $rams, $storages)
    {
        $colorValues = ['Xám không gian', 'Bạc'];
        $ramValues = ['8GB', '16GB', '32GB'];
        $storageValues = ['512GB', '1TB', '2TB'];
        
        $basePrice = $product->price;
        
        foreach ($colorValues as $colorName) {
            foreach ($ramValues as $ramIndex => $ramName) {
                foreach ($storageValues as $storageIndex => $storageName) {
                    $color = $colors->where('value', $colorName)->first();
                    $ram = $rams->where('value', $ramName)->first();
                    $storage = $storages->where('value', $storageName)->first();
                    
                    if ($color && $ram && $storage) {
                        $priceAdjustment = ($ramIndex * 8000000) + ($storageIndex * 10000000);
                        $variantPrice = $basePrice + $priceAdjustment;
                        $salePrice = $variantPrice - 2000000;
                        
                        $variant = ProductVariant::firstOrCreate(
                            ['sku' => $product->slug . '-' . $ramName . '-' . $storageName . '-' . $colorName],
                            [
                                'product_id' => $product->id,
                                'name' => $product->name . ' ' . $ramName . ' ' . $storageName . ' ' . $colorName,
                                'price' => $variantPrice,
                                'sale_price' => $salePrice,
                                'stock_quantity' => rand(3, 10),
                                'weight' => $product->weight,
                                'image' => 'products/' . $product->slug . '-' . strtolower(str_replace(' ', '-', $colorName)) . '.jpg',
                                'status' => 'active',
                                'created_user_id' => 1,
                                'updated_user_id' => 1,
                            ]
                        );
                        
                        // Attach attributes to variant
                        ProductVariantAttribute::firstOrCreate(
                            [
                                'product_variant_id' => $variant->id,
                                'product_attribute_id' => $color->product_attribute_id,
                            ],
                            [
                                'product_attribute_value_id' => $color->id,
                            ]
                        );
                        
                        ProductVariantAttribute::firstOrCreate(
                            [
                                'product_variant_id' => $variant->id,
                                'product_attribute_id' => $ram->product_attribute_id,
                            ],
                            [
                                'product_attribute_value_id' => $ram->id,
                            ]
                        );
                        
                        ProductVariantAttribute::firstOrCreate(
                            [
                                'product_variant_id' => $variant->id,
                                'product_attribute_id' => $storage->product_attribute_id,
                            ],
                            [
                                'product_attribute_value_id' => $storage->id,
                            ]
                        );
                    }
                }
            }
        }
    }

    private function createSamsungVariants($product, $colors, $storages)
    {
        $colorValues = ['Đen', 'Xám violet', 'Vàng titan', 'Xanh titan'];
        $storageValues = ['256GB', '512GB', '1TB'];
        
        $basePrice = $product->price;
        
        foreach ($colorValues as $colorName) {
            foreach ($storageValues as $storageIndex => $storageName) {
                $color = $colors->where('value', $colorName)->first();
                $storage = $storages->where('value', $storageName)->first();
                
                if ($color && $storage) {
                    $priceAdjustment = $storageIndex * 4000000;
                    $variantPrice = $basePrice + $priceAdjustment;
                    $salePrice = $variantPrice - 2000000;
                    
                    $variant = ProductVariant::firstOrCreate(
                        ['sku' => $product->sku . '-' . $colorName . '-' . $storageName],
                        [
                            'product_id' => $product->id,
                            'name' => $product->name . ' ' . $colorName . ' ' . $storageName,
                            'price' => $variantPrice,
                            'sale_price' => $salePrice,
                            'stock_quantity' => rand(5, 15),
                            'weight' => $product->weight,
                            'image' => 'products/' . $product->slug . '-' . strtolower(str_replace(' ', '-', $colorName)) . '.jpg',
                            'status' => 'active',
                            'created_user_id' => 1,
                            'updated_user_id' => 1,
                        ]
                    );
                    
                    // Attach attributes to variant
                    ProductVariantAttribute::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'product_attribute_id' => $color->product_attribute_id,
                        ],
                        [
                            'product_attribute_value_id' => $color->id,
                        ]
                    );
                    
                    ProductVariantAttribute::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'product_attribute_id' => $storage->product_attribute_id,
                        ],
                        [
                            'product_attribute_value_id' => $storage->id,
                        ]
                    );
                }
            }
        }
    }

    private function createAppleWatchVariants($product, $colors, $screenSizes)
    {
        $colorValues = ['Đen', 'Hồng', 'Xanh navy'];
        $screenSizeValues = ['41mm', '45mm'];
        
        $basePrice = $product->price;
        
        foreach ($colorValues as $colorName) {
            foreach ($screenSizeValues as $screenIndex => $screenSizeName) {
                $color = $colors->where('value', $colorName)->first();
                $screenSize = $screenSizes->where('value', $screenSizeName)->first();
                
                if ($color && $screenSize) {
                    $priceAdjustment = $screenIndex * 1000000;
                    $variantPrice = $basePrice + $priceAdjustment;
                    $salePrice = $variantPrice - 1000000;
                    
                    $variant = ProductVariant::firstOrCreate(
                        ['sku' => $product->sku . '-' . $colorName . '-' . $screenSizeName],
                        [
                            'product_id' => $product->id,
                            'name' => $product->name . ' ' . $colorName . ' ' . $screenSizeName,
                            'price' => $variantPrice,
                            'sale_price' => $salePrice,
                            'stock_quantity' => rand(5, 15),
                            'weight' => $product->weight + ($screenIndex * 5),
                            'image' => 'products/' . $product->slug . '-' . strtolower(str_replace(' ', '-', $colorName)) . '.jpg',
                            'status' => 'active',
                            'created_user_id' => 1,
                            'updated_user_id' => 1,
                        ]
                    );
                    
                    // Attach attributes to variant
                    ProductVariantAttribute::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'product_attribute_id' => $color->product_attribute_id,
                        ],
                        [
                            'product_attribute_value_id' => $color->id,
                        ]
                    );
                    
                    ProductVariantAttribute::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'product_attribute_id' => $screenSize->product_attribute_id,
                        ],
                        [
                            'product_attribute_value_id' => $screenSize->id,
                        ]
                    );
                }
            }
        }
    }

    private function createSimpleProducts($phoneCategory, $laptopCategory, $accessoryCategory)
    {
        // Xiaomi Redmi Note 13
        $redmiNote13 = Product::firstOrCreate(
            ['sku' => 'RN13'],
            [
                'name' => 'Xiaomi Redmi Note 13',
                'slug' => 'xiaomi-redmi-note-13',
                'description' => 'Xiaomi Redmi Note 13 với camera 108MP',
                'short_description' => 'Redmi Note 13 - Camera 108MP',
                'price' => 5990000,
                'sale_price' => 5490000,
                'cost_price' => 4500000,
                'stock_quantity' => 30,
                'min_stock_level' => 5,
                'weight' => 188,
                'dimensions' => json_encode(['length' => 161.1, 'width' => 74.9, 'height' => 7.6]),
                'image' => 'products/redmi-note-13.jpg',
                'gallery' => json_encode([
                    'products/redmi-note-13-1.jpg',
                    'products/redmi-note-13-2.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => false,
                'is_variable' => false,
                'is_digital' => false,
                'meta_title' => 'Xiaomi Redmi Note 13 chính hãng',
                'meta_description' => 'Mua Xiaomi Redmi Note 13 giá tốt nhất',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );
        $redmiNote13->categories()->attach([$phoneCategory->id]);

        // Dell Inspiron 15
        $dellInspiron = Product::firstOrCreate(
            ['sku' => 'DI15'],
            [
                'name' => 'Dell Inspiron 15',
                'slug' => 'dell-inspiron-15',
                'description' => 'Dell Inspiron 15 cho văn phòng và học tập',
                'short_description' => 'Dell Inspiron 15 - Hiệu năng ổn định',
                'price' => 15990000,
                'sale_price' => 14990000,
                'cost_price' => 12000000,
                'stock_quantity' => 15,
                'min_stock_level' => 3,
                'weight' => 1750,
                'dimensions' => json_encode(['length' => 357.3, 'width' => 238.1, 'height' => 17.9]),
                'image' => 'products/dell-inspiron-15.jpg',
                'gallery' => json_encode([
                    'products/dell-inspiron-15-1.jpg',
                    'products/dell-inspiron-15-2.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => false,
                'is_variable' => false,
                'is_digital' => false,
                'meta_title' => 'Dell Inspiron 15 chính hãng',
                'meta_description' => 'Mua Dell Inspiron 15 giá tốt nhất',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );
        $dellInspiron->categories()->attach([$laptopCategory->id]);

        // Sạc dự phòng Anker 20000mAh
        $ankerPowerbank = Product::firstOrCreate(
            ['sku' => 'AP20K'],
            [
                'name' => 'Sạc dự phòng Anker 20000mAh',
                'slug' => 'sac-du-phong-anker-20000mah',
                'description' => 'Sạc dự phòng Anker 20000mAh với công nghệ PowerIQ',
                'short_description' => 'Anker PowerCore 20000 - Sạc nhanh',
                'price' => 1290000,
                'sale_price' => 990000,
                'cost_price' => 750000,
                'stock_quantity' => 50,
                'min_stock_level' => 10,
                'weight' => 357,
                'dimensions' => json_encode(['length' => 166, 'width' => 75, 'height' => 19]),
                'image' => 'products/anker-powercore-20000.jpg',
                'gallery' => json_encode([
                    'products/anker-powercore-20000-1.jpg',
                    'products/anker-powercore-20000-2.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => false,
                'is_variable' => false,
                'is_digital' => false,
                'meta_title' => 'Sạc dự phòng Anker 20000mAh',
                'meta_description' => 'Mua sạc dự phòng Anker 20000mAh giá tốt',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );
        $ankerPowerbank->categories()->attach([$accessoryCategory->id]);
    }

    private function createMoreVariantProducts($phoneCategory, $laptopCategory, $accessoryCategory, $headphoneCategory, $tabletCategory, $colors, $storages, $rams, $materials, $headphoneTypes)
    {
        // ASUS ROG Gaming Laptop
        $asusRog = Product::firstOrCreate(
            ['sku' => 'ASUS-ROG'],
            [
                'name' => 'ASUS ROG Strix G15',
                'slug' => 'asus-rog-strix-g15',
                'description' => 'ASUS ROG Strix G15 với GPU RTX 4060',
                'short_description' => 'ASUS ROG Strix G15 - Gaming laptop mạnh mẽ',
                'price' => 28990000,
                'sale_price' => 26990000,
                'cost_price' => 22000000,
                'stock_quantity' => 15,
                'min_stock_level' => 3,
                'weight' => 2300,
                'dimensions' => json_encode(['length' => 354, 'width' => 251, 'height' => 22.9]),
                'image' => 'products/asus-rog-strix-g15.jpg',
                'gallery' => json_encode([
                    'products/asus-rog-strix-g15-1.jpg',
                    'products/asus-rog-strix-g15-2.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => true,
                'is_variable' => true,
                'is_digital' => false,
                'meta_title' => 'ASUS ROG Strix G15 chính hãng',
                'meta_description' => 'Mua ASUS ROG Strix G15 giá tốt nhất',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );
        $asusRog->categories()->attach([$laptopCategory->id]);
        $this->createAsusRogVariants($asusRog, $colors, $rams, $storages);

        // Sony WH-1000XM5 Headphones
        $sonyHeadphones = Product::firstOrCreate(
            ['sku' => 'SONY-WH1000XM5'],
            [
                'name' => 'Sony WH-1000XM5',
                'slug' => 'sony-wh-1000xm5',
                'description' => 'Sony WH-1000XM5 với công nghệ chống ồn hàng đầu',
                'short_description' => 'Sony WH-1000XM5 - Chống ồn vượt trội',
                'price' => 8990000,
                'sale_price' => 7990000,
                'cost_price' => 6500000,
                'stock_quantity' => 25,
                'min_stock_level' => 5,
                'weight' => 250,
                'dimensions' => json_encode(['length' => 254, 'width' => 203, 'height' => 73]),
                'image' => 'products/sony-wh-1000xm5.jpg',
                'gallery' => json_encode([
                    'products/sony-wh-1000xm5-1.jpg',
                    'products/sony-wh-1000xm5-2.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => true,
                'is_variable' => true,
                'is_digital' => false,
                'meta_title' => 'Sony WH-1000XM5 chính hãng',
                'meta_description' => 'Mua Sony WH-1000XM5 giá tốt nhất',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );
        $sonyHeadphones->categories()->attach([$accessoryCategory->id, $headphoneCategory->id]);
        $this->createSonyHeadphoneVariants($sonyHeadphones, $colors, $materials);

        // iPad Pro 12.9"
        $ipadPro = Product::firstOrCreate(
            ['sku' => 'IPAD-PRO-129'],
            [
                'name' => 'iPad Pro 12.9" M2',
                'slug' => 'ipad-pro-12-9-m2',
                'description' => 'iPad Pro 12.9 inch với chip M2',
                'short_description' => 'iPad Pro 12.9" - Mạnh mẽ như máy tính',
                'price' => 24990000,
                'sale_price' => 23990000,
                'cost_price' => 19000000,
                'stock_quantity' => 20,
                'min_stock_level' => 5,
                'weight' => 682,
                'dimensions' => json_encode(['length' => 280.6, 'width' => 214.9, 'height' => 6.4]),
                'image' => 'products/ipad-pro-12-9.jpg',
                'gallery' => json_encode([
                    'products/ipad-pro-12-9-1.jpg',
                    'products/ipad-pro-12-9-2.jpg'
                ]),
                'status' => ProductStatus::ACTIVE->value,
                'is_featured' => true,
                'is_variable' => true,
                'is_digital' => false,
                'meta_title' => 'iPad Pro 12.9" M2 chính hãng',
                'meta_description' => 'Mua iPad Pro 12.9" M2 giá tốt nhất',
                'created_user_id' => 1,
                'updated_user_id' => 1,
            ]
        );
        $ipadPro->categories()->attach([$tabletCategory->id]);
        $this->createIpadProVariants($ipadPro, $colors, $storages);
    }

    private function createAsusRogVariants($product, $colors, $rams, $storages)
    {
        $colorValues = ['Đen', 'Xám'];
        $ramValues = ['16GB', '32GB'];
        $storageValues = ['512GB SSD', '1TB SSD', '2TB SSD'];
        
        $basePrice = $product->price;
        
        foreach ($colorValues as $colorName) {
            foreach ($ramValues as $ramIndex => $ramName) {
                foreach ($storageValues as $storageIndex => $storageName) {
                    $color = $colors->where('value', $colorName)->first();
                    $ram = $rams->where('value', $ramName)->first();
                    $storage = $storages->where('value', $storageName)->first();
                    
                    if ($color && $ram && $storage) {
                        $priceAdjustment = ($ramIndex * 5000000) + ($storageIndex * 6000000);
                        $variantPrice = $basePrice + $priceAdjustment;
                        $salePrice = $variantPrice - 2000000;
                        
                        $variant = ProductVariant::firstOrCreate(
                            ['sku' => $product->sku . '-' . $ramName . '-' . $storageName . '-' . $colorName],
                            [
                                'product_id' => $product->id,
                                'name' => $product->name . ' ' . $ramName . ' ' . $storageName . ' ' . $colorName,
                                'price' => $variantPrice,
                                'sale_price' => $salePrice,
                                'stock_quantity' => rand(2, 8),
                                'weight' => $product->weight,
                                'image' => 'products/' . $product->slug . '-' . strtolower(str_replace(' ', '-', $colorName)) . '.jpg',
                                'status' => 'active',
                                'created_user_id' => 1,
                                'updated_user_id' => 1,
                            ]
                        );
                        
                        // Attach attributes to variant
                        ProductVariantAttribute::firstOrCreate(
                            [
                                'product_variant_id' => $variant->id,
                                'product_attribute_id' => $color->product_attribute_id,
                            ],
                            [
                                'product_attribute_value_id' => $color->id,
                            ]
                        );
                        
                        ProductVariantAttribute::firstOrCreate(
                            [
                                'product_variant_id' => $variant->id,
                                'product_attribute_id' => $ram->product_attribute_id,
                            ],
                            [
                                'product_attribute_value_id' => $ram->id,
                            ]
                        );
                        
                        ProductVariantAttribute::firstOrCreate(
                            [
                                'product_variant_id' => $variant->id,
                                'product_attribute_id' => $storage->product_attribute_id,
                            ],
                            [
                                'product_attribute_value_id' => $storage->id,
                            ]
                        );
                    }
                }
            }
        }
    }

    private function createSonyHeadphoneVariants($product, $colors, $materials)
    {
        $colorValues = ['Đen', 'Bạc'];
        $materialValues = ['Da', 'Vải'];
        
        $basePrice = $product->price;
        
        foreach ($colorValues as $colorName) {
            foreach ($materialValues as $materialIndex => $materialName) {
                $color = $colors->where('value', $colorName)->first();
                $material = $materials->where('value', $materialName)->first();
                
                if ($color && $material) {
                    $priceAdjustment = $materialIndex * 500000;
                    $variantPrice = $basePrice + $priceAdjustment;
                    $salePrice = $variantPrice - 1000000;
                    
                    $variant = ProductVariant::firstOrCreate(
                        ['sku' => $product->sku . '-' . $colorName . '-' . $materialName],
                        [
                            'product_id' => $product->id,
                            'name' => $product->name . ' ' . $colorName . ' ' . $materialName,
                            'price' => $variantPrice,
                            'sale_price' => $salePrice,
                            'stock_quantity' => rand(5, 15),
                            'weight' => $product->weight + ($materialIndex * 10),
                            'image' => 'products/' . $product->slug . '-' . strtolower(str_replace(' ', '-', $colorName)) . '.jpg',
                            'status' => 'active',
                            'created_user_id' => 1,
                            'updated_user_id' => 1,
                        ]
                    );
                    
                    // Attach attributes to variant
                    ProductVariantAttribute::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'product_attribute_id' => $color->product_attribute_id,
                        ],
                        [
                            'product_attribute_value_id' => $color->id,
                        ]
                    );
                    
                    ProductVariantAttribute::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'product_attribute_id' => $material->product_attribute_id,
                        ],
                        [
                            'product_attribute_value_id' => $material->id,
                        ]
                    );
                }
            }
        }
    }

    private function createIpadProVariants($product, $colors, $storages)
    {
        $colorValues = ['Xám không gian', 'Bạc'];
        $storageValues = ['128GB', '256GB', '512GB', '1TB', '2TB'];
        
        $basePrice = $product->price;
        
        foreach ($colorValues as $colorName) {
            foreach ($storageValues as $storageIndex => $storageName) {
                $color = $colors->where('value', $colorName)->first();
                $storage = $storages->where('value', $storageName)->first();
                
                if ($color && $storage) {
                    $priceAdjustment = $storageIndex * 4000000;
                    $variantPrice = $basePrice + $priceAdjustment;
                    $salePrice = $variantPrice - 1000000;
                    
                    $variant = ProductVariant::firstOrCreate(
                        ['sku' => $product->sku . '-' . $colorName . '-' . $storageName],
                        [
                            'product_id' => $product->id,
                            'name' => $product->name . ' ' . $colorName . ' ' . $storageName,
                            'price' => $variantPrice,
                            'sale_price' => $salePrice,
                            'stock_quantity' => rand(3, 10),
                            'weight' => $product->weight,
                            'image' => 'products/' . $product->slug . '-' . strtolower(str_replace(' ', '-', $colorName)) . '.jpg',
                            'status' => 'active',
                            'created_user_id' => 1,
                            'updated_user_id' => 1,
                        ]
                    );
                    
                    // Attach attributes to variant
                    ProductVariantAttribute::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'product_attribute_id' => $color->product_attribute_id,
                        ],
                        [
                            'product_attribute_value_id' => $color->id,
                        ]
                    );
                    
                    ProductVariantAttribute::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'product_attribute_id' => $storage->product_attribute_id,
                        ],
                        [
                            'product_attribute_value_id' => $storage->id,
                        ]
                    );
                }
            }
        }
    }
}