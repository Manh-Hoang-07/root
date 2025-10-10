# Hệ Thống Cấu Hình Hệ Thống (System Configuration System)

## 📋 Tổng Quan

Hệ thống cấu hình động cho phép quản lý các cài đặt hệ thống một cách linh hoạt mà không cần restart server. Hệ thống hỗ trợ cả admin quản lý và người dùng/hệ thống sử dụng.

## 🏗️ Kiến Trúc Hệ Thống

### Cấu Trúc Thư Mục
```
app/
├── Models/
│   ├── SystemConfig.php
│   ├── ConfigPermission.php
│   └── ConfigAuditLog.php
├── Services/Core/SystemConfig/
│   ├── SystemConfigService.php
│   ├── ConfigValidationService.php
│   ├── ConfigCacheService.php
│   └── ConfigAuditService.php
├── Repositories/SystemConfig/
│   └── SystemConfigRepository.php
├── Http/Controllers/Api/
│   ├── Admin/Config/
│   │   ├── SystemConfigController.php
│   │   └── ConfigAuditController.php
│   └── Public/Config/
│       └── SystemConfigController.php
├── Http/Requests/Core/Config/
│   ├── SystemConfigRequest.php
│   └── BulkUpdateConfigRequest.php
└── Enums/
    ├── ConfigType.php
    ├── ConfigGroup.php
    └── ConfigAction.php
```

## 🗄️ Database Schema

### Bảng `system_configs`
- `id`: Primary key
- `key`: Khóa cấu hình (unique)
- `value`: Giá trị cấu hình
- `type`: Loại dữ liệu (string, integer, boolean, json, array, float)
- `group`: Nhóm cấu hình
- `description`: Mô tả cấu hình
- `is_public`: Có thể truy cập public không
- `is_encrypted`: Có mã hóa không
- `validation_rules`: Rules validation (JSON)
- `default_value`: Giá trị mặc định
- `is_active`: Trạng thái hoạt động
- `sort_order`: Thứ tự sắp xếp

### Bảng `config_permissions`
- `id`: Primary key
- `user_id`: ID người dùng
- `config_group`: Nhóm cấu hình được phép
- `can_read`: Quyền đọc
- `can_write`: Quyền ghi
- `can_delete`: Quyền xóa
- `allowed_keys`: Danh sách key được phép
- `restricted_keys`: Danh sách key bị hạn chế

### Bảng `config_audit_logs`
- `id`: Primary key
- `config_key`: Khóa cấu hình
- `old_value`: Giá trị cũ
- `new_value`: Giá trị mới
- `action`: Hành động (created, updated, deleted)
- `changed_by`: Người thay đổi
- `change_reason`: Lý do thay đổi
- `ip_address`: Địa chỉ IP
- `user_agent`: User Agent
- `metadata`: Dữ liệu bổ sung

## 🚀 API Endpoints

### Public API (Không cần authentication)

#### Lấy danh sách nhóm cấu hình public
```
GET /api/config/groups
```

#### Lấy cấu hình theo nhóm (sử dụng index với filter)
```
GET /api/config/?group=general
```

#### Lấy cấu hình theo key
```
GET /api/config/key?key=app.name
```

#### Lấy nhiều cấu hình theo keys (sử dụng index với filter)
```
GET /api/config/?key[]=app.name&key[]=app.version
```

#### Lấy tất cả cấu hình public (sử dụng index)
```
GET /api/config/
```

#### Lấy cấu hình với pagination và field selection
```
GET /api/config/?per_page=10&fields=key,value,description
```

### Admin API (Cần authentication + role admin)

#### Quản lý cấu hình
```
GET    /api/admin/config/groups          # Lấy danh sách nhóm
GET    /api/admin/config/group           # Lấy cấu hình theo nhóm
GET    /api/admin/config/key             # Lấy cấu hình theo key
GET    /api/admin/config/                # Danh sách cấu hình (có phân trang, filters)
POST   /api/admin/config/store           # Tạo/cập nhật cấu hình
POST   /api/admin/config/bulk-update     # Cập nhật hàng loạt
POST   /api/admin/config/clear-cache     # Xóa cache
```

#### Quản lý Audit Logs
```
GET    /api/admin/config-audit/config-logs    # Lấy logs theo config
GET    /api/admin/config-audit/user-logs      # Lấy logs theo user
GET    /api/admin/config-audit/date-range     # Lấy logs theo ngày
GET    /api/admin/config-audit/statistics     # Thống kê audit
POST   /api/admin/config-audit/clean-old      # Xóa logs cũ
GET    /api/admin/config-audit/export         # Export logs
```

## 💻 Cách Sử Dụng

### 1. Lấy cấu hình trong code

```php
use App\Models\SystemConfig;

// Lấy cấu hình theo key
$appName = SystemConfig::getByKey('app.name', 'Default App Name');

// Lấy cấu hình với type casting
$config = SystemConfig::where('key', 'app.debug')->first();
$isDebug = $config->getTypedValue(); // boolean

// Lấy cấu hình public
$publicConfigs = SystemConfig::active()->public()->get();
```

### 2. Tạo/cập nhật cấu hình

```php
use App\Models\SystemConfig;
use App\Enums\ConfigType;
use App\Enums\ConfigGroup;

// Tạo cấu hình mới
SystemConfig::setByKey('custom.setting', 'value', ConfigType::STRING, ConfigGroup::CUSTOM);

// Hoặc sử dụng Service
use App\Services\Core\SystemConfig\SystemConfigService;

$configService = app(SystemConfigService::class);
$result = $configService->createOrUpdate([
    'key' => 'custom.setting',
    'value' => 'new value',
    'type' => 'string',
    'group' => 'custom',
    'description' => 'Custom setting description',
    'is_public' => true,
]);
```

### 3. Sử dụng trong Controller

```php
use App\Services\Core\SystemConfig\SystemConfigService;

class MyController extends Controller
{
    protected $configService;

    public function __construct(SystemConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function index()
    {
        // Lấy cấu hình
        $appName = $this->configService->getByKey('app.name');
        $isDebug = $this->configService->getByKey('app.debug', false);
        
        return view('welcome', compact('appName', 'isDebug'));
    }
}
```

## 🔧 Cấu Hình Mặc Định

Hệ thống đã được seed với các cấu hình mặc định:

### General Settings
- `app.name`: Tên ứng dụng
- `app.version`: Phiên bản ứng dụng
- `app.debug`: Chế độ debug
- `app.timezone`: Múi giờ hệ thống

### Email Settings
- `mail.driver`: Driver gửi email
- `mail.host`: SMTP Host
- `mail.port`: SMTP Port
- `mail.username`: SMTP Username (encrypted)
- `mail.password`: SMTP Password (encrypted)

### API Settings
- `api.rate_limit`: Giới hạn request per minute
- `api.timeout`: Timeout cho API requests
- `api.cors_enabled`: Bật CORS cho API

### Cache Settings
- `cache.default`: Cache driver mặc định
- `cache.ttl`: Cache TTL mặc định

### Security Settings
- `security.password_min_length`: Độ dài tối thiểu mật khẩu
- `security.session_timeout`: Timeout session
- `security.max_login_attempts`: Số lần đăng nhập sai tối đa

### Storage Settings
- `storage.disk`: Storage disk mặc định
- `storage.max_file_size`: Kích thước file tối đa
- `storage.allowed_extensions`: Định dạng file được phép

### Notification Settings
- `notification.email_enabled`: Bật thông báo email
- `notification.sms_enabled`: Bật thông báo SMS

### Custom Settings
- `custom.maintenance_mode`: Chế độ bảo trì
- `custom.maintenance_message`: Thông báo bảo trì

## 🔒 Bảo Mật

### Mã Hóa Dữ Liệu
- Các cấu hình nhạy cảm (passwords, API keys) được mã hóa tự động
- Sử dụng Laravel's encryption system
- Chỉ admin mới có thể xem giá trị đã mã hóa

### Phân Quyền
- Admin có toàn quyền quản lý cấu hình
- Có thể cấp quyền chi tiết cho từng nhóm cấu hình
- Public configs có thể truy cập mà không cần authentication

### Audit Logging
- Mọi thay đổi cấu hình đều được ghi log
- Theo dõi người thay đổi, thời gian, IP address
- Có thể export logs để phân tích

## ⚡ Performance

### Caching Strategy
- **L1 Cache**: Redis cho cấu hình thường dùng
- **L2 Cache**: File cache cho cấu hình ít thay đổi
- **L3 Cache**: Database query cache
- Smart cache invalidation khi có thay đổi

### Cache Keys
- `config_key_{key}`: Cache cho từng config key
- `config_group_{group}`: Cache cho từng nhóm config
- `config_public_all`: Cache cho tất cả public configs

## 🧪 Testing

### Chạy Migration và Seeder
```bash
# Chạy migration
php artisan migrate

# Chạy seeder
php artisan db:seed --class=SystemConfigSeeder

# Hoặc chạy tất cả
php artisan migrate:fresh --seed
```

### Test API
```bash
# Test public API
curl -X GET "http://localhost:8000/api/config/groups"

# Test admin API (cần token)
curl -X GET "http://localhost:8000/api/admin/config/statistics" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 📊 Monitoring

### Thống Kê Cấu Hình
- Tổng số cấu hình
- Số cấu hình active/public/encrypted
- Phân bố theo nhóm và loại
- Thống kê truy cập

### Audit Reports
- Lịch sử thay đổi cấu hình
- Thống kê theo người dùng
- Báo cáo theo thời gian
- Export dữ liệu

## 🔄 Maintenance

### Dọn Dẹp Cache
```php
use App\Services\Core\SystemConfig\SystemConfigService;

$configService = app(SystemConfigService::class);
$configService->clearAllCache();
```

### Dọn Dẹp Audit Logs
```php
use App\Services\Core\SystemConfig\ConfigAuditService;

$auditService = app(ConfigAuditService::class);
$deletedCount = $auditService->cleanOldLogs(90); // Xóa logs cũ hơn 90 ngày
```

## 🚨 Troubleshooting

### Lỗi Thường Gặp

1. **Cache không hoạt động**
   - Kiểm tra Redis connection
   - Chạy `php artisan config:cache`

2. **Migration lỗi**
   - Kiểm tra foreign key constraints
   - Chạy `php artisan migrate:fresh`

3. **Validation lỗi**
   - Kiểm tra type của value
   - Xem validation rules trong database

### Debug Commands
```bash
# Kiểm tra cache
php artisan tinker
>>> app('App\Services\Core\SystemConfig\ConfigCacheService')->isCacheWorking()

# Kiểm tra config
>>> App\Models\SystemConfig::getByKey('app.name')
```

## 📝 Changelog

### Version 1.0.0
- ✅ Hệ thống cấu hình động hoàn chỉnh
- ✅ API cho admin và public
- ✅ Caching và performance optimization
- ✅ Audit logging và security
- ✅ Validation và error handling
- ✅ Documentation và examples

---

**Tác giả**: AI Assistant  
**Ngày tạo**: 2025-10-08  
**Phiên bản**: 1.0.0

