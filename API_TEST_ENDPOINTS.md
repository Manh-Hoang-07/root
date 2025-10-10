# 🚀 SystemConfig API Test Endpoints

## 📋 **ADMIN APIs** (Cần Authentication)

### **Base URL**: `/api/admin/config`

#### **1. CRUD Operations**

**GET** `/api/admin/config/` - Lấy danh sách configs
```bash
curl -X GET "http://localhost:8000/api/admin/config/" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**GET** `/api/admin/config/{id}` - Lấy config theo ID
```bash
curl -X GET "http://localhost:8000/api/admin/config/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**POST** `/api/admin/config/store` - Tạo/cập nhật config
```bash
curl -X POST "http://localhost:8000/api/admin/config/store" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "key": "app_name",
    "value": "My Application",
    "type": "string",
    "group": "general",
    "description": "Tên ứng dụng",
    "is_public": true,
    "is_encrypted": false,
    "status": "active"
  }'
```

**PUT** `/api/admin/config/{id}` - Cập nhật config
```bash
curl -X PUT "http://localhost:8000/api/admin/config/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "key": "app_name",
    "value": "Updated Application Name",
    "type": "string",
    "group": "general",
    "description": "Tên ứng dụng đã cập nhật",
    "is_public": true,
    "is_encrypted": false,
    "status": "active"
  }'
```

**DELETE** `/api/admin/config/{id}` - Xóa config
```bash
curl -X DELETE "http://localhost:8000/api/admin/config/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

#### **2. Special Operations**

**GET** `/api/admin/config/group?group=general` - Lấy configs theo group
```bash
curl -X GET "http://localhost:8000/api/admin/config/group?group=general" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**GET** `/api/admin/config/key?key=app_name` - Lấy config theo key
```bash
curl -X GET "http://localhost:8000/api/admin/config/key?key=app_name" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**POST** `/api/admin/config/bulk-update` - Cập nhật nhiều configs
```bash
curl -X POST "http://localhost:8000/api/admin/config/bulk-update" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "configs": [
      {
        "key": "app_name",
        "value": "Bulk Updated App"
      },
      {
        "key": "app_version",
        "value": "2.0.0"
      }
    ]
  }'
```

**POST** `/api/admin/config/clear-cache` - Xóa cache
```bash
curl -X POST "http://localhost:8000/api/admin/config/clear-cache" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

#### **3. Query Parameters cho Admin APIs**

**Pagination & Filtering:**
```bash
# Pagination
curl -X GET "http://localhost:8000/api/admin/config/?page=1&per_page=10"

# Filtering
curl -X GET "http://localhost:8000/api/admin/config/?group=general&is_public=true&status=active"

# Sorting
curl -X GET "http://localhost:8000/api/admin/config/?sort_by=created_at&sort_direction=desc"

# Field selection
curl -X GET "http://localhost:8000/api/admin/config/?fields=id,key,value,group"
```

---

## 🌐 **PUBLIC APIs** (Không cần Authentication)

### **Base URL**: `/api/public/config`

#### **1. Basic Operations**

**GET** `/api/public/config/` - Lấy danh sách public configs
```bash
curl -X GET "http://localhost:8000/api/public/config/" \
  -H "Accept: application/json"
```

**GET** `/api/public/config/groups` - Lấy danh sách public groups
```bash
curl -X GET "http://localhost:8000/api/public/config/groups" \
  -H "Accept: application/json"
```

**GET** `/api/public/config/{id}` - Lấy config theo ID
```bash
curl -X GET "http://localhost:8000/api/public/config/1" \
  -H "Accept: application/json"
```

**GET** `/api/public/config/key?key=app_name` - Lấy config theo key
```bash
curl -X GET "http://localhost:8000/api/public/config/key?key=app_name" \
  -H "Accept: application/json"
```

#### **2. Query Parameters cho Public APIs**

**Filtering:**
```bash
# Filter by group
curl -X GET "http://localhost:8000/api/public/config/?group=general"

# Filter by multiple groups
curl -X GET "http://localhost:8000/api/public/config/?group[]=general&group[]=email"

# Search by key
curl -X GET "http://localhost:8000/api/public/config/?search=app"

# Pagination
curl -X GET "http://localhost:8000/api/public/config/?page=1&per_page=5"

# Field selection
curl -X GET "http://localhost:8000/api/public/config/?fields=key,value,group"
```

---

## 🧪 **TEST SCENARIOS**

### **Scenario 1: Tạo Config mới**
```bash
# 1. Tạo config
curl -X POST "http://localhost:8000/api/admin/config/store" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "key": "test_config",
    "value": "test_value",
    "type": "string",
    "group": "test",
    "description": "Config để test",
    "is_public": true,
    "is_encrypted": false,
    "status": "active"
  }'

# 2. Kiểm tra trong Admin
curl -X GET "http://localhost:8000/api/admin/config/key?key=test_config" \
  -H "Authorization: Bearer YOUR_TOKEN"

# 3. Kiểm tra trong Public
curl -X GET "http://localhost:8000/api/public/config/key?key=test_config"
```

### **Scenario 2: Test Cache**
```bash
# 1. Tạo config
curl -X POST "http://localhost:8000/api/admin/config/store" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "key": "cache_test",
    "value": "cached_value",
    "type": "string",
    "group": "cache",
    "is_public": true,
    "status": "active"
  }'

# 2. Lấy config (sẽ cache)
curl -X GET "http://localhost:8000/api/public/config/key?key=cache_test"

# 3. Cập nhật config
curl -X PUT "http://localhost:8000/api/admin/config/{id}" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "key": "cache_test",
    "value": "updated_cached_value",
    "type": "string",
    "group": "cache",
    "is_public": true,
    "status": "active"
  }'

# 4. Lấy lại config (cache đã được clear)
curl -X GET "http://localhost:8000/api/public/config/key?key=cache_test"
```

### **Scenario 3: Test Audit Logging**
```bash
# 1. Tạo config (sẽ log audit)
curl -X POST "http://localhost:8000/api/admin/config/store" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "key": "audit_test",
    "value": "original_value",
    "type": "string",
    "group": "audit",
    "is_public": false,
    "status": "active"
  }'

# 2. Cập nhật config (sẽ log audit)
curl -X PUT "http://localhost:8000/api/admin/config/{id}" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "key": "audit_test",
    "value": "updated_value",
    "type": "string",
    "group": "audit",
    "is_public": false,
    "status": "active"
  }'

# 3. Xóa config (sẽ log audit)
curl -X DELETE "http://localhost:8000/api/admin/config/{id}" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 📊 **EXPECTED RESPONSES**

### **Success Response Format:**
```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Thành công",
  "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

### **Error Response Format:**
```json
{
  "success": false,
  "data": null,
  "message": "Lỗi validation",
  "errors": {
    "key": ["Key là bắt buộc"]
  },
  "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

---

## 🔧 **SETUP NOTES**

1. **Authentication**: Thay `YOUR_TOKEN` bằng token thực tế
2. **Base URL**: Thay `localhost:8000` bằng domain của bạn
3. **Content-Type**: Luôn sử dụng `application/json`
4. **Accept**: Luôn sử dụng `application/json`

## 🎯 **TESTING TIPS**

1. **Test từng endpoint một cách tuần tự**
2. **Kiểm tra response format**
3. **Test cả success và error cases**
4. **Verify cache behavior**
5. **Check audit logs trong database**
6. **Test với different user roles**

---

**Happy Testing! 🚀**
