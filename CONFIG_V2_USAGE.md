# Hướng dẫn sử dụng hệ thống cấu hình API V2 (Đơn giản)

## Tổng quan
Hệ thống cấu hình V2 đơn giản chỉ dùng tạm thời trong quá trình phát triển, chỉ hỗ trợ cấu hình hệ thống chung và email.

## Cách sử dụng

### 1. Sử dụng ConfigV2Helper

```php
use App\Helpers\ConfigV2Helper;

// Lấy cấu hình theo key
$appName = ConfigV2Helper::get('app_name', 'Laravel App');

// Lấy cấu hình general
$generalConfig = ConfigV2Helper::getGeneralConfig();

// Lấy cấu hình email
$emailConfig = ConfigV2Helper::getEmailConfig();

// General config helpers - Thông tin website cơ bản
$appName = ConfigV2Helper::getAppName();
$siteName = ConfigV2Helper::getSiteName();
$siteLogo = ConfigV2Helper::getSiteLogo();
$siteFavicon = ConfigV2Helper::getSiteFavicon();
$siteEmail = ConfigV2Helper::getSiteEmail();
$sitePhone = ConfigV2Helper::getSitePhone();
$siteAddress = ConfigV2Helper::getSiteAddress();
$siteDescription = ConfigV2Helper::getSiteDescription();
$timezone = ConfigV2Helper::getTimezone();
$language = ConfigV2Helper::getLanguage();
$maintenanceMode = ConfigV2Helper::getMaintenanceMode();
$itemsPerPage = ConfigV2Helper::getItemsPerPage();

// Email config helpers
$smtpHost = ConfigV2Helper::getSmtpHost();
$smtpPort = ConfigV2Helper::getSmtpPort();
$smtpUsername = ConfigV2Helper::getSmtpUsername();
$smtpPassword = ConfigV2Helper::getSmtpPassword();
$smtpEncryption = ConfigV2Helper::getSmtpEncryption();
$fromEmail = ConfigV2Helper::getFromEmail();
$fromName = ConfigV2Helper::getFromName();
```

### 2. Sử dụng ConfigV2Facade

```php
use App\Facades\ConfigV2Facade;

// Tương tự như ConfigV2Helper
$appName = ConfigV2Facade::get('app_name');
$generalConfig = ConfigV2Facade::getGeneralConfig();
$emailConfig = ConfigV2Facade::getEmailConfig();
```

### 3. Sử dụng trong Controllers

```php
class ProductController extends Controller
{
    public function index()
    {
        // Lấy cấu hình hệ thống
        $generalConfig = ConfigV2Helper::getGeneralConfig();
        
        return response()->json([
            'data' => $products,
            'config' => [
                'general' => $generalConfig
            ]
        ]);
    }
}
```

### 4. Sử dụng trong Services

```php
class EmailService
{
    public function sendWelcomeEmail($user)
    {
        $emailConfig = ConfigV2Helper::getEmailConfig();
        
        // Sử dụng cấu hình email
        $smtpHost = $emailConfig['smtp_host'] ?? 'localhost';
        $smtpPort = $emailConfig['smtp_port'] ?? 587;
        
        // Gửi email...
    }
}
```

## API Endpoints

### Public Endpoints (Không cần authentication)

```
GET /api/config-v2/general - Lấy cấu hình hệ thống chung
GET /api/config-v2/email - Lấy cấu hình email
GET /api/config-v2/key?key=app_name - Lấy cấu hình theo key
```

### Ví dụ sử dụng API

```javascript
// Lấy cấu hình hệ thống chung
fetch('/api/config-v2/general')
    .then(response => response.json())
    .then(data => {
        console.log(data.data); // Cấu hình hệ thống
    });

// Lấy cấu hình email
fetch('/api/config-v2/email')
    .then(response => response.json())
    .then(data => {
        console.log(data.data); // Cấu hình email
    });

// Lấy cấu hình theo key
fetch('/api/config-v2/key?key=app_name')
    .then(response => response.json())
    .then(data => {
        console.log(data.data); // Giá trị của app_name
    });
```

### Ví dụ sử dụng trong API Response

```php
// Trong Controller
public function getProductWithConfig($id)
{
    $product = Product::find($id);
    $generalConfig = ConfigV2Helper::getGeneralConfig();
    
    return response()->json([
        'success' => true,
        'data' => $product,
        'config' => [
            'general' => $generalConfig
        ]
    ]);
}
```

## Các loại dữ liệu hỗ trợ

- `string`: Chuỗi văn bản
- `number`: Số (int/float)
- `boolean`: True/False
- `json`: Dữ liệu JSON (sẽ được encode/decode tự động)
- `text`: Văn bản dài

## Các groups cấu hình

- `general`: Cấu hình thông tin website cơ bản (site_name, site_logo, site_email, site_phone, site_address, site_description, timezone, language, maintenance_mode...)
- `email`: Cấu hình email (SMTP settings, from_email, from_name...)

## Lưu ý

1. Hệ thống này chỉ dùng tạm thời trong quá trình phát triển
2. Tất cả cấu hình đều được cache trong 1 giờ (3600 giây)
3. Cấu hình public có thể truy cập từ frontend mà không cần authentication
4. Sử dụng `ConfigV2Helper` thay vì truy cập trực tiếp model để có hiệu suất tốt hơn
5. Phù hợp cho API project, không có view rendering
6. Hỗ trợ nhiều loại dữ liệu với auto casting
7. Có các helper methods tiện ích cho các cấu hình thường dùng
