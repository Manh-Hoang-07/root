# Admin RBAC API

API quản lý phân quyền (Role-Based Access Control).

## Cấu trúc

- Base URL: `http://localhost:3000`
- Authentication: JWT Bearer Token (bắt buộc)
- Headers: `Content-Type: application/json`

---

## 1. Create Role (Tạo vai trò mới)

### Request

```bash
curl -X POST http://localhost:3000/admin/rbac/roles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "staff",
    "name": "Nhân viên",
    "parent_id": null
  }'
```

### Request Body

```json
{
  "code": "staff",
  "name": "Nhân viên",
  "parent_id": null
}
```

**Fields:**
- `code` (required): Mã vai trò (unique)
- `name` (optional): Tên vai trò
- `parent_id` (optional): ID vai trò cha

### Response

**Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 6,
    "code": "staff",
    "name": "Nhân viên",
    "status": "active",
    "parent_id": null,
    "created_at": "2025-01-11T06:00:00.000Z",
    "updated_at": "2025-01-11T06:00:00.000Z"
  },
  "message": "Tạo vai trò thành công"
}
```

---

## 2. Create Permission (Tạo quyền mới)

### Request

```bash
curl -X POST http://localhost:3000/admin/rbac/permissions \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "order.manage",
    "name": "Quản lý đơn hàng",
    "parent_id": null
  }'
```

### Request Body

```json
{
  "code": "order.manage",
  "name": "Quản lý đơn hàng",
  "parent_id": null
}
```

**Fields:**
- `code` (required): Mã quyền (unique)
- `name` (optional): Tên quyền
- `parent_id` (optional): ID quyền cha

### Response

**Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 37,
    "code": "order.manage",
    "name": "Quản lý đơn hàng",
    "status": "active",
    "parent_id": null,
    "created_at": "2025-01-11T06:05:00.000Z",
    "updated_at": "2025-01-11T06:05:00.000Z"
  },
  "message": "Tạo quyền thành công"
}
```

---

## 3. Assign Permissions to Role (Gán quyền cho vai trò)

### Request

```bash
curl -X POST http://localhost:3000/admin/rbac/roles/1/permissions \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "permission_ids": [1, 2, 3, 4, 5, 6]
  }'
```

### Request Body

```json
{
  "permission_ids": [1, 2, 3, 4, 5, 6]
}
```

**Fields:**
- `permission_ids` (required): Mảng ID quyền

### Response

**Success (200):**
```json
{
  "success": true,
  "data": null,
  "message": "Gán quyền cho vai trò thành công"
}
```

---

## 4. Assign Roles to User (Gán vai trò cho user)

### Request

```bash
curl -X POST http://localhost:3000/admin/rbac/users/1/roles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "role_ids": [1, 2]
  }'
```

### Request Body

```json
{
  "role_ids": [1, 2]
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
  "message": "Gán vai trò cho user thành công"
}
```

---

## 5. Assign Permissions to User (Gán quyền trực tiếp cho user)

### Request

```bash
curl -X POST http://localhost:3000/admin/rbac/users/1/permissions \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "permission_ids": [10, 11, 12]
  }'
```

### Request Body

```json
{
  "permission_ids": [10, 11, 12]
}
```

**Fields:**
- `permission_ids` (required): Mảng ID quyền

### Response

**Success (200):**
```json
{
  "success": true,
  "data": null,
  "message": "Gán quyền cho user thành công"
}
```

---

## RBAC Concepts

### Hierarchical Structure

**Roles Hierarchy:**
- Admin (Top level)
  - Manager
    - Editor
      - Author

**Permissions Hierarchy:**
- post.manage (Parent)
  - post.create
  - post.read
  - post.update
  - post.delete

### Permission Inheritance

Khi gán quyền cho vai trò hoặc vai trò cho user:
1. **Role → User**: User nhận tất cả quyền của các vai trò
2. **Permission → Role**: Tất cả user có vai trò đó nhận quyền
3. **Permission → User**: User nhận quyền trực tiếp (riêng lẻ)

### Best Practices

1. **Sử dụng Roles** cho nhóm người dùng có cùng quyền
2. **Sử dụng Direct Permissions** cho ngoại lệ
3. **Tạo hierarchical roles** để quản lý dễ hơn
4. **Phân quyền module-based** để dễ maintain

---

## Error Codes

| Status Code | Description |
|-------------|-------------|
| 200 | Success |
| 400 | Bad Request - Validation failed |
| 401 | Unauthorized |
| 404 | Not Found - Resource not found |
| 500 | Internal Server Error |

---

**Xem thêm:**
- [Authentication API](./../auth.md)
- [Admin Users API](./user.md)
- [Admin Roles API](./role.md)
- [Admin Permissions API](./permission.md)


