## Admin API - Product Attributes

- Base URL: `/api/admin/product-attributes`
- Auth: Bearer token via `Authorization` header (middleware `auto.auth`)
- Content-Type: `application/json`

### Model
- Fields: `id, name, slug, type, description, is_required, is_variant, is_filterable, sort_order, status, created_at, updated_at`
- Relations (optional via `relations`): `values`, `createdUser`, `updatedUser`

### Related Enums (for FE dropdowns)
- Attribute types: `GET /api/admin/enums/attribute_type`
  - Example response:
  ```json
  {
    "success": true,
    "data": [
      { "key": "TEXT", "value": "text", "label": "Text" },
      { "key": "TEXTAREA", "value": "textarea", "label": "Textarea" },
      { "key": "SELECT", "value": "select", "label": "Select" },
      { "key": "MULTISELECT", "value": "multiselect", "label": "Multiselect" },
      { "key": "RADIO", "value": "radio", "label": "Radio" },
      { "key": "CHECKBOX", "value": "checkbox", "label": "Checkbox" },
      { "key": "COLOR", "value": "color", "label": "Color" },
      { "key": "IMAGE", "value": "image", "label": "Image" }
    ],
    "message": "Lấy danh sách enum thành công"
  }
  ```
- Status (shared): `GET /api/admin/enums/product_status` hoặc `GET /api/admin/enums/role_status` (tùy type cấu hình). Với thuộc tính sản phẩm đang dùng `active|inactive`, FE có thể dùng: `GET /api/admin/enums/attribute_status` nếu được cấu hình, hoặc tái dùng chung `active/inactive` enum được hệ thống cung cấp.

### Common Query Params
- `per_page`: items per page (default 20, max 100)
- `relations`: comma-separated, e.g. `values`
- `fields`: comma-separated, e.g. `id,name,type,status`

---

### 1) List attributes
- GET `/api/admin/product-attributes`
- Query: `per_page`, `relations`, `fields`
- Response 200 (paginated):
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Màu sắc",
        "slug": "mau-sac",
        "type": "select",
        "is_variant": true,
        "is_filterable": true,
        "status": "active",
        "values": [ { "id": 10, "product_attribute_id": 1, "value": "Đỏ" } ]
      }
    ],
    "current_page": 1,
    "per_page": 20,
    "total": 5,
    "last_page": 1
  },
  "message": "Lấy danh sách dữ liệu thành công"
}
```

### 2) Get detail
- GET `/api/admin/product-attributes/{id}`
- Query: `relations`, `fields`
- Response 200:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Màu sắc",
    "slug": "mau-sac",
    "type": "select",
    "description": null,
    "is_required": false,
    "is_variant": true,
    "is_filterable": true,
    "sort_order": 0,
    "status": "active",
    "values": [ { "id": 10, "product_attribute_id": 1, "value": "Đỏ" } ],
    "createdUser": { "id": 1, "name": "Admin" },
    "updatedUser": { "id": 1, "name": "Admin" }
  },
  "message": "Lấy thông tin chi tiết thành công"
}
```

### 3) Create
- POST `/api/admin/product-attributes`
- Body (validated by `ProductAttributeRequest`):
```json
{
  "name": "Màu sắc",
  "slug": "mau-sac",          // optional; auto-gen from name if omitted
  "type": "select",           // text|textarea|select|multiselect|radio|checkbox|color|image
  "description": null,
  "is_required": false,
  "is_variant": true,
  "is_filterable": true,
  "sort_order": 0,
  "status": "active"
}
```
- Response 201:
```json
{ "success": true, "data": { "id": 1, "name": "Màu sắc" }, "message": "Tạo dữ liệu thành công" }
```

### 4) Update
- PUT/PATCH `/api/admin/product-attributes/{id}`
- Body: same keys as create (partial allowed)
- Response 200:
```json
{ "success": true, "data": { "id": 1, "name": "Màu" }, "message": "Cập nhật dữ liệu thành công" }
```

### 5) Delete
- DELETE `/api/admin/product-attributes/{id}`
- Response 200:
```json
{ "success": true, "data": null, "message": "" }
```

### 6) Update status
- PATCH `/api/admin/product-attributes/status/{id}`
- Body:
```json
{ "status": "active" }
```
- Response 200:
```json
{ "success": true, "data": { "id": 1, "status": "active" }, "message": "Cập nhật trạng thái thành công" }
```


