# SystemConfigV2 - Quick Reference

## Public APIs (Không cần authentication)

### 1. Lấy cấu hình hệ thống chung
**GET** `/api/config-v2/general`

**Response:**
```json
{
  "success": true,
  "data": {
    "app_name": "Laravel App",
    "site_name": "Website Demo",
    "site_logo": "/images/logo.png",
    "site_favicon": "/images/favicon.ico",
    "site_email": "admin@demo.com",
    "site_phone": "0123456789",
    "site_address": "123 Đường ABC, Quận XYZ, TP.HCM",
    "site_description": "Website bán hàng trực tuyến...",
    "timezone": "Asia/Ho_Chi_Minh",
    "language": "vi",
    "maintenance_mode": false,
    "items_per_page": 20
  }
}
```

### 2. Lấy cấu hình email
**GET** `/api/config-v2/email`

**Response:**
```json
{
  "success": true,
  "data": {
    "smtp_host": "smtp.gmail.com",
    "smtp_port": 587,
    "smtp_username": "your-email@gmail.com",
    "smtp_password": "your-app-password",
    "smtp_encryption": "tls",
    "from_email": "noreply@example.com",
    "from_name": "Laravel App"
  }
}
```

### 3. Lấy cấu hình theo key
**GET** `/api/config-v2/key?key=site_name&default=Default Value`

**Response:**
```json
{
  "success": true,
  "data": "Website Demo"
}
```

## Admin APIs (Cần authentication + role admin)

### 4. Cập nhật cấu hình hệ thống chung
**POST** `/api/admin/config-v2/general`

**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "site_name": "Website Mới",
  "site_email": "new@demo.com",
  "site_phone": "0987654321",
  "site_address": "Địa chỉ mới",
  "site_description": "Mô tả mới",
  "timezone": "Asia/Ho_Chi_Minh",
  "language": "vi",
  "maintenance_mode": false,
  "items_per_page": 25
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "site_name": "Website Mới",
    "site_email": "new@demo.com",
    "site_phone": "0987654321",
    "site_address": "Địa chỉ mới",
    "site_description": "Mô tả mới",
    "timezone": "Asia/Ho_Chi_Minh",
    "language": "vi",
    "maintenance_mode": false,
    "items_per_page": 25
  }
}
```

### 5. Cập nhật cấu hình email
**POST** `/api/admin/config-v2/email`

**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "smtp_host": "smtp.example.com",
  "smtp_port": 465,
  "smtp_username": "admin@example.com",
  "smtp_password": "new-password",
  "smtp_encryption": "ssl",
  "from_email": "admin@example.com",
  "from_name": "Website Demo"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "smtp_host": "smtp.example.com",
    "smtp_port": 465,
    "smtp_username": "admin@example.com",
    "smtp_password": "new-password",
    "smtp_encryption": "ssl",
    "from_email": "admin@example.com",
    "from_name": "Website Demo"
  }
}
```

### 6. Cập nhật cấu hình theo key
**POST** `/api/admin/config-v2/key`

**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "key": "site_name",
  "value": "Website Test"
}
```

**Response:**
```json
{
  "success": true,
  "data": "Website Test"
}
```

## Field Types

### General Config Fields
| Field | Type | Description |
|-------|------|-------------|
| `app_name` | string | Tên ứng dụng |
| `site_name` | string | Tên website |
| `site_logo` | string | Logo website |
| `site_favicon` | string | Favicon website |
| `site_email` | string | Email liên hệ |
| `site_phone` | string | Số điện thoại |
| `site_address` | string | Địa chỉ website |
| `site_description` | string | Mô tả website |
| `timezone` | string | Múi giờ |
| `language` | string | Ngôn ngữ |
| `maintenance_mode` | boolean | Chế độ bảo trì |
| `items_per_page` | number | Số item mỗi trang |

### Email Config Fields
| Field | Type | Description |
|-------|------|-------------|
| `smtp_host` | string | SMTP Host |
| `smtp_port` | number | SMTP Port |
| `smtp_username` | string | SMTP Username |
| `smtp_password` | string | SMTP Password |
| `smtp_encryption` | string | SMTP Encryption (tls/ssl/none) |
| `from_email` | string | Email gửi đi |
| `from_name` | string | Tên người gửi |

## Error Response
```json
{
  "success": false,
  "message": "Lỗi khi cập nhật cấu hình: [Chi tiết lỗi]"
}
```