# Authentication API

Tài liệu các API liên quan đến xác thực người dùng.

## Cấu trúc

- Base URL: `http://localhost:3000`
- Authentication: JWT Bearer Token (cho các endpoint được bảo vệ)
- Headers: `Content-Type: application/json`

---

## 1. Login (Đăng nhập)

### Request

```bash
curl -X POST http://localhost:3000/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password",
    "remember": true
  }'
```

### Request Body

```json
{
  "email": "admin@example.com",
  "password": "password",
  "remember": false
}
```

**Fields:**
- `email` (required): Email đăng nhập
- `password` (required): Mật khẩu (tối thiểu 6 ký tự)
- `remember` (optional): Nhớ đăng nhập (mặc định: false)

### Response

**Success (200):**
```json
{
  "success": true,
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id": 1,
      "username": "admin",
      "email": "admin@example.com",
      "status": "active"
    }
  },
  "message": "Đăng nhập thành công."
}
```

**Error (401):**
```json
{
  "success": false,
  "message": "Email hoặc mật khẩu không chính xác.",
  "data": null
}
```

### Lưu token vào biến (bash/zsh)

```bash
# Lưu token vào biến TOKEN
export TOKEN=$(curl -s -X POST http://localhost:3000/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password"}' \
  | jq -r '.data.token')

echo "Token: $TOKEN"
```

---

## 2. Register (Đăng ký)

### Request

```bash
curl -X POST http://localhost:3000/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Nguyễn Văn A",
    "username": "nguyenvana",
    "email": "nguyenvana@example.com",
    "phone": "0901234567",
    "password": "password123",
    "confirmPassword": "password123"
  }'
```

### Request Body

```json
{
  "name": "Nguyễn Văn A",
  "username": "nguyenvana",
  "email": "nguyenvana@example.com",
  "phone": "0901234567",
  "password": "password123",
  "confirmPassword": "password123"
}
```

**Fields:**
- `name` (required): Họ và tên
- `username` (optional): Tên đăng nhập (tối đa 50 ký tự)
- `email` (required): Email
- `phone` (optional): Số điện thoại (tối đa 20 ký tự)
- `password` (required): Mật khẩu (tối thiểu 8 ký tự)
- `confirmPassword` (required): Xác nhận mật khẩu (phải khớp với password)

### Response

**Success (201):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 21,
      "username": "nguyenvana",
      "email": "nguyenvana@example.com",
      "phone": "0901234567",
      "status": "active"
    }
  },
  "message": "Đăng ký thành công."
}
```

**Error (400):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email đã được sử dụng."],
    "confirmPassword": ["Xác nhận mật khẩu không khớp."]
  }
}
```

---

## 3. Get Current User (Lấy thông tin user hiện tại)

### Request

```bash
curl -X GET http://localhost:3000/me \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

### Response

**Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "username": "admin",
    "email": "admin@example.com",
    "phone": "0901234567",
    "status": "active",
    "roles": [
      {
        "id": 1,
        "code": "admin",
        "name": "Admin"
      }
    ]
  },
  "message": "Lấy thông tin người dùng thành công."
}
```

**Error (401):**
```json
{
  "success": false,
  "message": "Unauthorized",
  "data": null
}
```

---

## 4. Logout (Đăng xuất)

### Request

```bash
curl -X POST http://localhost:3000/logout \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

### Response

**Success (200):**
```json
{
  "success": true,
  "data": null,
  "message": "Đăng xuất thành công."
}
```

---

## 5. Refresh Token (Làm mới token)

### Request

```bash
curl -X POST http://localhost:3000/refresh \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

### Response

**Success (200):**
```json
{
  "success": true,
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  },
  "message": "Làm mới token thành công."
}
```

---

## Script Mẫu

### Script đăng nhập và lấy token (bash)

```bash
#!/bin/bash

# Đăng nhập
LOGIN_RESPONSE=$(curl -s -X POST http://localhost:3000/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }')

# Trích xuất token
TOKEN=$(echo $LOGIN_RESPONSE | jq -r '.data.token')

if [ "$TOKEN" != "null" ] && [ ! -z "$TOKEN" ]; then
  echo "✅ Đăng nhập thành công!"
  echo "Token: $TOKEN"
  
  # Lưu token vào file để sử dụng sau
  echo $TOKEN > .token
  
  # Lấy thông tin user hiện tại
  echo ""
  echo "📋 Thông tin user hiện tại:"
  curl -s -X GET http://localhost:3000/me \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" | jq '.'
else
  echo "❌ Đăng nhập thất bại!"
  echo $LOGIN_RESPONSE | jq '.'
fi
```

---

## Error Codes

| Status Code | Description |
|-------------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request - Validation failed |
| 401 | Unauthorized - Invalid credentials or token |
| 500 | Internal Server Error |

---

**Xem thêm:**
- [Admin Users API](../admin/user.md)
- [Admin Roles API](../admin/role.md)
- [Admin Permissions API](../admin/permission.md)


