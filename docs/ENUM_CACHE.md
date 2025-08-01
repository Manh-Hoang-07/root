# Enum Cache Documentation

## Tổng quan

API Enum đã được cấu hình cache trong 24 giờ (86400 giây) để tối ưu hiệu suất. Cache sẽ tự động được clear khi có thay đổi trong database.

## Cách hoạt động

### 1. Cache tự động
- Khi gọi API `/api/enums/{type}`, dữ liệu sẽ được cache trong 24 giờ
- Cache key format: `enums_{type}`
- Ví dụ: `enums_user_status`, `enums_gender`, etc.

### 2. Các enum types được hỗ trợ
- `user_status` - Trạng thái user
- `gender` - Giới tính
- `basic_status` - Trạng thái cơ bản
- `role_status` - Trạng thái role
- `product_status` - Trạng thái sản phẩm

## API Endpoints

### Lấy enum data (Public)
```http
GET /api/enums/{type}
```

### Clear cache cho enum type cụ thể (Admin only)
```http
DELETE /api/admin/enums/{type}/cache
```

### Clear tất cả cache enum (Admin only)
```http
DELETE /api/admin/enums/cache/all
```

## Artisan Commands

### Clear cache cho enum type cụ thể
```bash
php artisan cache:clear-enums user_status
```

### Clear tất cả cache enum
```bash
php artisan cache:clear-enums
```

## Tự động clear cache

Cache sẽ tự động được clear khi:
- User được tạo/cập nhật → clear `user_status` cache
- Role được tạo/cập nhật → clear `role_status` cache  
- Product được tạo/cập nhật → clear `product_status` cache

## Sử dụng trong code

### Clear cache programmatically
```php
use App\Providers\EnumCacheServiceProvider;

// Clear cache cho enum type cụ thể
$cacheKey = "enums_user_status";
Cache::forget($cacheKey);

// Clear tất cả cache enum
EnumCacheServiceProvider::clearAllEnumCache();
```

### Kiểm tra cache
```php
// Kiểm tra cache có tồn tại không
$cacheKey = "enums_user_status";
if (Cache::has($cacheKey)) {
    $data = Cache::get($cacheKey);
}
```

## Lưu ý

1. Cache chỉ áp dụng cho các enum type hợp lệ
2. Error responses không được cache
3. Cache sẽ tự động expire sau 24 giờ
4. Admin có thể manually clear cache khi cần thiết
5. Cache được clear tự động khi có thay đổi trong database 