# Ví Dụ Test Hệ Thống Cấu Hình

## 🧪 Test API Endpoints

### 1. Test Public API

#### Lấy danh sách nhóm cấu hình public
```bash
curl -X GET "http://localhost:8000/api/config/groups" \
  -H "Accept: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": [
    {
      "value": "general",
      "label": "Cài đặt chung",
      "description": "Các cài đặt cơ bản của hệ thống",
      "is_public": true
    },
    {
      "value": "api",
      "label": "Cài đặt API",
      "description": "Cài đặt API và rate limiting",
      "is_public": true
    }
  ],
  "message": "Lấy danh sách nhóm cấu hình public thành công"
}
```

#### Lấy cấu hình theo nhóm
```bash
curl -X GET "http://localhost:8000/api/config/group?group=general" \
  -H "Accept: application/json"
```

#### Lấy cấu hình theo key
```bash
curl -X GET "http://localhost:8000/api/config/key?key=app.name" \
  -H "Accept: application/json"
```

#### Lấy nhiều cấu hình
```bash
curl -X GET "http://localhost:8000/api/config/keys?keys[]=app.name&keys[]=app.version" \
  -H "Accept: application/json"
```

#### Tìm kiếm cấu hình
```bash
curl -X GET "http://localhost:8000/api/config/search?search=app" \
  -H "Accept: application/json"
```

### 2. Test Admin API

#### Lấy thống kê cấu hình
```bash
curl -X GET "http://localhost:8000/api/admin/config/statistics" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

#### Tạo cấu hình mới
```bash
curl -X POST "http://localhost:8000/api/admin/config/store" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "key": "test.setting",
    "value": "test value",
    "type": "string",
    "group": "custom",
    "description": "Test setting for demo",
    "is_public": true,
    "is_active": true
  }'
```

#### Cập nhật hàng loạt
```bash
curl -X POST "http://localhost:8000/api/admin/config/bulk-update" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "configs": [
      {
        "key": "test.setting1",
        "value": "value1",
        "type": "string",
        "group": "custom"
      },
      {
        "key": "test.setting2",
        "value": "value2",
        "type": "integer",
        "group": "custom"
      }
    ]
  }'
```

#### Xóa cấu hình
```bash
curl -X DELETE "http://localhost:8000/api/admin/config/delete?key=test.setting" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

#### Lấy danh sách cấu hình (có phân trang)
```bash
curl -X GET "http://localhost:8000/api/admin/config/list?per_page=10&page=1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

#### Xóa cache
```bash
curl -X POST "http://localhost:8000/api/admin/config/clear-cache" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

### 3. Test Audit Logs

#### Lấy audit logs theo config
```bash
curl -X GET "http://localhost:8000/api/admin/config-audit/config-logs?config_key=app.name&limit=10" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

#### Lấy audit logs theo user
```bash
curl -X GET "http://localhost:8000/api/admin/config-audit/user-logs?user_id=1&limit=10" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

#### Lấy thống kê audit
```bash
curl -X GET "http://localhost:8000/api/admin/config-audit/statistics" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

#### Export audit logs
```bash
curl -X GET "http://localhost:8000/api/admin/config-audit/export?format=json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
```

## 💻 Test Trong Code

### 1. Test Model
```php
use App\Models\SystemConfig;
use App\Enums\ConfigType;
use App\Enums\ConfigGroup;

// Test lấy cấu hình
$appName = SystemConfig::getByKey('app.name');
echo "App Name: " . $appName . "\n";

// Test tạo cấu hình
$config = SystemConfig::setByKey('test.key', 'test value', ConfigType::STRING, ConfigGroup::CUSTOM);
echo "Created config: " . $config->key . "\n";

// Test type casting
$debugConfig = SystemConfig::where('key', 'app.debug')->first();
$isDebug = $debugConfig->getTypedValue();
echo "Debug mode: " . ($isDebug ? 'true' : 'false') . "\n";
```

### 2. Test Service
```php
use App\Services\Core\SystemConfig\SystemConfigService;

$configService = app(SystemConfigService::class);

// Test lấy cấu hình
$appName = $configService->getByKey('app.name');
echo "App Name: " . $appName . "\n";

// Test lấy cấu hình theo nhóm
$generalConfigs = $configService->getByGroup('general');
echo "General configs count: " . count($generalConfigs) . "\n";

// Test tạo cấu hình
$result = $configService->createOrUpdate([
    'key' => 'test.service',
    'value' => 'service test value',
    'type' => 'string',
    'group' => 'custom',
    'description' => 'Test from service',
    'is_public' => true
]);

if ($result['success']) {
    echo "Config created successfully\n";
} else {
    echo "Error: " . $result['message'] . "\n";
}
```

### 3. Test Cache
```php
use App\Services\Core\SystemConfig\ConfigCacheService;

$cacheService = app(ConfigCacheService::class);

// Test cache hoạt động
$isWorking = $cacheService->isCacheWorking();
echo "Cache is working: " . ($isWorking ? 'true' : 'false') . "\n";

// Test cache stats
$stats = $cacheService->getCacheStats();
echo "Cache stats: " . json_encode($stats) . "\n";
```

### 4. Test Validation
```php
use App\Services\Core\SystemConfig\ConfigValidationService;

$validationService = app(ConfigValidationService::class);

// Test validation
$isValid = $validationService->validateKey('valid.key.name');
echo "Key is valid: " . ($isValid ? 'true' : 'false') . "\n";

$isValid = $validationService->validateKey('invalid key!');
echo "Invalid key is valid: " . ($isValid ? 'true' : 'false') . "\n";
```

## 🧪 Test Scenarios

### Scenario 1: Tạo và quản lý cấu hình
```php
// 1. Tạo cấu hình mới
$config = SystemConfig::setByKey('scenario.test', 'initial value', ConfigType::STRING, ConfigGroup::CUSTOM);

// 2. Lấy cấu hình
$value = SystemConfig::getByKey('scenario.test');
assert($value === 'initial value');

// 3. Cập nhật cấu hình
$config->setTypedValue('updated value');
$config->save();

// 4. Kiểm tra giá trị mới
$newValue = SystemConfig::getByKey('scenario.test');
assert($newValue === 'updated value');

// 5. Xóa cấu hình
$config->delete();
$deletedValue = SystemConfig::getByKey('scenario.test', 'default');
assert($deletedValue === 'default');
```

### Scenario 2: Test caching
```php
// 1. Lấy cấu hình (sẽ cache)
$start = microtime(true);
$value1 = SystemConfig::getByKey('app.name');
$time1 = microtime(true) - $start;

// 2. Lấy lại cấu hình (từ cache)
$start = microtime(true);
$value2 = SystemConfig::getByKey('app.name');
$time2 = microtime(true) - $start;

// 3. Kiểm tra cache hoạt động
assert($value1 === $value2);
assert($time2 < $time1); // Cache nhanh hơn
```

### Scenario 3: Test validation
```php
$validationService = app(ConfigValidationService::class);

// Test các loại validation
$testCases = [
    ['type' => 'integer', 'value' => '123', 'expected' => true],
    ['type' => 'integer', 'value' => 'abc', 'expected' => false],
    ['type' => 'boolean', 'value' => '1', 'expected' => true],
    ['type' => 'boolean', 'value' => 'yes', 'expected' => false],
    ['type' => 'json', 'value' => '{"key": "value"}', 'expected' => true],
    ['type' => 'json', 'value' => 'invalid json', 'expected' => false],
];

foreach ($testCases as $case) {
    $isValid = $validationService->validateValueByRules($case['value'], [$case['type']]);
    assert($isValid === $case['expected'], "Validation failed for {$case['type']}: {$case['value']}");
}
```

## 🔍 Debug Commands

### Kiểm tra database
```sql
-- Xem tất cả cấu hình
SELECT * FROM system_configs ORDER BY `group`, sort_order;

-- Xem cấu hình theo nhóm
SELECT * FROM system_configs WHERE `group` = 'general';

-- Xem audit logs
SELECT * FROM config_audit_logs ORDER BY created_at DESC LIMIT 10;
```

### Kiểm tra cache
```bash
# Redis
redis-cli
> KEYS system_config_*
> GET system_config_config_key_app.name

# File cache
ls storage/framework/cache/
```

### Kiểm tra logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Cache logs
grep "Cache" storage/logs/laravel.log
```

## 📊 Performance Testing

### Load Test với Apache Bench
```bash
# Test public API
ab -n 1000 -c 10 "http://localhost:8000/api/config/groups"

# Test admin API (cần token)
ab -n 1000 -c 10 -H "Authorization: Bearer YOUR_TOKEN" "http://localhost:8000/api/admin/config/statistics"
```

### Memory Usage Test
```php
// Test memory usage
$startMemory = memory_get_usage();

// Load 1000 configs
for ($i = 0; $i < 1000; $i++) {
    SystemConfig::getByKey('app.name');
}

$endMemory = memory_get_usage();
echo "Memory used: " . ($endMemory - $startMemory) / 1024 / 1024 . " MB\n";
```

## ✅ Checklist Test

- [ ] Public API hoạt động không cần authentication
- [ ] Admin API yêu cầu authentication
- [ ] Tạo cấu hình mới thành công
- [ ] Cập nhật cấu hình thành công
- [ ] Xóa cấu hình thành công
- [ ] Validation hoạt động đúng
- [ ] Cache hoạt động đúng
- [ ] Audit logging ghi log đúng
- [ ] Type casting hoạt động đúng
- [ ] Encryption/decryption hoạt động đúng
- [ ] Pagination hoạt động đúng
- [ ] Search hoạt động đúng
- [ ] Statistics trả về đúng
- [ ] Error handling hoạt động đúng

---

**Lưu ý**: Thay `YOUR_ADMIN_TOKEN` bằng token thực tế từ hệ thống authentication của bạn.
