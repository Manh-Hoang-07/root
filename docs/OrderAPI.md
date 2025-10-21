# API Đặt Hàng (Order API)

Tài liệu này mô tả các API endpoint cho chức năng đặt hàng, hỗ trợ cả khách hàng và admin.

## Base URL
```
http://your-domain.com/api
```

## Authentication
- **Khách hàng**: Có thể đặt hàng mà không cần đăng nhập (guest checkout)
- **Người dùng đã đăng nhập**: Sử dụng Bearer Token (Sanctum)
- **Admin**: Cần đăng nhập và có quyền phù hợp

---

## 1. API cho Khách hàng và Người dùng đã đăng nhập

### 1.1. Quá trình checkout tách biệt (Split Checkout)

#### 1.1.1. Cập nhật thông tin địa chỉ
```bash
curl -X POST "http://your-domain.com/api/checkout/address" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "customer_name": "Nguyễn Văn A",
    "customer_email": "nguyenvana@example.com",
    "customer_phone": "0123456789",
    "shipping_address": "123 Đường ABC, Quận 1, TP.HCM",
    "billing_address": "123 Đường ABC, Quận 1, TP.HCM",
    "notes": "Giao hàng vào buổi sáng"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Lưu thông tin địa chỉ thành công",
  "data": null
}
```

#### 1.1.2. Tạo đơn hàng
```bash
curl -X POST "http://your-domain.com/api/checkout/order" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "cart_header_id": "abc123",
    "payment_method": "cod",
    "shipping_method": "standard"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Tạo đơn hàng thành công",
  "data": {
    "id": 1,
    "order_number": "ORD-20231021-001",
    "status": "pending",
    "payment_status": "pending",
    "shipping_status": "pending",
    "total_amount": 500000,
    "customer_name": "Nguyễn Văn A",
    "customer_email": "nguyenvana@example.com",
    "customer_phone": "0123456789",
    "shipping_address": "123 Đường ABC, Quận 1, TP.HCM",
    "items": [
      {
        "id": 1,
        "product_name": "Sản phẩm A",
        "quantity": 2,
        "price": 250000
      }
    ]
  }
}
```

### 1.2. Quá trình checkout hợp nhất (Unified Checkout)

#### 1.2.1. Tạo đơn hàng (khách vãng lai)
```bash
curl -X POST "http://your-domain.com/api/orders" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Nguyễn Văn A",
    "customer_email": "nguyenvana@example.com",
    "customer_phone": "0123456789",
    "shipping_address": "123 Đường ABC, Quận 1, TP.HCM",
    "billing_address": "123 Đường ABC, Quận 1, TP.HCM",
    "payment_method": "cod",
    "shipping_method": "standard",
    "cart_header_id": "abc123",
    "notes": "Giao hàng vào buổi sáng"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Tạo đơn hàng thành công",
  "data": {
    "id": 1,
    "order_number": "ORD-20231021-001",
    "status": "pending",
    "payment_status": "pending",
    "shipping_status": "pending",
    "total_amount": 500000,
    "customer_name": "Nguyễn Văn A",
    "customer_email": "nguyenvana@example.com",
    "customer_phone": "0123456789",
    "shipping_address": "123 Đường ABC, Quận 1, TP.HCM",
    "items": [
      {
        "id": 1,
        "product_name": "Sản phẩm A",
        "quantity": 2,
        "price": 250000
      }
    ]
  }
}
```

### 1.3. Xem chi tiết đơn hàng

#### 1.3.1. Xem đơn hàng (người dùng đã đăng nhập)
```bash
curl -X GET "http://your-domain.com/api/orders/1" \
  -H "Authorization: Bearer {token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy thông tin chi tiết thành công",
  "data": {
    "id": 1,
    "order_number": "ORD-20231021-001",
    "status": "pending",
    "payment_status": "pending",
    "shipping_status": "pending",
    "total_amount": 500000,
    "customer_name": "Nguyễn Văn A",
    "customer_email": "nguyenvana@example.com",
    "customer_phone": "0123456789",
    "shipping_address": "123 Đường ABC, Quận 1, TP.HCM",
    "items": [
      {
        "id": 1,
        "product_name": "Sản phẩm A",
        "quantity": 2,
        "price": 250000
      }
    ]
  }
}
```

#### 1.3.2. Xem đơn hàng (khách vãng lai)
```bash
curl -X GET "http://your-domain.com/api/orders/guest/ORD-20231021-001/nguyenvana@example.com"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy chi tiết đơn hàng thành công",
  "data": {
    "id": 1,
    "order_number": "ORD-20231021-001",
    "status": "pending",
    "payment_status": "pending",
    "shipping_status": "pending",
    "total_amount": 500000,
    "customer_name": "Nguyễn Văn A",
    "customer_email": "nguyenvana@example.com",
    "customer_phone": "0123456789",
    "shipping_address": "123 Đường ABC, Quận 1, TP.HCM",
    "items": [
      {
        "id": 1,
        "product_name": "Sản phẩm A",
        "quantity": 2,
        "price": 250000
      }
    ]
  }
}
```

### 1.4. Xử lý thanh toán
```bash
curl -X POST "http://your-domain.com/api/orders/1/payment" \
  -H "Content-Type: application/json" \
  -d '{
    "payment_method": "credit_card",
    "payment_details": {
      "card_number": "****-****-****-1234",
      "transaction_id": "txn_123456"
    }
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Thanh toán thành công",
  "data": {
    "payment_status": "paid",
    "transaction_id": "txn_123456"
  }
}
```

### 1.5. Kiểm tra trạng thái đơn hàng
```bash
curl -X GET "http://your-domain.com/api/orders/status/ORD-20231021-001"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy trạng thái đơn hàng thành công",
  "data": {
    "order_number": "ORD-20231021-001",
    "status": "confirmed",
    "payment_status": "paid",
    "shipping_status": "preparing"
  }
}
```

### 1.6. Lấy thông tin địa chỉ người dùng
```bash
curl -X GET "http://your-domain.com/api/orders/user-address" \
  -H "Authorization: Bearer {token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy thông tin địa chỉ thành công",
  "data": {
    "customer_name": "Nguyễn Văn A",
    "customer_email": "nguyenvana@example.com",
    "customer_phone": "0123456789",
    "default_shipping_address": "123 Đường ABC, Quận 1, TP.HCM",
    "default_billing_address": "123 Đường ABC, Quận 1, TP.HCM"
  }
}
```

---

## 2. API cho Admin

### 2.1. Lấy danh sách đơn hàng
```bash
curl -X GET "http://your-domain.com/api/admin/orders" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách đơn hàng thành công",
  "data": [
    {
      "id": 1,
      "order_number": "ORD-20231021-001",
      "status": "pending",
      "payment_status": "pending",
      "shipping_status": "pending",
      "total_amount": 500000,
      "customer_name": "Nguyễn Văn A",
      "customer_email": "nguyenvana@example.com",
      "created_at": "2023-10-21T10:30:00Z"
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

### 2.2. Xem chi tiết đơn hàng
```bash
curl -X GET "http://your-domain.com/api/admin/orders/1" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy thông tin chi tiết thành công",
  "data": {
    "id": 1,
    "order_number": "ORD-20231021-001",
    "status": "pending",
    "payment_status": "pending",
    "shipping_status": "pending",
    "total_amount": 500000,
    "customer_name": "Nguyễn Văn A",
    "customer_email": "nguyenvana@example.com",
    "customer_phone": "0123456789",
    "shipping_address": "123 Đường ABC, Quận 1, TP.HCM",
    "items": [
      {
        "id": 1,
        "product_name": "Sản phẩm A",
        "quantity": 2,
        "price": 250000
      }
    ],
    "user": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "email": "nguyenvana@example.com"
    }
  }
}
```

### 2.3. Cập nhật trạng thái đơn hàng
```bash
curl -X PATCH "http://your-domain.com/api/admin/orders/status/1" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "status": "confirmed"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Cập nhật trạng thái đơn hàng thành công",
  "data": {
    "id": 1,
    "status": "confirmed"
  }
}
```

### 2.4. Cập nhật trạng thái thanh toán
```bash
curl -X PATCH "http://your-domain.com/api/admin/orders/payment-status/1" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "payment_status": "paid"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Cập nhật trạng thái thanh toán thành công",
  "data": {
    "id": 1,
    "payment_status": "paid"
  }
}
```

### 2.5. Cập nhật trạng thái vận chuyển
```bash
curl -X PATCH "http://your-domain.com/api/admin/orders/shipping-status/1" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "shipping_status": "shipped",
    "tracking_number": "TRACK123456"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Cập nhật trạng thái vận chuyển thành công",
  "data": {
    "id": 1,
    "shipping_status": "shipped",
    "tracking_number": "TRACK123456"
  }
}
```

### 2.6. Thêm sản phẩm vào đơn hàng
```bash
curl -X POST "http://your-domain.com/api/admin/orders/1/items" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "product_id": 2,
    "quantity": 1,
    "price": 150000
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Thêm sản phẩm vào đơn hàng thành công",
  "data": {
    "id": 2,
    "product_id": 2,
    "product_name": "Sản phẩm B",
    "quantity": 1,
    "price": 150000
  }
}
```

### 2.7. Cập nhật sản phẩm trong đơn hàng
```bash
curl -X PATCH "http://your-domain.com/api/admin/orders/1/items/2" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "quantity": 2,
    "price": 140000
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Cập nhật sản phẩm trong đơn hàng thành công",
  "data": {
    "id": 2,
    "product_id": 2,
    "product_name": "Sản phẩm B",
    "quantity": 2,
    "price": 140000
  }
}
```

### 2.8. Xóa sản phẩm khỏi đơn hàng
```bash
curl -X DELETE "http://your-domain.com/api/admin/orders/1/items/2" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Xóa sản phẩm khỏi đơn hàng thành công",
  "data": null
}
```

### 2.9. Tính lại tổng tiền đơn hàng
```bash
curl -X POST "http://your-domain.com/api/admin/orders/1/recalculate" \
  -H "Authorization: Bearer {admin_token}"
```

**Response:**
```json
{
  "success": true,
  "message": "Tính lại tổng tiền thành công",
  "data": {
    "id": 1,
    "subtotal": 640000,
    "tax": 64000,
    "shipping": 30000,
    "total_amount": 734000
  }
}
```

### 2.10. Xác nhận đơn hàng
```bash
curl -X POST "http://your-domain.com/api/admin/orders/1/confirm" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "note": "Đã xác nhận đơn hàng, sẽ chuẩn bị hàng trong ngày"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Xác nhận đơn hàng thành công",
  "data": {
    "id": 1,
    "status": "confirmed",
    "confirmed_at": "2023-10-21T11:00:00Z"
  }
}
```

### 2.11. Hủy đơn hàng
```bash
curl -X POST "http://your-domain.com/api/admin/orders/1/cancel" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "reason": "Sản phẩm hết hàng"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Hủy đơn hàng thành công",
  "data": {
    "id": 1,
    "status": "cancelled",
    "cancelled_at": "2023-10-21T11:30:00Z",
    "cancellation_reason": "Sản phẩm hết hàng"
  }
}
```

### 2.12. Cập nhật trạng thái hàng loạt
```bash
curl -X POST "http://your-domain.com/api/admin/orders/bulk-update-status" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "ids": [1, 2, 3],
    "status": "confirmed"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Cập nhật trạng thái hàng loạt thành công",
  "data": {
    "updated_count": 3,
    "failed_count": 0
  }
}
```

---

## 3. Các trạng thái đơn hàng

### 3.1. Trạng thái đơn hàng (status)
- `pending`: Chờ xác nhận
- `confirmed`: Đã xác nhận
- `preparing`: Đang chuẩn bị
- `ready`: Sẵn sàng giao hàng
- `shipped`: Đang giao hàng
- `delivered`: Đã giao hàng
- `cancelled`: Đã hủy
- `returned`: Đã trả hàng

### 3.2. Trạng thái thanh toán (payment_status)
- `pending`: Chờ thanh toán
- `paid`: Đã thanh toán
- `failed`: Thanh toán thất bại
- `refunded`: Đã hoàn tiền
- `partially_refunded`: Hoàn tiền một phần

### 3.3. Trạng thái vận chuyển (shipping_status)
- `pending`: Chờ giao hàng
- `preparing`: Đang chuẩn bị
- `shipped`: Đang giao hàng
- `delivered`: Đã giao hàng
- `returned`: Đã trả hàng

---

## 4. Các phương thức thanh toán và vận chuyển

### 4.1. Phương thức thanh toán (payment_method)
- `cod`: Thanh toán khi nhận hàng (Cash on Delivery)
- `bank_transfer`: Chuyển khoản ngân hàng
- `credit_card`: Thẻ tín dụng
- `debit_card`: Thẻ ghi nợ
- `ewallet`: Ví điện tử

### 4.2. Phương thức vận chuyển (shipping_method)
- `standard`: Giao hàng tiêu chuẩn (3-5 ngày)
- `express`: Giao hàng nhanh (1-2 ngày)
- `overnight`: Giao hàng trong ngày
- `pickup`: Tại cửa hàng

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

---

## 7. Ví dụ flow hoàn chỉnh

### 7.1. Flow đặt hàng cho khách vãng lai

1. **Thêm sản phẩm vào giỏ hàng**:
```bash
curl -X POST "http://your-domain.com/api/cart" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 2,
    "variant_id": 1
  }'
```

2. **Tạo đơn hàng**:
```bash
curl -X POST "http://your-domain.com/api/orders" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Nguyễn Văn A",
    "customer_email": "nguyenvana@example.com",
    "customer_phone": "0123456789",
    "shipping_address": "123 Đường ABC, Quận 1, TP.HCM",
    "payment_method": "cod",
    "shipping_method": "standard"
  }'
```

3. **Kiểm tra trạng thái đơn hàng**:
```bash
curl -X GET "http://your-domain.com/api/orders/status/ORD-20231021-001"
```

### 7.2. Flow xử lý đơn hàng cho admin

1. **Xem danh sách đơn hàng**:
```bash
curl -X GET "http://your-domain.com/api/admin/orders" \
  -H "Authorization: Bearer {admin_token}"
```

2. **Xem chi tiết đơn hàng**:
```bash
curl -X GET "http://your-domain.com/api/admin/orders/1" \
  -H "Authorization: Bearer {admin_token}"
```

3. **Xác nhận đơn hàng**:
```bash
curl -X POST "http://your-domain.com/api/admin/orders/1/confirm" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "note": "Đã xác nhận đơn hàng"
  }'
```

4. **Cập nhật trạng thái vận chuyển**:
```bash
curl -X PATCH "http://your-domain.com/api/admin/orders/shipping-status/1" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {admin_token}" \
  -d '{
    "shipping_status": "shipped",
    "tracking_number": "TRACK123456"
  }'