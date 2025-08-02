# Inventory API Design

## Tổng quan

API được thiết kế theo chuẩn RESTful với đầy đủ các chức năng CRUD và các tính năng đặc biệt cho quản lý tồn kho.

## Base URL
```
/api/admin/inventory
```

## Authentication
Tất cả endpoints yêu cầu authentication với token và role admin:
```
Authorization: Bearer {token}
```

## Endpoints

### 1. CRUD Operations

#### GET /api/admin/inventory
**Lấy danh sách tồn kho với phân trang và bộ lọc**

**Query Parameters:**
- `search` (string): Tìm kiếm theo tên sản phẩm hoặc mã
- `warehouse_id` (integer): Lọc theo kho hàng
- `product_id` (integer): Lọc theo sản phẩm
- `brand_id` (integer): Lọc theo thương hiệu
- `category_id` (integer): Lọc theo danh mục
- `low_stock` (boolean): Chỉ lấy hàng sắp hết
- `expiring_soon` (boolean): Chỉ lấy hàng sắp hết hạn
- `expired` (boolean): Chỉ lấy hàng đã hết hạn
- `out_of_stock` (boolean): Chỉ lấy hàng hết
- `sort_by` (string): Sắp xếp theo trường (default: updated_at)
- `sort_direction` (string): Hướng sắp xếp (asc/desc, default: desc)
- `per_page` (integer): Số bản ghi mỗi trang (default: 15)
- `page` (integer): Trang hiện tại

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách tồn kho thành công",
  "data": {
    "data": [
      {
        "id": 1,
        "product_id": 1,
        "warehouse_id": 1,
        "quantity": 100,
        "batch_no": "BATCH001",
        "lot_no": "LOT001",
        "expiry_date": "2024-12-31",
        "reserved_quantity": 10,
        "available_quantity": 90,
        "cost_price": 50000,
        "stock_status": "in_stock",
        "is_expired": false,
        "is_expiring_soon": false,
        "created_at": "2024-01-01 10:00:00",
        "updated_at": "2024-01-01 10:00:00",
        "product": {
          "id": 1,
          "name": "Sản phẩm A",
          "code": "SP001",
          "image": "path/to/image.jpg",
          "brand": {
            "id": 1,
            "name": "Thương hiệu A"
          }
        },
        "warehouse": {
          "id": 1,
          "name": "Kho Hà Nội",
          "address": "Hà Nội"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 100,
      "from": 1,
      "to": 15,
      "last_page": 7,
      "has_more_pages": true
    }
  }
}
```

#### GET /api/admin/inventory/{id}
**Lấy thông tin chi tiết tồn kho**

**Response:**
```json
{
  "success": true,
  "message": "Lấy thông tin tồn kho thành công",
  "data": {
    "id": 1,
    "product_id": 1,
    "warehouse_id": 1,
    "quantity": 100,
    "batch_no": "BATCH001",
    "lot_no": "LOT001",
    "expiry_date": "2024-12-31",
    "reserved_quantity": 10,
    "available_quantity": 90,
    "cost_price": 50000,
    "stock_status": "in_stock",
    "is_expired": false,
    "is_expiring_soon": false,
    "created_at": "2024-01-01 10:00:00",
    "updated_at": "2024-01-01 10:00:00",
    "product": {
      "id": 1,
      "name": "Sản phẩm A",
      "code": "SP001",
      "image": "path/to/image.jpg",
      "brand": {
        "id": 1,
        "name": "Thương hiệu A"
      }
    },
    "warehouse": {
      "id": 1,
      "name": "Kho Hà Nội",
      "address": "Hà Nội"
    }
  }
}
```

#### POST /api/admin/inventory
**Tạo tồn kho mới**

**Request Body:**
```json
{
  "product_id": 1,
  "warehouse_id": 1,
  "quantity": 100,
  "batch_no": "BATCH001",
  "lot_no": "LOT001",
  "expiry_date": "2024-12-31",
  "reserved_quantity": 0,
  "cost_price": 50000
}
```

**Response:**
```json
{
  "success": true,
  "message": "Tạo tồn kho thành công",
  "data": {
    "id": 1,
    "product_id": 1,
    "warehouse_id": 1,
    "quantity": 100,
    "available_quantity": 100,
    "batch_no": "BATCH001",
    "lot_no": "LOT001",
    "expiry_date": "2024-12-31",
    "reserved_quantity": 0,
    "cost_price": 50000,
    "stock_status": "in_stock",
    "is_expired": false,
    "is_expiring_soon": false,
    "created_at": "2024-01-01 10:00:00",
    "updated_at": "2024-01-01 10:00:00"
  }
}
```

#### PUT /api/admin/inventory/{id}
**Cập nhật tồn kho**

**Request Body:** (tương tự POST)

**Response:** (tương tự POST)

#### DELETE /api/admin/inventory/{id}
**Xóa tồn kho**

**Response:**
```json
{
  "success": true,
  "message": "Xóa tồn kho thành công",
  "data": null
}
```

### 2. Special Operations

#### POST /api/admin/inventory/import
**Nhập kho (tăng số lượng)**

**Request Body:**
```json
{
  "product_id": 1,
  "warehouse_id": 1,
  "quantity": 50,
  "batch_no": "BATCH002",
  "lot_no": "LOT002",
  "expiry_date": "2024-12-31",
  "cost_price": 50000
}
```

#### POST /api/admin/inventory/export
**Xuất kho (giảm số lượng)**

**Request Body:**
```json
{
  "product_id": 1,
  "warehouse_id": 1,
  "quantity": 10,
  "batch_no": "BATCH001"
}
```

### 3. Reports and Statistics

#### GET /api/admin/inventory/statistics
**Lấy thống kê tồn kho**

**Response:**
```json
{
  "success": true,
  "message": "Lấy thống kê tồn kho thành công",
  "data": {
    "total_products": 100,
    "total_quantity": 5000,
    "total_reserved": 500,
    "total_available": 4500,
    "low_stock_count": 15,
    "out_of_stock_count": 5,
    "expiring_soon_count": 8,
    "expired_count": 2
  }
}
```

#### GET /api/admin/inventory/expiring-soon?days=30
**Lấy hàng sắp hết hạn**

**Query Parameters:**
- `days` (integer): Số ngày trước hạn sử dụng (default: 30)

#### GET /api/admin/inventory/expired
**Lấy hàng đã hết hạn**

#### GET /api/admin/inventory/low-stock?threshold=10
**Lấy hàng sắp hết**

**Query Parameters:**
- `threshold` (integer): Ngưỡng số lượng (default: 10)

### 4. Filter Options

#### GET /api/admin/inventory/filter-options
**Lấy tùy chọn bộ lọc**

**Response:**
```json
{
  "success": true,
  "message": "Lấy tùy chọn bộ lọc thành công",
  "data": {
    "brands": [
      {
        "id": 1,
        "name": "Thương hiệu A"
      }
    ],
    "categories": [
      {
        "id": 1,
        "name": "Danh mục A"
      }
    ],
    "warehouses": [
      {
        "id": 1,
        "name": "Kho Hà Nội"
      }
    ],
    "products": [
      {
        "id": 1,
        "name": "Sản phẩm A",
        "code": "SP001"
      }
    ]
  }
}
```

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "product_id": ["Vui lòng chọn sản phẩm."],
    "quantity": ["Số lượng phải là số nguyên."]
  },
  "data": null
}
```

### Not Found Error (404)
```json
{
  "success": false,
  "message": "Không tìm thấy tồn kho",
  "data": null
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Có lỗi xảy ra",
  "data": null
}
```

## Validation Rules

### Inventory Request
- `product_id`: required, exists:products,id
- `warehouse_id`: required, exists:warehouses,id
- `quantity`: required, integer, min:0
- `batch_no`: nullable, string, max:100
- `lot_no`: nullable, string, max:100
- `expiry_date`: nullable, date, after:today
- `reserved_quantity`: nullable, integer, min:0
- `available_quantity`: nullable, integer, min:0
- `cost_price`: nullable, numeric, min:0

### Unique Constraint
- `product_id` + `warehouse_id` phải unique (không trùng lặp)

## Features

### 1. Automatic Calculations
- `available_quantity` = `quantity` - `reserved_quantity` (tự động tính)

### 2. Stock Status
- `out_of_stock`: available_quantity <= 0
- `low_stock`: available_quantity <= 10
- `in_stock`: available_quantity > 10

### 3. Expiry Management
- `is_expired`: expiry_date < today
- `is_expiring_soon`: expiry_date <= today + 30 days

### 4. Batch/Lot Management
- Quản lý lô hàng với `batch_no` và `lot_no`
- Hỗ trợ FIFO (First In, First Out)

### 5. Reserved Quantity
- Quản lý số lượng đã giữ chỗ cho đơn hàng
- Tránh overselling

## Usage Examples

### Tạo tồn kho mới
```bash
curl -X POST /api/admin/inventory \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "warehouse_id": 1,
    "quantity": 100,
    "batch_no": "BATCH001",
    "expiry_date": "2024-12-31",
    "cost_price": 50000
  }'
```

### Lấy danh sách với bộ lọc
```bash
curl -X GET "/api/admin/inventory?search=product&warehouse_id=1&low_stock=1&per_page=20" \
  -H "Authorization: Bearer {token}"
```

### Nhập kho
```bash
curl -X POST /api/admin/inventory/import \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "warehouse_id": 1,
    "quantity": 50,
    "batch_no": "BATCH002"
  }'
``` 