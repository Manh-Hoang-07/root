# Hệ Thống SystemConfig Thống Nhất

## Tổng Quan

Hệ thống SystemConfig đã được chuẩn hóa và thống nhất từ 2 hệ thống cũ (SystemConfig và SystemConfigV2) thành một hệ thống duy nhất với đầy đủ chức năng.

## Cấu Trúc Mới

### 1. Service Layer
- **Admin Service**: `app/Services/Admin/SystemConfig/SystemConfigService.php`
  - Kế thừa từ `BaseService`
  - Quản lý cấu hình cho Admin
  - CRUD operations, bulk operations, search
  - Cache management và logging
- **Public Service**: `app/Services/Public/SystemConfig/SystemConfigService.php`
  - Quản lý cấu hình cho Public
  - Chỉ đọc dữ liệu, không có quyền sửa
  - Cache optimization cho frontend

### 2. Repository Layer
- **File**: `app/Repositories/SystemConfig/SystemConfigRepository.php`
- **Chức năng**:
  - Data access layer
  - Cache management
  - Query optimization

### 3. Controller Layer
- **Admin Controller**: `app/Http/Controllers/Api/Admin/SystemConfig/SystemConfigController.php`
  - API endpoints cho Admin
  - CRUD operations, bulk operations, search
  - Validation và response formatting
- **Public Controller**: `app/Http/Controllers/Api/Public/SystemConfig/SystemConfigController.php`
  - API endpoints cho Public
  - Chỉ đọc dữ liệu, không có quyền sửa
  - Tương thích với Config V2

## API Endpoints

### Public Endpoints (Không cần authentication)

#### Lấy cấu hình
```http
GET /api/config/public
GET /api/config/group/{group}
POST /api/config/groups
GET /api/config/key?key={key}
POST /api/config/keys
```

#### Config V2 Compatibility
```http
GET /api/config/general
GET /api/config/email
GET /api/config/by-key?key={key}
```

### Admin Endpoints (Cần authentication + role admin)

#### CRUD Operations
```http
GET /api/admin/system-configs
GET /api/admin/system-configs/{id}
POST /api/admin/system-configs
PUT /api/admin/system-configs/{id}
DELETE /api/admin/system-configs/{id}
```

#### Group Management
```http
GET /api/admin/system-configs/group/{group}
PUT /api/admin/system-configs/group/{group}
GET /api/admin/system-configs/group/{group}/form
```

#### Bulk Operations
```http
POST /api/admin/system-configs/bulk-update
POST /api/admin/system-configs/bulk-delete
```

#### Search & Filter
```http
GET /api/admin/system-configs/search
GET /api/admin/system-configs/groups/list
```

#### Cache Management
```http
POST /api/admin/system-configs/clear-cache
```

#### Config V2 Admin
```http
POST /api/admin/config-v2/general
POST /api/admin/config-v2/email
POST /api/admin/config-v2/key
```

## Cách Sử Dụng

### 1. Lấy cấu hình trong code

```php
use App\Services\SystemConfigService;

$configService = app(SystemConfigService::class);

// Lấy config theo key
$value = $configService->getByKey('site_name', 'Default Site');

// Lấy config theo group
$configs = $configService->getGroupAsArray('general');

// Lấy config public
$publicConfigs = $configService->getGeneralConfig();
```

### 2. Cập nhật cấu hình

```php
// Cập nhật theo group
$configService->updateGroup('general', [
    'site_name' => 'New Site Name',
    'site_email' => 'new@example.com'
]);

// Cập nhật theo key
$configService->updateByKeyResponse('site_name', 'New Site Name');
```

### 3. Sử dụng trong Controller

```php
use App\Services\SystemConfigService;

class MyController extends Controller
{
    protected $configService;

    public function __construct(SystemConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function index()
    {
        $siteName = $this->configService->getByKey('site_name');
        $generalConfig = $this->configService->getGeneralConfig();
        
        return view('index', compact('siteName', 'generalConfig'));
    }
}
```

## Cache Management

Hệ thống tự động quản lý cache với các key patterns:
- `system_config_key_{key}` - Cache cho từng key
- `system_configs_group_{group}` - Cache cho group
- `system_configs_group_array_{group}` - Cache cho group dạng array
- `public_general_config` - Cache cho config public
- `system_configs_public` - Cache cho tất cả config public

### Xóa cache
```php
// Xóa tất cả cache
$configService->clearCache();

// Xóa cache trong controller
POST /api/admin/system-configs/clear-cache
```

## Migration từ hệ thống cũ

### 1. Thay đổi import
```php
// Cũ
use App\Services\Admin\SystemConfig\SystemConfigService;
use App\Services\Public\SystemConfig\SystemConfigService;

// Mới - Vẫn giữ cấu trúc thư mục
use App\Services\Admin\SystemConfig\SystemConfigService;
use App\Services\Public\SystemConfig\SystemConfigService;
// Nhưng sử dụng repository thống nhất
use App\Repositories\SystemConfigRepository;
```

### 2. Thay đổi controller
```php
// Cũ
use App\Http\Controllers\Api\Admin\SystemConfig\SystemConfigService;
use App\Http\Controllers\Api\Public\SystemConfig\SystemConfigService;

// Mới - Vẫn giữ cấu trúc thư mục
use App\Http\Controllers\Api\Admin\SystemConfig\SystemConfigController;
use App\Http\Controllers\Api\Public\SystemConfig\SystemConfigController;
// Nhưng sử dụng service thống nhất
use App\Services\SystemConfigService;
```

### 3. Thay đổi repository
```php
// Cũ
use App\Repositories\SystemConfig\SystemConfigRepository;
use App\Repositories\SystemConfigV2\SystemConfigV2Repository;

// Mới - Vẫn giữ cấu trúc thư mục
use App\Repositories\SystemConfig\SystemConfigRepository;
```

## Lợi Ích

1. **Thống nhất**: Một hệ thống duy nhất thay vì 2 hệ thống song song
2. **Tối ưu**: Cache thông minh và query optimization
3. **Linh hoạt**: Hỗ trợ cả Admin và Public operations
4. **Dễ bảo trì**: Code tập trung, dễ debug và maintain
5. **Tương thích**: Vẫn hỗ trợ API cũ để không break existing code
6. **Mở rộng**: Dễ dàng thêm tính năng mới

## Lưu Ý

- Tất cả API cũ vẫn hoạt động bình thường
- Cache được tự động quản lý
- Logging được tích hợp sẵn cho audit trail
- Validation được áp dụng cho tất cả operations
- Error handling được chuẩn hóa
