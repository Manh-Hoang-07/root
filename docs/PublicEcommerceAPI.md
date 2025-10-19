# Public E-commerce API Documentation

## Overview
This API provides endpoints for public users to browse products, manage cart, and place orders without requiring authentication.

## Base URL
```
/api
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

Response:
```json
{
  "success": true,
  "data": {
    "id": "cart_abc123",
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "product_variant_id": null,
        "product_name": "Product Name",
        "product_sku": "PRD001",
        "variant_name": null,
        "quantity": 2,
        "unit_price": 90000,
        "total_price": 180000,
        "product": {...},
        "variant": null
      }
    ],
    "subtotal": 180000,
    "tax_amount": 18000,
    "shipping_amount": 30000,
    "discount_amount": 0,
    "total_amount": 228000,
    "coupon_code": null,
    "currency": "VND"
  },
  "message": "Lấy giỏ hàng thành công"
}
```

### Add Item to Cart
```
POST /cart
```

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

### Clear Cart
```
DELETE /cart
```

### Apply Coupon Code
```
POST /cart/apply-coupon
```

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

## Order Endpoints

### Create Order (Authenticated Users)
```
POST /orders
```

Request Body:
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
  "cart_id": "cart_abc123"
}
```

### Guest Checkout (No Authentication Required)
```
POST /orders/guest
```

Request Body:
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

### Get Order Details (Authenticated Users)
```
GET /orders/{id}
```

### Get Order Details (Guest Users)
```
GET /orders/guest/{orderNumber}/{email}
```

### Process Payment
```
POST /orders/{id}/payment
```

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

#### Search Products
```bash
curl -X GET "http://localhost:8000/api/products/search?q=iphone&category=1&min_price=1000000&max_price=5000000&limit=10" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

#### Get Products by Category
```bash
curl -X GET "http://localhost:8000/api/products/by-category/1?sort_by=name&sort_order=asc&limit=12" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

#### Get Product Variants
```bash
curl -X GET "http://localhost:8000/api/products/1/variants" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

### Product Category Endpoints

#### Get All Categories
```bash
curl -X GET "http://localhost:8000/api/product-categories" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

#### Get Category Tree
```bash
curl -X GET "http://localhost:8000/api/product-categories/tree" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

#### Get Category Products
```bash
curl -X GET "http://localhost:8000/api/product-categories/1/products?limit=10" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

### Cart Endpoints

#### Get Cart
```bash
curl -X GET "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Cart-ID: cart_abc123def456"
```

#### Add Item to Cart
```bash
curl -X POST "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Cart-ID: cart_abc123def456" \
  -d '{
    "product_id": 1,
    "product_variant_id": null,
    "quantity": 2
  }'
```

#### Add Product Variant to Cart
```bash
curl -X POST "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Cart-ID: cart_abc123def456" \
  -d '{
    "product_id": 1,
    "product_variant_id": 5,
    "quantity": 1
  }'
```

#### Update Cart Item
```bash
curl -X PUT "http://localhost:8000/api/cart/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Cart-ID: cart_abc123def456" \
  -d '{
    "quantity": 3
  }'
```

#### Remove Item from Cart
```bash
curl -X DELETE "http://localhost:8000/api/cart/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Cart-ID: cart_abc123def456"
```

#### Clear Cart
```bash
curl -X DELETE "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Cart-ID: cart_abc123def456"
```

#### Apply Coupon Code
```bash
curl -X POST "http://localhost:8000/api/cart/apply-coupon" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Cart-ID: cart_abc123def456" \
  -d '{
    "code": "SAVE10"
  }'
```

#### Remove Coupon Code
```bash
curl -X DELETE "http://localhost:8000/api/cart/remove-coupon" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Cart-ID: cart_abc123def456"
```

### Order Endpoints

#### Create Order (Guest Checkout)
```bash
curl -X POST "http://localhost:8000/api/orders/guest" \
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
    "billing_address": {
      "address": "123 Nguyen Hue Street",
      "city": "Ho Chi Minh City",
      "postal_code": "700000",
      "country": "Vietnam"
    },
    "notes": "Giao hàng vào buổi chiều",
    "payment_method": "cod",
    "shipping_method": "standard",
    "items": [
      {
        "product_id": 1,
        "product_variant_id": null,
        "quantity": 2,
        "unit_price": 90000
      },
      {
        "product_id": 2,
        "product_variant_id": 3,
        "quantity": 1,
        "unit_price": 150000
      }
    ]
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
    "billing_address": {
      "address": "123 Nguyen Hue Street",
      "city": "Ho Chi Minh City",
      "postal_code": "700000",
      "country": "Vietnam"
    },
    "notes": "Giao hàng vào buổi chiều",
    "payment_method": "cod",
    "shipping_method": "standard",
    "cart_id": "cart_abc123"
  }'
```

#### Get Order Details (Guest)
```bash
curl -X GET "http://localhost:8000/api/orders/guest/ORD-20231018-0001/nguyenvana@example.com" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json"
```

#### Get Order Details (Authenticated)
```bash
curl -X GET "http://localhost:8000/api/orders/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_auth_token_here"
```

#### Process Payment (Credit Card)
```bash
curl -X POST "http://localhost:8000/api/orders/1/payment" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_auth_token_here" \
  -d '{
    "payment_method": "credit_card",
    "transaction_id": "TXN123456789",
    "payment_details": {
      "card_number": "4111111111111111",
      "card_holder": "NGUYEN VAN A",
      "expiry_date": "12/25",
      "cvv": "123"
    }
  }'
```

#### Process Payment (Bank Transfer)
```bash
curl -X POST "http://localhost:8000/api/orders/1/payment" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_auth_token_here" \
  -d '{
    "payment_method": "bank_transfer",
    "transaction_id": "BANK123456789",
    "payment_details": {
      "bank_name": "Vietcombank",
      "account_number": "1234567890",
      "account_holder": "NGUYEN VAN A"
    }
  }'
```

#### Get Order Status
```bash
curl -X GET "http://localhost:8000/api/orders/status/ORD-20231018-0001" \
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
    "name": "Nguyen Van A",
    "email": "nguyenvana@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

## Testing Tips

1. **Cart Management**: For cart operations, include a cart ID in the `X-Cart-ID` header to maintain cart state across requests. The cart ID can be any unique identifier (e.g., `cart_abc123def456`).

2. **Base URL**: Replace `http://localhost:8000` with your actual API base URL.

3. **Authentication**: For authenticated endpoints, replace `your_auth_token_here` with a valid JWT token obtained from login.

4. **Product IDs**: Replace product IDs (1, 2, etc.) with actual product IDs from your database.

5. **Error Handling**: Always check the response status code and JSON body for error messages.

6. **Content-Type**: Always include `Content-Type: application/json` header for POST/PUT requests.

7. **Accept Header**: Include `Accept: application/json` to receive JSON responses.

## Complete Workflow Example

```bash
# 1. Get products
curl -X GET "http://localhost:8000/api/products" -H "Accept: application/json"

# 2. Add product to cart
curl -X POST "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Cart-ID: cart_test_123456" \
  -d '{"product_id": 1, "quantity": 2}'

# 3. View cart
curl -X GET "http://localhost:8000/api/cart" \
  -H "Accept: application/json" \
  -H "X-Cart-ID: cart_test_123456"

# 4. Place order as guest
curl -X POST "http://localhost:8000/api/orders/guest" \
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
    "shipping_method": "standard",
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "unit_price": 90000
      }
    ]
  }'

# 5. Check order status
curl -X GET "http://localhost:8000/api/orders/status/ORD-20231018-0001" \
  -H "Accept: application/json"
```