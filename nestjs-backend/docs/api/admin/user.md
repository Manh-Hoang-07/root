# Admin Users API

API quản lý người dùng trong hệ thống admin.

## Cấu trúc

- Base URL: `http://localhost:3000`
- Authentication: JWT Bearer Token (bắt buộc)
- Headers: `Content-Type: application/json`

---

## 1. Create User (Tạo người dùng mới)

### Request

```bash
curl -X POST http://localhost:3000/admin/users \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "newuser",
    "email": "newuser@example.com",
    "phone": "0901234567",
    "password": "password123",
    "role_ids": [1],
    "profile": {
      "name": "Người dùng mới",
      "image": "https://example.com/avatar.jpg",
      "birthday": "1990-01-01",
      "gender": "male",
      "address": "123 Đường ABC, Quận XYZ",
      "about": "Giới thiệu về người dùng"
    }
  }'
```

### Request Body

```json
{
  "username": "newuser",
  "email": "newuser@example.com",
  "phone": "0901234567",
  "password": "password123",
  "role_ids": [1],
  "profile": {
    "name": "Người dùng mới",
    "image": "https://example.com/avatar.jpg",
    "birthday": "1990-01-01",
    "gender": "male",
    "address": "123 Đường ABC, Quận XYZ",
    "about": "Giới thiệu về người dùng"
  }
}
```

**Fields:**
- `username` (optional): Tên đăng nhập
- `email` (optional): Email
- `phone` (optional): Số điện thoại
- `password` (required): Mật khẩu (tối thiểu 6 ký tự)
- `role_ids` (optional): Mảng ID vai trò
- `profile` (optional): Thông tin profile

### Response

**Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 21,
    "username": "newuser",
    "email": "newuser@example.com",
    "phone": "0901234567",
    "status": "active",
    "created_at": "2025-01-11T05:30:00.000Z",
    "updated_at": "2025-01-11T05:30:00.000Z"
  },
  "message": "Thành công"
}
```

---

## 2. Update User (Cập nhật người dùng)

### Request

```bash
curl -X PATCH http://localhost:3000/admin/users/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "updateduser",
    "email": "updated@example.com",
    "phone": "0987654321",
    "profile": {
      "name": "Người dùng đã cập nhật",
      "image": "https://example.com/new-avatar.jpg"
    }
  }'
```

### Request Body

Tương tự như Create User, tất cả các fields đều optional.

### Response

**Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "username": "updateduser",
    "email": "updated@example.com",
    "phone": "0987654321",
    "status": "active",
    "updated_at": "2025-01-11T05:35:00.000Z"
  },
  "message": "Cập nhật thành công"
}
```

---

## 3. Get User Profile (Lấy thông tin profile)

### Request

```bash
curl -X GET http://localhost:3000/admin/users/1/profile \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

### Response

**Success (200):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "email": "admin@example.com",
      "phone": "0901234567",
      "status": "active"
    },
    "profile": {
      "id": 1,
      "userId": 1,
      "name": "Admin User",
      "image": "https://example.com/avatar.jpg",
      "birthday": "1985-01-01",
      "gender": "male",
      "address": "123 Main Street",
      "about": "System Administrator"
    }
  },
  "message": "Lấy thông tin profile thành công"
}
```

---

## 4. Change Password (Đổi mật khẩu)

### Request

```bash
curl -X PATCH http://localhost:3000/admin/users/1/password \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "newPassword": "newpassword123"
  }'
```

### Request Body

```json
{
  "newPassword": "newpassword123"
}
```

**Fields:**
- `newPassword` (required): Mật khẩu mới (tối thiểu 6 ký tự)

### Response

**Success (200):**
```json
{
  "success": true,
  "data": null,
  "message": "Đổi mật khẩu thành công"
}
```

---

## 5. Assign Roles (Gán vai trò)

### Request

```bash
curl -X POST http://localhost:3000/admin/users/1/roles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "role_ids": [1, 2, 3]
  }'
```

### Request Body

```json
{
  "role_ids": [1, 2, 3]
}
```

**Fields:**
- `role_ids` (required): Mảng ID vai trò

### Response

**Success (200):**
```json
{
  "success": true,
  "data": null,
  "message": "Gán vai trò thành công"
}
```

**Note:** Hiện tại endpoint này trả về error vì chưa được implement đầy đủ.

---

## Error Codes

| Status Code | Description |
|-------------|-------------|
| 200 | Success |
| 400 | Bad Request - Validation failed |
| 401 | Unauthorized |
| 404 | Not Found - User not found |
| 500 | Internal Server Error |

---

**Xem thêm:**
- [Authentication API](./../auth.md)
- [Admin Roles API](./role.md)
- [Admin Permissions API](./permission.md)


