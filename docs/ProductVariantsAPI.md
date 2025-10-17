## Admin API - Product Variants

- Base URL: `/api/admin/product-variants`
- Auth: Bearer token via `Authorization` header (middleware `auto.auth`)
- Content-Type: `application/json`

### Model
- Fields: `id, product_id, sku, name, price, sale_price, cost_price, stock_quantity, weight, image, status, created_at, updated_at`
- Relations (optional via `relations`): `product`, `attributes.attribute`, `attributes.value`, `createdUser`, `updatedUser`

### Related Enums (for FE dropdowns)
- Variant status: `GET /api/admin/enums/product_status` (or a dedicated `variant_status` if configured). Expected contains `active|inactive`.
- Common statuses used across product domain can be taken from enums configured in system.

### Common Query Params
- `per_page`: items per page (default 20, max 100)
- `relations`: comma-separated, e.g. `product,attributes.attribute,attributes.value`
- `fields`: comma-separated, e.g. `id,product_id,sku,name,price,stock_quantity,status`

---

### 1) List variants
- GET `/api/admin/product-variants`
- Query: `per_page`, `relations`, `fields`
- Response 200 (paginated):
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1001,
        "product_id": 200,
        "sku": "TS-001-RED-M",
        "name": "Áo thun - Đỏ - M",
        "price": "199000",
        "stock_quantity": 50,
        "status": "active",
        "product": { "id": 200, "name": "Áo thun" },
        "attributes": [
          { "attribute": { "id": 1, "name": "Màu" }, "value": { "id": 10, "value": "Đỏ" } },
          { "attribute": { "id": 2, "name": "Size" }, "value": { "id": 20, "value": "M" } }
        ]
      }
    ],
    "current_page": 1,
    "per_page": 20,
    "total": 120,
    "last_page": 6
  },
  "message": "Lấy danh sách dữ liệu thành công"
}
```

### 2) Get detail
- GET `/api/admin/product-variants/{id}`
- Query: `relations`, `fields`
- Response 200:
```json
{
  "success": true,
  "data": {
    "id": 1001,
    "product_id": 200,
    "sku": "TS-001-RED-M",
    "name": "Áo thun - Đỏ - M",
    "price": "199000",
    "sale_price": null,
    "cost_price": null,
    "stock_quantity": 50,
    "weight": null,
    "image": null,
    "status": "active",
    "product": { "id": 200, "name": "Áo thun", "sku": "TS-001" },
    "attributes": [
      { "attribute": { "id": 1, "name": "Màu" }, "value": { "id": 10, "value": "Đỏ" } },
      { "attribute": { "id": 2, "name": "Size" }, "value": { "id": 20, "value": "M" } }
    ],
    "createdUser": { "id": 1, "name": "Admin" },
    "updatedUser": { "id": 1, "name": "Admin" }
  },
  "message": "Lấy thông tin chi tiết thành công"
}
```

### 3) Create
- POST `/api/admin/product-variants`
- Body (validated by `ProductVariantRequest`):
```json
{
  "product_id": 200,
  "sku": "TS-001-RED-M",     // optional; unique per variant
  "name": "Áo thun - Đỏ - M",
  "price": 199000,
  "sale_price": null,
  "cost_price": null,
  "stock_quantity": 50,
  "weight": null,
  "image": null,
  "status": "active",
  "attributes": [
    { "attribute_id": 1, "value_id": 10 },
    { "attribute_id": 2, "value_id": 20 }
  ]
}
```
- Response 201:
```json
{ "success": true, "data": { "id": 1001, "sku": "TS-001-RED-M" }, "message": "Tạo dữ liệu thành công" }
```

### 4) Update
- PUT/PATCH `/api/admin/product-variants/{id}`
- Body: same keys as create (partial allowed)
- Response 200:
```json
{ "success": true, "data": { "id": 1001, "price": "209000" }, "message": "Cập nhật dữ liệu thành công" }
```

### 5) Delete
- DELETE `/api/admin/product-variants/{id}`
- Response 200:
```json
{ "success": true, "data": null, "message": "" }
```

### 6) Update status
- PATCH `/api/admin/product-variants/status/{id}`
- Body:
```json
{ "status": "active" }
```
- Response 200:
```json
{ "success": true, "data": { "id": 1001, "status": "active" }, "message": "Cập nhật trạng thái thành công" }
```

### 7) List variants by product
- GET `/api/admin/product-variants/product/{productId}`
- Response 200:
```json
{
  "success": true,
  "data": [
    { "id": 1001, "product_id": 200, "sku": "TS-001-RED-M", "name": "Áo thun - Đỏ - M", "stock_quantity": 50, "status": "active" }
  ],
  "message": "Lấy danh sách biến thể sản phẩm thành công"
}
```


