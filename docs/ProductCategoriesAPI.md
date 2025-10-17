## Admin API - Product Categories

- Base URL: `/api/admin/product-categories`
- Auth: Bearer token via `Authorization` header (middleware `auto.auth`)
- Content-Type: `application/json`

### Common Query Params
- `per_page`: items per page (default 20, max 100)
- `relations`: comma-separated relations to include. Supported: `parent,children,createdUser,updatedUser`
- `fields`: comma-separated fields to select. Example: `id,name,parent_id,status`

---

### 1) List categories
- Method: GET
- URL: `/api/admin/product-categories`
- Query:
  - `per_page`, `relations`, `fields`
- Response (200):
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 12,
        "name": "Áo nam",
        "slug": "ao-nam",
        "parent_id": null,
        "status": "active",
        "sort_order": 1,
        "parent": null,
        "children": [
          { "id": 13, "name": "Áo thun", "parent_id": 12 },
          { "id": 14, "name": "Áo sơ mi", "parent_id": 12 }
        ]
      }
    ],
    "current_page": 1,
    "per_page": 20,
    "total": 53,
    "last_page": 3
  },
  "message": "Lấy danh sách dữ liệu thành công"
}
```

---

### 2) Get detail
- Method: GET
- URL: `/api/admin/product-categories/{id}`
- Query:
  - `relations`, `fields`
- Response (200):
```json
{
  "success": true,
  "data": {
    "id": 12,
    "name": "Áo nam",
    "slug": "ao-nam",
    "description": "Mô tả",
    "parent_id": null,
    "image": null,
    "icon": "tshirt",
    "status": "active",
    "sort_order": 1,
    "meta_title": "Áo nam",
    "meta_description": "Seo desc",
    "canonical_url": null,
    "og_image": null,
    "parent": null,
    "children": [
      { "id": 13, "name": "Áo thun", "parent_id": 12 },
      { "id": 14, "name": "Áo sơ mi", "parent_id": 12 }
    ],
    "createdUser": { "id": 1, "name": "Admin" },
    "updatedUser": { "id": 1, "name": "Admin" }
  },
  "message": "Lấy thông tin chi tiết thành công"
}
```

---

### 3) Create category
- Method: POST
- URL: `/api/admin/product-categories`
- Body (validated by `ProductCategoryRequest`):
```json
{
  "name": "Áo thun",
  "slug": "ao-thun",         // optional; auto-generated from name if omitted
  "description": "Mô tả",
  "parent_id": 12,            // optional
  "image": "https://.../img.jpg",
  "icon": "tshirt",
  "status": "active",        // required: active|inactive
  "sort_order": 1,            // default 0
  "meta_title": "SEO title",
  "meta_description": "SEO desc",
  "canonical_url": "https://...",
  "og_image": "https://.../og.jpg"
}
```
- Response (201):
```json
{ "success": true, "data": { "id": 15, "name": "Áo thun", "...": "..." }, "message": "Tạo dữ liệu thành công" }
```

---

### 4) Update category
- Method: PUT/PATCH
- URL: `/api/admin/product-categories/{id}`
- Body: same as create; fields can be partial with `sometimes`
- Response (200):
```json
{ "success": true, "data": { "id": 15, "name": "Áo thun cập nhật", "...": "..." }, "message": "Cập nhật dữ liệu thành công" }
```

---

### 5) Delete category
- Method: DELETE
- URL: `/api/admin/product-categories/{id}`
- Response (200):
```json
{ "success": true, "data": null, "message": "" }
```

---

### 6) Category tree
- Method: GET
- URL: `/api/admin/product-categories/tree`
- Response (200):
```json
{
  "success": true,
  "data": [
    {
      "id": 12,
      "name": "Áo nam",
      "parent_id": null,
      "children": [
        {
          "id": 13,
          "name": "Áo thun",
          "parent_id": 12,
          "children": []
        }
      ]
    }
  ],
  "message": "Lấy cây danh mục thành công"
}
```

---

### 7) Products by category
- Method: GET
- URL: `/api/admin/product-categories/products/{id}`
- Query:
  - `per_page` (<=100)
  - `relations` (e.g. `variants`)
  - `fields` (e.g. `id,name,sku,price,status`)
  - `status` (e.g. `active`)
  - `search` (match `name` or `sku`)
- Response (200):
```json
{
  "success": true,
  "data": {
    "data": [
      { "id": 101, "name": "Áo thun cotton", "sku": "TS-001", "price": "199000", "status": "active" }
    ],
    "current_page": 1,
    "per_page": 20,
    "total": 42,
    "last_page": 3
  },
  "message": "Lấy danh sách sản phẩm theo danh mục thành công"
}
```

---

### FE Notes
- List/Table: `GET /product-categories?relations=parent,children&fields=id,name,parent_id,status,sort_order`
- Form create/edit: `POST/PUT /product-categories` (+ preload parents from `GET /product-categories?per_page=1000&fields=id,name,parent_id` or `GET /product-categories/tree`)
- Tree view: `GET /product-categories/tree`
- Category products tab: `GET /product-categories/products/{id}?per_page=20&search=&status=active&fields=id,name,sku,price,status`


