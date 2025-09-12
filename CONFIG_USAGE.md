# Hướng dẫn sử dụng hệ thống cấu hình API

## Tổng quan
Hệ thống cấu hình đơn giản cho phép bạn lấy thông tin cấu hình từ bất kỳ đâu trong API.

## Cách sử dụng

### 1. Sử dụng ConfigHelper

```php
use App\Helpers\ConfigHelper;

// Lấy cấu hình theo key
$siteName = ConfigHelper::get('site_name', 'Tên mặc định');

// Lấy cấu hình theo group
$websiteConfig = ConfigHelper::getGroup('website');

// Lấy cấu hình public
$publicConfigs = ConfigHelper::getPublicConfigs();

// Lấy nhiều cấu hình cùng lúc
$configs = ConfigHelper::getMultiple(['site_name', 'site_email', 'site_phone']);

// Lấy nhiều groups
$configs = ConfigHelper::getMultipleGroups(['website', 'email', 'payment']);
```

### 2. Sử dụng ConfigFacade

```php
use App\Facades\ConfigFacade;

// Tương tự như ConfigHelper
$siteName = ConfigFacade::get('site_name');
$websiteConfig = ConfigFacade::getGroup('website');
```

### 3. Sử dụng trong Controllers

```php
class ProductController extends Controller
{
    public function index()
    {
        // Lấy cấu hình website
        $websiteConfig = ConfigHelper::getWebsiteConfig();
        
        // Lấy cấu hình SEO
        $seoConfig = ConfigHelper::getSeoConfig();
        
        return response()->json([
            'data' => $products,
            'config' => [
                'website' => $websiteConfig,
                'seo' => $seoConfig
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
        $emailConfig = ConfigHelper::getEmailConfig();
        
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
GET /api/config/public - Lấy tất cả cấu hình public
GET /api/config/group/{group} - Lấy cấu hình theo group
POST /api/config/groups - Lấy cấu hình nhiều groups
GET /api/config/key?key=site_name - Lấy cấu hình theo key
POST /api/config/keys - Lấy cấu hình nhiều keys
```

### Ví dụ sử dụng API

```javascript
// Lấy cấu hình public
fetch('/api/config/public')
    .then(response => response.json())
    .then(data => {
        console.log(data.data); // Cấu hình public
    });

// Lấy cấu hình theo group
fetch('/api/config/group/website')
    .then(response => response.json())
    .then(data => {
        console.log(data.data); // Cấu hình website
    });

// Lấy nhiều groups
fetch('/api/config/groups', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        groups: ['website', 'email', 'payment']
    })
})
.then(response => response.json())
.then(data => {
    console.log(data.data); // Cấu hình nhiều groups
});
```

### Ví dụ sử dụng trong API Response

```php
// Trong Controller
public function getProductWithConfig($id)
{
    $product = Product::find($id);
    $websiteConfig = ConfigHelper::getWebsiteConfig();
    
    return response()->json([
        'success' => true,
        'data' => $product,
        'config' => [
            'website' => $websiteConfig,
            'seo' => ConfigHelper::getSeoConfig()
        ]
    ]);
}
```

## Cache Management

```php
// Xóa cache cho một key
ConfigHelper::clearCache('site_name');

// Xóa cache cho một group
ConfigHelper::clearGroupCache('website');

// Xóa tất cả cache cấu hình
ConfigHelper::clearAllCache();
```

## Lưu ý

1. Tất cả cấu hình đều được cache trong 1 giờ (3600 giây)
2. Khi cập nhật cấu hình, cache sẽ tự động được xóa
3. Cấu hình public có thể truy cập từ frontend mà không cần authentication
4. Sử dụng `ConfigHelper` thay vì truy cập trực tiếp model để có hiệu suất tốt hơn
5. Phù hợp cho API project, không có view rendering
