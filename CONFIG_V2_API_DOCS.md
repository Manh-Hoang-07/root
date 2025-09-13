# Tài liệu API SystemConfigV2 - Mô tả cho Frontend

## Tổng quan
Hệ thống cấu hình V2 đơn giản chỉ dùng tạm thời trong quá trình phát triển, hỗ trợ cấu hình hệ thống chung và email.

## API Endpoints

### Public Endpoints (Không cần authentication)

#### 1. Lấy cấu hình hệ thống chung
**GET** `/api/config-v2/general`

**Response:**
```json
{
  "success": true,
  "message": "Lấy cấu hình hệ thống thành công",
  "data": {
    "app_name": "Laravel App",
    "site_name": "Website Demo",
    "site_logo": "/images/logo.png",
    "site_favicon": "/images/favicon.ico",
    "site_email": "admin@demo.com",
    "site_phone": "0123456789",
    "site_address": "123 Đường ABC, Quận XYZ, TP.HCM",
    "site_description": "Website bán hàng trực tuyến với nhiều sản phẩm chất lượng",
    "timezone": "Asia/Ho_Chi_Minh",
    "language": "vi",
    "maintenance_mode": false,
    "items_per_page": 20
  }
}
```

#### 2. Lấy cấu hình email
**GET** `/api/config-v2/email`

**Response:**
```json
{
  "success": true,
  "message": "Lấy cấu hình email thành công",
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

#### 3. Lấy cấu hình theo key
**GET** `/api/config-v2/key?key=site_name&default=Default Value`

**Response:**
```json
{
  "success": true,
  "message": "Lấy cấu hình thành công",
  "data": "Website Demo"
}
```

### Admin Endpoints (Cần authentication + role admin)

#### 4. Cập nhật cấu hình hệ thống chung
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
  "message": "Cập nhật cấu hình hệ thống thành công",
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

#### 5. Cập nhật cấu hình email
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
  "message": "Cập nhật cấu hình email thành công",
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

#### 6. Cập nhật cấu hình theo key
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
  "message": "Cập nhật cấu hình thành công",
  "data": "Website Test"
}
```

## Mô tả các trường dữ liệu

### General Config (Cấu hình hệ thống chung)

| Trường | Kiểu | Mô tả | Ví dụ | Bắt buộc |
|--------|------|-------|-------|----------|
| `app_name` | string | Tên ứng dụng | "Laravel App" | Không |
| `site_name` | string | Tên website | "Website Demo" | Không |
| `site_logo` | string | Đường dẫn logo website | "/images/logo.png" | Không |
| `site_favicon` | string | Đường dẫn favicon | "/images/favicon.ico" | Không |
| `site_email` | string | Email liên hệ | "admin@demo.com" | Không |
| `site_phone` | string | Số điện thoại liên hệ | "0123456789" | Không |
| `site_address` | text | Địa chỉ website | "123 Đường ABC, Quận XYZ, TP.HCM" | Không |
| `site_description` | text | Mô tả website | "Website bán hàng trực tuyến..." | Không |
| `timezone` | string | Múi giờ | "Asia/Ho_Chi_Minh" | Không |
| `language` | string | Ngôn ngữ mặc định | "vi" | Không |
| `maintenance_mode` | boolean | Chế độ bảo trì | false | Không |
| `items_per_page` | number | Số item mỗi trang | 20 | Không |

### Email Config (Cấu hình email)

| Trường | Kiểu | Mô tả | Ví dụ | Bắt buộc |
|--------|------|-------|-------|----------|
| `smtp_host` | string | SMTP Host | "smtp.gmail.com" | Không |
| `smtp_port` | number | SMTP Port | 587 | Không |
| `smtp_username` | string | SMTP Username | "your-email@gmail.com" | Không |
| `smtp_password` | string | SMTP Password | "your-app-password" | Không |
| `smtp_encryption` | string | SMTP Encryption | "tls" | Không |
| `from_email` | string | Email gửi đi | "noreply@example.com" | Không |
| `from_name` | string | Tên người gửi | "Laravel App" | Không |

## Giao diện đề xuất

### 1. Tab "Cấu hình hệ thống chung"

#### Thông tin cơ bản
- **Tên ứng dụng** (app_name): Input text
- **Tên website** (site_name): Input text
- **Logo website** (site_logo): File upload hoặc input text
- **Favicon** (site_favicon): File upload hoặc input text

#### Thông tin liên hệ
- **Email liên hệ** (site_email): Input email
- **Số điện thoại** (site_phone): Input tel
- **Địa chỉ** (site_address): Textarea

#### Cấu hình khác
- **Mô tả website** (site_description): Textarea
- **Múi giờ** (timezone): Select dropdown
- **Ngôn ngữ** (language): Select dropdown
- **Chế độ bảo trì** (maintenance_mode): Toggle switch
- **Số item mỗi trang** (items_per_page): Input number

### 2. Tab "Cấu hình email"

#### SMTP Settings
- **SMTP Host** (smtp_host): Input text
- **SMTP Port** (smtp_port): Input number
- **SMTP Username** (smtp_username): Input text
- **SMTP Password** (smtp_password): Input password
- **SMTP Encryption** (smtp_encryption): Select dropdown (tls, ssl, none)

#### Thông tin gửi email
- **Email gửi đi** (from_email): Input email
- **Tên người gửi** (from_name): Input text

## Validation Rules

### General Config
- `app_name`: string, max 255 ký tự
- `site_name`: string, max 255 ký tự
- `site_logo`: string, max 500 ký tự
- `site_favicon`: string, max 500 ký tự
- `site_email`: email format
- `site_phone`: string, max 20 ký tự
- `site_address`: text, max 1000 ký tự
- `site_description`: text, max 2000 ký tự
- `timezone`: string, phải là timezone hợp lệ
- `language`: string, 2 ký tự (vi, en, etc.)
- `maintenance_mode`: boolean
- `items_per_page`: number, min 1, max 100

### Email Config
- `smtp_host`: string, max 255 ký tự
- `smtp_port`: number, min 1, max 65535
- `smtp_username`: string, max 255 ký tự
- `smtp_password`: string, max 255 ký tự
- `smtp_encryption`: enum (tls, ssl, none)
- `from_email`: email format
- `from_name`: string, max 255 ký tự

## Error Handling

### Lỗi thường gặp
- **400 Bad Request**: Thiếu key bắt buộc
- **422 Unprocessable Entity**: Dữ liệu không hợp lệ
- **500 Internal Server Error**: Lỗi server

### Response lỗi
```json
{
  "success": false,
  "message": "Lỗi khi cập nhật cấu hình: [Chi tiết lỗi]"
}
```

## Lưu ý cho Frontend

1. **Phân quyền**: 
   - **Public**: Chỉ có thể đọc dữ liệu (GET endpoints)
   - **Admin**: Có thể đọc và cập nhật dữ liệu (POST endpoints)
2. **Authentication**: Admin endpoints cần Bearer token trong header
3. **Cache**: Tất cả dữ liệu được cache 1 giờ, sau khi update sẽ tự động clear cache
4. **Encoding**: Sử dụng UTF-8 cho tất cả text
5. **File Upload**: Các trường logo và favicon có thể là đường dẫn file hoặc URL
6. **Validation**: Validate phía frontend trước khi gửi request
7. **Loading States**: Hiển thị loading khi đang gửi request
8. **Success/Error Messages**: Hiển thị thông báo kết quả cho user
9. **Auto-save**: Có thể implement auto-save sau một khoảng thời gian
10. **Reset**: Có nút reset về giá trị mặc định
11. **Error Handling**: Xử lý lỗi 401 (Unauthorized) cho admin endpoints
