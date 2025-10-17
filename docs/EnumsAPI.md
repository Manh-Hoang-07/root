## Admin API - Enums

- Base URL: `/api/admin`
- Auth: Bearer token via `Authorization` header (middleware `auto.auth`)
- Content-Type: `application/json`

### Endpoints

#### 1) Get enum values by type
- GET `/api/admin/enums/{type}`
- Response 200:
```json
{
  "success": true,
  "data": [
    { "key": "ACTIVE", "value": "active", "label": "Kích hoạt" },
    { "key": "INACTIVE", "value": "inactive", "label": "Tạm dừng" }
  ],
  "message": "Lấy danh sách enum thành công"
}
```
- Lưu ý: `{type}` là tên enum đã đăng ký trong hệ thống (ví dụ: `product_status`, `role_status`, `order_status`, `payment_status`, `shipping_status`, `attribute_type`, ...). FE có thể gọi trước để dựng dropdown/label.

#### 2) Get available enum types
- GET `/api/admin/enums/types`
- Response 200:
```json
{
  "success": true,
  "data": ["product_status", "order_status", "payment_status", "shipping_status", "attribute_type", ...],
  "message": "Lấy danh sách enum types thành công"
}
```

#### 3) Clear enum cache by type (admin tools)
- DELETE `/api/admin/enums/cache/{type}`
- Response 200:
```json
{ "success": true, "data": null, "message": "Đã xóa cache cho enum type: order_status" }
```

#### 4) Clear all enum caches (admin tools)
- GET `/api/admin/enums/cache/all`
- Response 200:
```json
{ "success": true, "data": null, "message": "Đã xóa tất cả cache enum" }
```

### FE Usage Notes
- Gọi `GET /api/admin/enums/types` một lần để lấy các type khả dụng.
- Cache cục bộ FE các enums phổ biến (status, type) để giảm call.
- Khi admin thay đổi dữ liệu cấu hình có thể ảnh hưởng enum, dùng endpoints clear cache để đồng bộ nhanh.


