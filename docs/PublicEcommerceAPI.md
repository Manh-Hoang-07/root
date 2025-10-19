# Public E-commerce API Documentation

## Overview
API này cung cấp các endpoint cho người dùng công khai để duyệt sản phẩm, quản lý giỏ hàng và đặt hàng. Hệ thống được trang bị xác thực toàn cục, cho phép cả người dùng chưa đăng nhập (guest) và đã đăng nhập sử dụng API.

## Đặc điểm xác thực
- **Xác thực toàn cục**: Tất cả API đều có thể nhận diện user nếu có bearer token
- **Không bắt buộc xác thực**: Các API công cộng vẫn hoạt động bình thường với guest users
- **Tự động chuyển đổi**: Nếu có token, hệ thống sẽ tự động sử dụng thông tin user đã đăng nhập
- **Linh hoạt**: Cùng một endpoint có thể hoạt động với cả guest và authenticated user

## Base URL
```
/api
```

## Authentication
API hỗ trợ 3 phương thức xác thực:
1. **Authorization Header (Khuyến khích)**: `Authorization: Bearer your_token`
2. **Query Parameter**: `?token=your_token`
3. **Cookie**: `auth_token=your_token`

### Lấy Token
```bash
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

## Product Endpoints

### Get All Products
```
GET /products
```

Query Parameters:
- `limit` (optional): Number of products per page (default: 12)
- `sort_by` (optional): Field to sort by (default: created_at)
- `sort_order` (optional): Sort direction (asc/desc, default: desc)

Response:
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Product Name",
        "description": "Product description",
        "price": 100000,
        "sale_price": 90000,
        "sku": "PRD001",
        "stock_quantity": 50,
        "is_featured": true,
        "status": "active",
        "category": {
          "id": 1,
          "name": "Category Name",
          "slug": "category-name"
        },
        "variants": [...]
      }
    ],
    "current_page": 1,
    "total": 10
  },
  "message": "Lấy danh sách sản phẩm thành công"
}
```

### Get Product Details
```
GET /products/{id}
```

Response:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Product Name",
    "description": "Product description",
    "price": 100000,
    "sale_price": 90000,
    "sku": "PRD001",
    "stock_quantity": 50,
    "is_featured": true,
    "status": "active",
    "category": {...},
    "variants": [...],
    "attributes": [...]
  },
  "message": "Lấy chi tiết sản phẩm thành công"
}
```

### Get Featured Products
```
GET /products/featured
```

Query Parameters:
- `limit` (optional): Number of products (default: 12)

### Search Products
```
GET /products/search
```

Query Parameters:
- `q` (required): Search query
- `category` (optional): Category ID filter
- `min_price` (optional): Minimum price filter
- `max_price` (optional): Maximum price filter
- `sort_by` (optional): Field to sort by (default: created_at)
- `sort_order` (optional): Sort direction (asc/desc, default: desc)
- `limit` (optional): Number of products per page (default: 12)

### Get Products by Category
```
GET /products/by-category/{categoryId}
```

Query Parameters:
- `sort_by` (optional): Field to sort by (default: created_at)
- `sort_order` (optional): Sort direction (asc/desc, default: desc)
- `limit` (optional): Number of products per page (default: 12)

### Get Product Variants
```
GET /products/{id}/variants
```

## Product Category Endpoints

### Get All Categories
```
GET /product-categories
```

### Get Category Details
```
GET /product-categories/{id}
```

### Get Category Tree
```
GET /product-categories/tree
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Parent Category",
      "slug": "parent-category",
      "children": [
        {
          "id": 2,
          "name": "Child Category",
          "slug": "child-category",
          "children": []
        }
      ]
    }
  ],
  "message": "Lấy cây danh mục thành công"
}
```

### Get Products by Category
```
GET /product-categories/{id}/products
```

Query Parameters:
- `sort_by` (optional): Field to sort by (default: created_at)
- `sort_order` (optional): Sort direction (asc/desc, default: desc)
- `limit` (optional): Number of products per page (default: 12)

## Cart Endpoints

### Get Cart
```
GET /cart
```

**Hỗ trợ cả guest và authenticated user**:
- Guest: Cart được lưu theo session ID
- Authenticated User: Cart được lưu theo user ID

Response:
```json
{
  "success": true,
  "data": {
    "cart_id": "cart_abc123",
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "product_variant_id": null,
        "quantity": 2,
        "product": {...},
        "variant": null
      }
    ],
    "subtotal": 180000,
    "tax_amount": 18000,
    "shipping_amount": 30000,
    "discount_amount": 0,
    "total_amount": 228000,
    "currency": "VND"
  },
  "message": "Lấy giỏ hàng thành công"
}
```

### Add Item to Cart
```
POST /cart
```

**Hỗ trợ cả guest và authenticated user**

Request Body:
```json
{
  "product_id": 1,
  "product_variant_id": null,
  "quantity": 2
}
```

### Update Cart Item
```
PUT /cart/{id}
```

**Hỗ trợ cả guest và authenticated user**

Request Body:
```json
{
  "quantity": 3
}
```

### Remove Item from Cart
```
DELETE /cart/{id}
```

**Hỗ trợ cả guest và authenticated user**

### Clear Cart
```
DELETE /cart
```

**Hỗ trợ cả guest và authenticated user**

### Apply Coupon Code
```
POST /cart/apply-coupon
```

**Hỗ trợ cả guest và authenticated user**

Request Body:
```json
{
  "code": "SAVE10"
}
```

### Remove Coupon Code
```
DELETE /cart/remove-coupon
```

**Hỗ trợ cả guest và authenticated user**

## Order Endpoints

### Create Order (Unified for Guest and Authenticated Users)
```
POST /orders
```

**Endpoint duy nhất cho cả guest và authenticated user**:
- Nếu có token: Sử dụng thông tin user đã đăng nhập và tự động lấy giỏ hàng của user
- Nếu không có token: Xử lý như guest checkout và yêu cầu `items`

Request Body (Authenticated User):
```json
{
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "0123456789",
  "shipping_address": {
    "address": "123 Street Name",
    "city": "City Name",
    "postal_code": "12345",
    "country": "Vietnam"
  },
  "billing_address": {
    "address": "123 Street Name",
    "city": "City Name",
    "postal_code": "12345",
    "country": "Vietnam"
  },
  "notes": "Special instructions",
  "payment_method": "cod",
  "shipping_method": "standard"
}
```

**Lưu ý**: `cart_id` là tùy chọn cho authenticated user, hệ thống sẽ tự động lấy giỏ hàng của user.

Request Body (Guest User):
```json
{
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "0123456789",
  "shipping_address": {
    "address": "123 Street Name",
    "city": "City Name",
    "postal_code": "12345",
    "country": "Vietnam"
  },
  "billing_address": {
    "address": "123 Street Name",
    "city": "City Name",
    "postal_code": "12345",
    "country": "Vietnam"
  },
  "notes": "Special instructions",
  "payment_method": "cod",
  "shipping_method": "standard",
  "items": [
    {
      "product_id": 1,
      "product_variant_id": null,
      "quantity": 2,
      "unit_price": 90000
    }
  ]
}
```

### Get Order Details
```
GET /orders/{id}
```

**Hỗ trợ cả guest và authenticated user**:
- Authenticated User: Có thể xem các đơn hàng của mình
- Guest: Cần cung cấp email đúng với email trong đơn hàng

### Get Order Details (Guest)
```
GET /orders/guest/{orderNumber}/{email}
```

### Process Payment
```
POST /orders/{id}/payment
```

**Hỗ trợ cả guest và authenticated user**

Request Body:
```json
{
  "payment_method": "credit_card",
  "transaction_id": "TXN123456",
  "payment_details": {
    "card_number": "****-****-****-1234",
    "card_holder": "John Doe",
    "expiry_date": "12/25",
    "cvv": "123"
  }
}
```

### Get Order Status
```
GET /orders/status/{orderNumber}
```

**Hỗ trợ cả guest và authenticated user**

Response:
```json
{
  "success": true,
  "data": {
    "order_number": "ORD-20231018-0001",
    "status": "confirmed",
    "payment_status": "paid",
    "shipping_status": "preparing"
  },
  "message": "Lấy trạng thái đơn hàng thành công"
}
```

### Get User Address (Authenticated Users Only)
```
GET /orders/user-address
```

**Chỉ hoạt động với authenticated user**

## Payment Methods
- `cod`: Cash on Delivery
- `bank_transfer`: Bank Transfer
- `credit_card`: Credit Card

## Shipping Methods
- `standard`: Standard Shipping (30,000 VND)
- `express`: Express Shipping (50,000 VND)

## Error Responses
All endpoints return error responses in the following format:
```json
{
  "success": false,
  "data": null,
  "message": "Error message description"
}
```

## Status Codes
- `200`: Success
- `400`: Bad Request
- `401`: Unauthorized (chỉ cho các endpoint bắt buộc xác thực)
- `404`: Not Found
- `500`: Internal Server Error

---

## CURL Examples

### Product Endpoints

#### Get All Products
```bash
curl -X GET "http://localhost:8000/api/products?limit=10&sort_by=price&sort_order=asc" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

#### Get Product Details
```bash
curl -X GET "http://localhost:8000/api/products/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

#### Get Featured Products
```bash
curl -X GET "http://localhost:8000/api/products/featured?limit=8" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

### Cart Endpoints

#### Get Cart (Guest)
```bash
curl -X GET "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

#### Get Cart (Authenticated User)
```bash
curl -X GET "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_auth_token_here"
```

#### Add Item to Cart (Guest)
```bash
curl -X POST "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "product_variant_id": null,
    "quantity": 2
  }'
```

#### Add Item to Cart (Authenticated User)
```bash
curl -X POST "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_auth_token_here" \
  -d '{
    "product_id": 1,
    "product_variant_id": null,
    "quantity": 2
  }'
```

### Order Endpoints

#### Create Order (Guest)
```bash
curl -X POST "http://localhost:8000/api/orders" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Nguyen Van A",
    "customer_email": "nguyenvana@example.com",
    "customer_phone": "0123456789",
    "shipping_address": {
      "address": "123 Nguyen Hue Street",
      "city": "Ho Chi Minh City",
      "postal_code": "700000",
      "country": "Vietnam"
    },
    "payment_method": "cod",
    "shipping_method": "standard"
  }'
```

#### Create Order (Authenticated User)
```bash
curl -X POST "http://localhost:8000/api/orders" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_auth_token_here" \
  -d '{
    "customer_name": "Nguyen Van A",
    "customer_email": "nguyenvana@example.com",
    "customer_phone": "0123456789",
    "shipping_address": {
      "address": "123 Nguyen Hue Street",
      "city": "Ho Chi Minh City",
      "postal_code": "700000",
      "country": "Vietnam"
    },
    "payment_method": "cod",
    "shipping_method": "standard"
  }'
```

#### Get Order Details (Authenticated User)
```bash
curl -X GET "http://localhost:8000/api/orders/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_auth_token_here"
```

#### Get Order Details (Guest)
```bash
curl -X GET "http://localhost:8000/api/orders/guest/ORD-20231018-0001/nguyenvana@example.com" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

### Authentication Endpoints

#### Login
```bash
curl -X POST "http://localhost:8000/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

#### Register
```bash
curl -X POST "http://localhost:8000/api/register" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "nguyenvana",
    "email": "nguyenvana@example.com",
    "password": "password123",
    "phone": "0123456789"
  }'
```

## Testing Tips

1. **Xác thực toàn cục**: Bạn có thể thêm token vào bất kỳ request nào để hệ thống nhận diện user
2. **Cart Management**: Guest cart được lưu theo session, authenticated user cart được lưu theo user ID
3. **Base URL**: Replace `http://localhost:8000` with your actual API base URL
4. **Authentication**: Thay `your_auth_token_here` với token thực từ API login
5. **Product IDs**: Thay product IDs (1, 2, etc.) với IDs thực từ database
6. **Error Handling**: Luôn kiểm tra response status code và JSON body cho error messages
7. **Content-Type**: Luôn include `Content-Type: application/json` header cho POST/PUT requests
8. **Accept Header**: Include `Accept: application/json` để nhận JSON responses

## Complete Workflow Example

### Workflow cho Guest User
```bash
# 1. Get products
curl -X GET "http://localhost:8000/api/products" -H "Accept: application/json"

# 2. Add product to cart (guest)
curl -X POST "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1, "quantity": 2}'

# 3. View cart (guest)
curl -X GET "http://localhost:8000/api/cart" -H "Accept: application/json"

# 4. Place order as guest
curl -X POST "http://localhost:8000/api/orders" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Test User",
    "customer_email": "test@example.com",
    "customer_phone": "0123456789",
    "shipping_address": {
      "address": "123 Test Street",
      "city": "Test City",
      "country": "Vietnam"
    },
    "payment_method": "cod",
    "shipping_method": "standard"
  }'
```

### Workflow cho Authenticated User
```bash
# 1. Login
TOKEN=$(curl -s -X POST "http://localhost:8000/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password123"}' | \
  jq -r '.data.token')

# 2. Get products
curl -X GET "http://localhost:8000/api/products" -H "Accept: application/json"

# 3. Add product to cart (authenticated)
curl -X POST "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"product_id": 1, "quantity": 2}'

# 4. View cart (authenticated)
curl -X GET "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

# 5. Place order (authenticated)
curl -X POST "http://localhost:8000/api/orders" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "customer_name": "Test User",
    "customer_email": "test@example.com",
    "customer_phone": "0123456789",
    "shipping_address": {
      "address": "123 Test Street",
      "city": "Test City",
      "country": "Vietnam"
    },
    "payment_method": "cod",
    "shipping_method": "standard"
  }'
```

## Lợi ích của Xác thực Toàn cục

1. **Trải nghiệm liền mạch**: User có thể đăng nhập ở bất kỳ đâu và tiếp tục công việc
2. **Không cần quản lý nhiều endpoint**: Cùng một endpoint hoạt động với cả guest và authenticated user
3. **Tự động chuyển đổi**: Hệ thống tự động nhận diện và áp dụng logic phù hợp
4. **Linh hoạt**: Frontend có thể quyết định khi nào yêu cầu xác thực