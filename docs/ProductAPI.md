# API Sản Phẩm (Product API)

Tài liệu này mô tả các API endpoint cho chức năng quản lý sản phẩm, hỗ trợ cả khách hàng và admin.

## Base URL
```
http://your-domain.com/api
```

## Authentication
- **Khách hàng**: Có thể xem sản phẩm mà không cần đăng nhập
- **Người dùng đã đăng nhập**: Có thể xem sản phẩm với thông tin chi tiết hơn
- **Admin**: Cần đăng nhập và có quyền phù hợp để quản lý sản phẩm

---

## 1. API cho Khách hàng và Người dùng đã đăng nhập

### 1.1. Lấy danh sách sản phẩm
```bash
curl -X GET "http://your-domain.com/api/products?page=1&per_page=20" \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách dữ liệu thành công",
  "data": [
    {
      "id": 1,
      "name": "iPhone 15 Pro",
      "slug": "iphone-15-pro",
      "sku": "IP15P-128GB-BLU",
      "description": "iPhone 15 Pro với chip A17 Pro",
      "price": 99900000,
      "sale_price": 94900000,
      "stock_quantity": 50,
      "status": "active",
      "featured": true,
      "image": "http://your-domain.com/storage/products/iphone-15-pro.jpg",
      "categories": [
        {
          "id": 1,
          "name": "Điện thoại",
          "slug": "dien-thoai"
        }
      ],
      "variants": [
        {
          "id": 1,
          "name": "iPhone 15 Pro 128GB Blue",
          "sku": "IP15P-128GB-BLU",
          "price": 99900000,
          "sale_price": 94900000,
          "stock_quantity": 20
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

### 1.2. Xem chi tiết sản phẩm
```bash
curl -X GET "http://your-domain.com/api/products/1" \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy chi tiết sản phẩm thành công",
  "data": {
    "id": 1,
    "name": "iPhone 15 Pro",
    "slug": "iphone-15-pro",
    "sku": "IP15P-128GB-BLU",
    "description": "iPhone 15 Pro với chip A17 Pro",
    "content": "Chi tiết về sản phẩm...",
    "price": 99900000,
    "sale_price": 94900000,
    "stock_quantity": 50,
    "status": "active",
    "featured": true,
    "weight": 187,
    "dimensions": {
      "length": 146.6,
      "width": 70.6,
      "height": 8.25
    },
    "images": [
      "http://your-domain.com/storage/products/iphone-15-pro-1.jpg",
      "http://your-domain.com/storage/products/iphone-15-pro-2.jpg"
    ],
    "categories": [
      {
        "id": 1,
        "name": "Điện thoại",
        "slug": "dien-thoai"
      }
    ],
    "variants": [
      {
        "id": 1,
        "name": "iPhone 15 Pro 128GB Blue",
        "sku": "IP15P-128GB-BLU",
        "price": 99900000,
        "sale_price": 94900000,
        "stock_quantity": 20,
        "images": [
          "http://your-domain.com/storage/products/iphone-15-pro-blue-1.jpg"
        ]
      }
    ],
    "created_at": "2023-10-21T10:30:00Z",
    "updated_at": "2023-10-21T10:30:00Z"
  }
}
```

### 1.3. Lấy sản phẩm nổi bật
```bash
curl -X GET "http://your-domain.com/api/products/featured?limit=12" \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy sản phẩm nổi bật thành công",
  "data": [
    {
      "id": 1,
      "name": "iPhone 15 Pro",
      "slug": "iphone-15-pro",
      "sku": "IP15P-128GB-BLU",
      "description": "iPhone 15 Pro với chip A17 Pro",
      "price": 99900000,
      "sale_price": 94900000,
      "stock_quantity": 50,
      "status": "active",
      "featured": true,
      "image": "http://your-domain.com/storage/products/iphone-15-pro.jpg",
      "categories": [
        {
          "id": 1,
          "name": "Điện thoại",
          "slug": "dien-thoai"
        }
      ]
    }
  ]
}
```

### 1.4. Tìm kiếm sản phẩm
```bash
curl -X GET "http://your-domain.com/api/products/search?q=iPhone&category=1&min_price=50000000&max_price=150000000&sort_by=price&sort_order=asc&limit=12" \
  -H "Content-Type: application/json"
```

**Query Parameters:**
- `q`: Từ khóa tìm kiếm
- `category`: ID danh mục
- `min_price`: Giá tối thiểu
- `max_price`: Giá tối đa
- `sort_by`: Trường sắp xếp (name, price, created_at)
- `sort_order`: Thứ tự sắp xếp (asc, desc)
- `limit`: Giới hạn kết quả

**Response:**
```json
{
  "success": true,
  "message": "Tìm kiếm sản phẩm thành công",
  "data": [
    {
      "id": 1,
      "name": "iPhone 15 Pro",
      "slug": "iphone-15-pro",
      "sku": "IP15P-128GB-BLU",
      "description": "iPhone 15 Pro với chip A17 Pro",
      "price": 99900000,
      "sale_price": 94900000,
      "stock_quantity": 50,
      "status": "active",
      "featured": true,
      "image": "http://your-domain.com/storage/products/iphone-15-pro.jpg",
      "categories": [
        {
          "id": 1,
          "name": "Điện thoại",
          "slug": "dien-thoai"
        }
      ]
    }
  ]
}
```

### 1.5. Lấy sản phẩm theo danh mục
```bash
curl -X GET "http://your-domain.com/api/products/by-category/1?page=1&per_page=20" \
  -H "Content-Type: application/json"
```

**Mô tả**: Lấy danh sách sản phẩm thuộc một danh mục cụ thể. API này sử dụng quan hệ nhiều-nhiều giữa sản phẩm và danh mục thông qua bảng `product_category`.

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách dữ liệu thành công",
  "data": [
    {
      "id": 1,
      "name": "iPhone 15 Pro",
      "slug": "iphone-15-pro",
      "sku": "IP15P-128GB-BLU",
      "description": "iPhone 15 Pro với chip A17 Pro",
      "price": 99900000,
      "sale_price": 94900000,
      "stock_quantity": 50,
      "status": "active",
      "featured": true,
      "image": "http://your-domain.com/storage/products/iphone-15-pro.jpg",
      "categories": [
        {
          "id": 1,
          "name": "Điện thoại",
          "slug": "dien-thoai"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 50
  }
}
```

**Lưu ý**: API này sử dụng quan hệ nhiều-nhiều giữa sản phẩm và danh mục thông qua bảng `product_category`.

### 1.6. Lấy biến thể sản phẩm
```bash
curl -X GET "http://your-domain.com/api/products/1/variants" \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy biến thể sản phẩm thành công",
  "data": [
    {
      "id": 1,
      "name": "iPhone 15 Pro 128GB Blue",
      "sku": "IP15P-128GB-BLU",
      "price": 99900000,
      "sale_price": 94900000,
      "stock_quantity": 20,
      "weight": 187,
      "image": "http://your-domain.com/storage/products/iphone-15-pro-blue.jpg",
      "attributes": [
        {
          "attribute": {
            "id": 1,
            "name": "Màu sắc"
          },
          "value": {
            "id": 1,
            "value": "Blue"
          }
        },
        {
          "attribute": {
            "id": 2,
            "name": "Dung lượng"
          },
          "value": {
            "id": 1,
            "value": "128GB"
          }
        }
      ]
    }
  ]
}
```

### 1.7. Lấy danh sách danh mục sản phẩm
```bash
curl -X GET "http://your-domain.com/api/product-categories" \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách dữ liệu thành công",
  "data": [
    {
      "id": 1,
      "name": "Điện thoại",
      "slug": "dien-thoai",
      "description": "Các loại điện thoại thông minh",
      "image": "http://your-domain.com/storage/categories/dien-thoai.jpg",
      "parent_id": null,
      "status": "active",
      "children": [
        {
          "id": 2,
          "name": "iPhone",
          "slug": "iphone",
          "parent_id": 1,
          "status": "active"
        }
      ]
    }
  ]
}
```

### 1.8. Xem chi tiết danh mục sản phẩm
```bash
curl -X GET "http://your-domain.com/api/product-categories/1" \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy thông tin chi tiết thành công",
  "data": {
    "id": 1,
    "name": "Điện thoại",
    "slug": "dien-thoai",
    "description": "Các loại điện thoại thông minh",
    "image": "http://your-domain.com/storage/categories/dien-thoai.jpg",
    "parent_id": null,
    "status": "active",
    "parent": null,
    "children": [
      {
        "id": 2,
        "name": "iPhone",
        "slug": "iphone",
        "parent_id": 1,
        "status": "active"
      }
    ]
  }
}
```

### 1.9. Lấy cây danh mục sản phẩm
```bash
curl -X GET "http://your-domain.com/api/product-categories/tree" \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy cây danh mục thành công",
  "data": [
    {
      "id": 1,
      "name": "Điện thoại",
      "slug": "dien-thoai",
      "description": "Các loại điện thoại thông minh",
      "image": "http://your-domain.com/storage/categories/dien-thoai.jpg",
      "parent_id": null,
      "status": "active",
      "children": [
        {
          "id": 2,
          "name": "iPhone",
          "slug": "iphone",
          "parent_id": 1,
          "status": "active",
          "children": []
        }
      ]
    }
  ]
}
```

### 1.10. Lấy sản phẩm theo danh mục (chi tiết)
```bash
curl -X GET "http://your-domain.com/api/product-categories/1/products?page=1&per_page=20" \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy sản phẩm theo danh mục thành công",
  "data": [
    {
      "id": 1,
      "name": "iPhone 15 Pro",
      "slug": "iphone-15-pro",
      "sku": "IP15P-128GB-BLU",
      "description": "iPhone 15 Pro với chip A17 Pro",
      "price": 99900000,
      "sale_price": 94900000,
      "stock_quantity": 50,
      "status": "active",
      "featured": true,
      "image": "http://your-domain.com/storage/products/iphone-15-pro.jpg"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 50
  }
}
```

---

## 2. API cho Admin

### 2.1. Quản lý sản phẩm

#### 2.1.1. Lấy danh sách sản phẩm
```bash
curl -X GET "http://your-domain.com/api/admin/products?page=1&per_page=20" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách dữ liệu thành công",
  "data": [
    {
      "id": 1,
      "name": "iPhone 15 Pro",
      "slug": "iphone-15-pro",
      "sku": "IP15P-128GB-BLU",
      "description": "iPhone 15 Pro với chip A17 Pro",
      "price": 99900000,
      "sale_price": 94900000,
      "stock_quantity": 50,
      "status": "active",
      "featured": true,
      "created_at": "2023-10-21T10:30:00Z",
      "updated_at": "2023-10-21T10:30:00Z",
      "categories": [
        {
          "id": 1,
          "name": "Điện thoại"
        }
      ],
      "variants": [
        {
          "id": 1,
          "product_id": 1,
          "name": "iPhone 15 Pro 128GB Blue",
          "sku": "IP15P-128GB-BLU",
          "price": 99900000,
          "stock_quantity": 20
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

#### 2.1.2. Tạo sản phẩm mới
```bash
curl -X POST "http://your-domain.com/api/admin/products" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "name": "iPhone 15 Pro Max",
    "slug": "iphone-15-pro-max",
    "sku": "IP15PM-256GB-TIT",
    "description": "iPhone 15 Pro Max với chip A17 Pro",
    "content": "Chi tiết về sản phẩm...",
    "price": 119900000,
    "sale_price": 114900000,
    "stock_quantity": 30,
    "status": "active",
    "featured": true,
    "weight": 221,
    "dimensions": {
      "length": 159.9,
      "width": 76.7,
      "height": 8.25
    },
    "category_ids": [1, 2],
    "images": [
      "http://your-domain.com/storage/products/iphone-15-pro-max-1.jpg",
      "http://your-domain.com/storage/products/iphone-15-pro-max-2.jpg"
    ]
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Tạo dữ liệu thành công",
  "data": {
    "id": 2,
    "name": "iPhone 15 Pro Max",
    "slug": "iphone-15-pro-max",
    "sku": "IP15PM-256GB-TIT",
    "description": "iPhone 15 Pro Max với chip A17 Pro",
    "price": 119900000,
    "sale_price": 114900000,
    "stock_quantity": 30,
    "status": "active",
    "featured": true,
    "created_at": "2023-10-21T11:00:00Z"
  }
}
```

#### 2.1.3. Xem chi tiết sản phẩm
```bash
curl -X GET "http://your-domain.com/api/admin/products/1" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy thông tin chi tiết thành công",
  "data": {
    "id": 1,
    "name": "iPhone 15 Pro",
    "slug": "iphone-15-pro",
    "sku": "IP15P-128GB-BLU",
    "description": "iPhone 15 Pro với chip A17 Pro",
    "content": "Chi tiết về sản phẩm...",
    "price": 99900000,
    "sale_price": 94900000,
    "stock_quantity": 50,
    "status": "active",
    "featured": true,
    "weight": 187,
    "dimensions": {
      "length": 146.6,
      "width": 70.6,
      "height": 8.25
    },
    "images": [
      "http://your-domain.com/storage/products/iphone-15-pro-1.jpg",
      "http://your-domain.com/storage/products/iphone-15-pro-2.jpg"
    ],
    "categories": [
      {
        "id": 1,
        "name": "Điện thoại"
      }
    ],
    "variants": [
      {
        "id": 1,
        "product_id": 1,
        "name": "iPhone 15 Pro 128GB Blue",
        "sku": "IP15P-128GB-BLU",
        "price": 99900000,
        "stock_quantity": 20
      }
    ],
    "createdUser": {
      "id": 1,
      "name": "Admin User"
    },
    "updatedUser": {
      "id": 1,
      "name": "Admin User"
    }
  }
}
```

#### 2.1.4. Cập nhật sản phẩm
```bash
curl -X PUT "http://your-domain.com/api/admin/products/1" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "name": "iPhone 15 Pro",
    "slug": "iphone-15-pro",
    "sku": "IP15P-128GB-BLU",
    "description": "iPhone 15 Pro với chip A17 Pro",
    "content": "Chi tiết cập nhật về sản phẩm...",
    "price": 99900000,
    "sale_price": 92900000,
    "stock_quantity": 45,
    "status": "active",
    "featured": true,
    "weight": 187,
    "category_ids": [1, 2],
    "images": [
      "http://your-domain.com/storage/products/iphone-15-pro-1.jpg",
      "http://your-domain.com/storage/products/iphone-15-pro-2.jpg"
    ]
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Cập nhật dữ liệu thành công",
  "data": {
    "id": 1,
    "name": "iPhone 15 Pro",
    "slug": "iphone-15-pro",
    "sku": "IP15P-128GB-BLU",
    "description": "iPhone 15 Pro với chip A17 Pro",
    "price": 99900000,
    "sale_price": 92900000,
    "stock_quantity": 45,
    "status": "active",
    "featured": true,
    "updated_at": "2023-10-21T11:30:00Z"
  }
}
```

#### 2.1.5. Cập nhật trạng thái sản phẩm
```bash
curl -X PATCH "http://your-domain.com/api/admin/products/status/1" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "status": "inactive"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Cập nhật trạng thái thành công",
  "data": {
    "id": 1,
    "status": "inactive"
  }
}
```

#### 2.1.6. Chuyển đổi trạng thái nổi bật
```bash
curl -X PATCH "http://your-domain.com/api/admin/products/toggle-featured/1" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Cập nhật trạng thái nổi bật thành công",
  "data": {
    "id": 1,
    "featured": false
  }
}
```

#### 2.1.7. Xóa sản phẩm
```bash
curl -X DELETE "http://your-domain.com/api/admin/products/1" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Xóa dữ liệu thành công",
  "data": null
}
```

### 2.2. Quản lý danh mục sản phẩm

#### 2.2.1. Lấy danh sách danh mục sản phẩm
```bash
curl -X GET "http://your-domain.com/api/admin/product-categories" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách dữ liệu thành công",
  "data": [
    {
      "id": 1,
      "name": "Điện thoại",
      "slug": "dien-thoai",
      "description": "Các loại điện thoại thông minh",
      "image": "http://your-domain.com/storage/categories/dien-thoai.jpg",
      "parent_id": null,
      "status": "active",
      "parent": null,
      "children": [
        {
          "id": 2,
          "name": "iPhone",
          "slug": "iphone",
          "parent_id": 1,
          "status": "active"
        }
      ]
    }
  ]
}
```

#### 2.2.2. Tạo danh mục sản phẩm mới
```bash
curl -X POST "http://your-domain.com/api/admin/product-categories" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "name": "Laptop",
    "slug": "laptop",
    "description": "Các loại laptop gaming và văn phòng",
    "image": "http://your-domain.com/storage/categories/laptop.jpg",
    "parent_id": null,
    "status": "active"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Tạo dữ liệu thành công",
  "data": {
    "id": 3,
    "name": "Laptop",
    "slug": "laptop",
    "description": "Các loại laptop gaming và văn phòng",
    "image": "http://your-domain.com/storage/categories/laptop.jpg",
    "parent_id": null,
    "status": "active",
    "created_at": "2023-10-21T12:00:00Z"
  }
}
```

#### 2.2.3. Lấy cây danh mục sản phẩm
```bash
curl -X GET "http://your-domain.com/api/admin/product-categories/tree" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy cây danh mục thành công",
  "data": [
    {
      "id": 1,
      "name": "Điện thoại",
      "slug": "dien-thoai",
      "description": "Các loại điện thoại thông minh",
      "image": "http://your-domain.com/storage/categories/dien-thoai.jpg",
      "parent_id": null,
      "status": "active",
      "children": [
        {
          "id": 2,
          "name": "iPhone",
          "slug": "iphone",
          "parent_id": 1,
          "status": "active",
          "children": []
        }
      ]
    }
  ]
}
```

#### 2.2.4. Lấy sản phẩm theo danh mục
```bash
curl -X GET "http://your-domain.com/api/admin/product-categories/products/1?page=1&per_page=20" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy sản phẩm theo danh mục thành công",
  "data": [
    {
      "id": 1,
      "name": "iPhone 15 Pro",
      "slug": "iphone-15-pro",
      "sku": "IP15P-128GB-BLU",
      "description": "iPhone 15 Pro với chip A17 Pro",
      "price": 99900000,
      "sale_price": 94900000,
      "stock_quantity": 50,
      "status": "active",
      "featured": true,
      "image": "http://your-domain.com/storage/products/iphone-15-pro.jpg"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 50
  }
}
```

### 2.3. Quản lý biến thể sản phẩm

#### 2.3.1. Lấy danh sách biến thể sản phẩm
```bash
curl -X GET "http://your-domain.com/api/admin/product-variants?page=1&per_page=20" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách dữ liệu thành công",
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "name": "iPhone 15 Pro 128GB Blue",
      "sku": "IP15P-128GB-BLU",
      "price": 99900000,
      "sale_price": 94900000,
      "stock_quantity": 20,
      "weight": 187,
      "status": "active",
      "product": {
        "id": 1,
        "name": "iPhone 15 Pro",
        "sku": "IP15P"
      },
      "attributes": [
        {
          "attribute": {
            "id": 1,
            "name": "Màu sắc"
          },
          "value": {
            "id": 1,
            "value": "Blue"
          }
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

#### 2.3.2. Tạo biến thể sản phẩm mới
```bash
curl -X POST "http://your-domain.com/api/admin/product-variants" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "product_id": 1,
    "name": "iPhone 15 Pro 256GB Blue",
    "sku": "IP15P-256GB-BLU",
    "price": 109900000,
    "sale_price": 104900000,
    "stock_quantity": 15,
    "weight": 187,
    "status": "active",
    "attribute_values": [
      {
        "attribute_id": 1,
        "value_id": 1
      },
      {
        "attribute_id": 2,
        "value_id": 2
      }
    ],
    "image": "http://your-domain.com/storage/products/iphone-15-pro-256gb-blue.jpg"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Tạo dữ liệu thành công",
  "data": {
    "id": 2,
    "product_id": 1,
    "name": "iPhone 15 Pro 256GB Blue",
    "sku": "IP15P-256GB-BLU",
    "price": 109900000,
    "sale_price": 104900000,
    "stock_quantity": 15,
    "weight": 187,
    "status": "active",
    "created_at": "2023-10-21T12:30:00Z"
  }
}
```

#### 2.3.3. Lấy biến thể theo sản phẩm
```bash
curl -X GET "http://your-domain.com/api/admin/product-variants/product/1" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách biến thể sản phẩm thành công",
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "name": "iPhone 15 Pro 128GB Blue",
      "sku": "IP15P-128GB-BLU",
      "price": 99900000,
      "sale_price": 94900000,
      "stock_quantity": 20,
      "weight": 187,
      "status": "active"
    }
  ]
}
```

#### 2.3.4. Cập nhật trạng thái biến thể
```bash
curl -X PATCH "http://your-domain.com/api/admin/product-variants/status/1" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "status": "inactive"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Cập nhật trạng thái thành công",
  "data": {
    "id": 1,
    "status": "inactive"
  }
}
```

### 2.4. Quản lý thuộc tính sản phẩm

#### 2.4.1. Lấy danh sách thuộc tính sản phẩm
```bash
curl -X GET "http://your-domain.com/api/admin/product-attributes?page=1&per_page=20" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách dữ liệu thành công",
  "data": [
    {
      "id": 1,
      "name": "Màu sắc",
      "slug": "mau-sac",
      "type": "color",
      "status": "active",
      "created_at": "2023-10-21T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 20,
    "total": 15
  }
}
```

#### 2.4.2. Tạo thuộc tính sản phẩm mới
```bash
curl -X POST "http://your-domain.com/api/admin/product-attributes" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "name": "Dung lượng",
    "slug": "dung-luong",
    "type": "text",
    "status": "active"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Tạo dữ liệu thành công",
  "data": {
    "id": 2,
    "name": "Dung lượng",
    "slug": "dung-luong",
    "type": "text",
    "status": "active",
    "created_at": "2023-10-21T13:00:00Z"
  }
}
```

#### 2.4.3. Cập nhật trạng thái thuộc tính
```bash
curl -X PATCH "http://your-domain.com/api/admin/product-attributes/status/1" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "status": "inactive"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Cập nhật trạng thái thành công",
  "data": {
    "id": 1,
    "status": "inactive"
  }
}
```

---

## 3. Các trạng thái sản phẩm

### 3.1. Trạng thái sản phẩm (status)
- `active`: Đang bán
- `inactive`: Ngừng bán
- `draft`: Bản nháp
- `out_of_stock`: Hết hàng

### 3.2. Loại thuộc tính (type)
- `text`: Văn bản
- `color`: Màu sắc
- `image`: Hình ảnh
- `number`: Số

---

## 4. Các tham số lọc và sắp xếp

### 4.1. Tham số lọc
- `status`: Trạng thái sản phẩm
- `featured`: Sản phẩm nổi bật (true/false)
- `category_id`: ID danh mục
- `min_price`: Giá tối thiểu
- `max_price`: Giá tối đa
- `in_stock`: Còn hàng (true/false)

### 4.2. Tham số sắp xếp
- `sort_by`: Trường sắp xếp (name, price, created_at, updated_at)
- `sort_order`: Thứ tự sắp xếp (asc, desc)

### 4.3. Tham số phân trang
- `page`: Trang hiện tại
- `per_page`: Số lượng mục trên mỗi trang (tối đa 100)

---

## 5. Mã lỗi

| Mã lỗi | Mô tả |
|--------|--------|
| 400 | Yêu cầu không hợp lệ |
| 401 | Chưa xác thực |
| 403 | Không có quyền truy cập |
| 404 | Không tìm thấy tài nguyên |
| 422 | Dữ liệu không hợp lệ |
| 500 | Lỗi máy chủ nội bộ |

---

## 6. Lưu ý quan trọng

1. **Authentication**: Admin endpoints cần token với quyền phù hợp
2. **Rate Limiting**: Có giới hạn số lượng yêu cầu trong một khoảng thời gian
3. **Validation**: Tất cả dữ liệu đầu vào đều được validate
4. **Error Handling**: Kiểm tra mã lỗi và thông báo lỗi để xử lý phù hợp
5. **Pagination**: Các endpoint danh sách hỗ trợ phân trang với các tham số `page` và `per_page`
6. **Search**: Các endpoint danh sách hỗ trợ tìm kiếm với tham số `search`
7. **Filtering**: Các endpoint danh sách hỗ trợ lọc với các tham số tương ứng
8. **Image Upload**: Sử dụng File API để tải lên hình ảnh sản phẩm
9. **Slug**: Slug sẽ được tự động tạo nếu không cung cấp
10. **Stock**: Kiểm tra số lượng tồn kho trước khi thêm vào giỏ hàng

---

## 7. Ví dụ flow hoàn chỉnh

### 7.1. Flow tạo sản phẩm cho admin

1. **Tạo danh mục sản phẩm**:
```bash
curl -X POST "http://your-domain.com/api/admin/product-categories" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "name": "Điện thoại",
    "slug": "dien-thoai",
    "description": "Các loại điện thoại thông minh",
    "status": "active"
  }'
```

2. **Tạo thuộc tính sản phẩm**:
```bash
curl -X POST "http://your-domain.com/api/admin/product-attributes" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "name": "Màu sắc",
    "slug": "mau-sac",
    "type": "color",
    "status": "active"
  }'
```

3. **Tạo sản phẩm**:
```bash
curl -X POST "http://your-domain.com/api/admin/products" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "name": "iPhone 15 Pro",
    "slug": "iphone-15-pro",
    "sku": "IP15P-128GB-BLU",
    "description": "iPhone 15 Pro với chip A17 Pro",
    "price": 99900000,
    "sale_price": 94900000,
    "stock_quantity": 50,
    "status": "active",
    "featured": true,
    "category_ids": [1]
  }'
```

4. **Tạo biến thể sản phẩm**:
```bash
curl -X POST "http://your-domain.com/api/admin/product-variants" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "product_id": 1,
    "name": "iPhone 15 Pro 128GB Blue",
    "sku": "IP15P-128GB-BLU",
    "price": 99900000,
    "sale_price": 94900000,
    "stock_quantity": 20,
    "status": "active"
  }'
```

### 7.2. Flow tìm kiếm sản phẩm cho khách hàng

1. **Tìm kiếm sản phẩm**:
```bash
curl -X GET "http://your-domain.com/api/products/search?q=iPhone&min_price=50000000&max_price=150000000&sort_by=price&sort_order=asc" \
  -H "Content-Type: application/json"
```

2. **Xem chi tiết sản phẩm**:
```bash
curl -X GET "http://your-domain.com/api/products/1" \
  -H "Content-Type: application/json"
```

3. **Xem biến thể sản phẩm**:
```bash
curl -X GET "http://your-domain.com/api/products/1/variants" \
  -H "Content-Type: application/json"
```

4. **Xem sản phẩm theo danh mục**:
```bash
curl -X GET "http://your-domain.com/api/products/by-category/1" \
  -H "Content-Type: application/json"