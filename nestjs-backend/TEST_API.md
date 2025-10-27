# Hướng Dẫn Test API

Sau khi ứng dụng chạy thành công tại `http://localhost:3000`, bạn có thể test các endpoints sau:

## 1. Test Enum Endpoints

```bash
# Lấy danh sách các enum types
curl http://localhost:3000/api/enums/types

# User Status
curl http://localhost:3000/api/enums/userStatus

# Product Status
curl http://localhost:3000/api/enums/productStatus

# Order Status
curl http://localhost:3000/api/enums/orderStatus

# Payment Status
curl http://localhost:3000/api/enums/paymentStatus

# Shipping Status
curl http://localhost:3000/api/enums/shippingStatus

# Post Status
curl http://localhost:3000/api/enums/postStatus

# Contact Status
curl http://localhost:3000/api/enums/contactStatus

# Role Status
curl http://localhost:3000/api/enums/roleStatus

# Gender
curl http://localhost:3000/api/enums/gender
```

## 2. Test Authentication

### Register
```bash
curl -X POST http://localhost:3000/api/register \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"user@example.com\",\"password\":\"password123\",\"username\":\"user1\"}"
```

### Login
```bash
curl -X POST http://localhost:3000/api/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"user@example.com\",\"password\":\"password123\"}"
```

Kết quả sẽ trả về:
```json
{
  "statusCode": 200,
  "message": "Success",
  "data": {
    "accessToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "tokenType": "Bearer",
    "user": {
      "id": "...",
      "email": "user@example.com",
      ...
    }
  }
}
```

### Get Current User
```bash
curl http://localhost:3000/api/me \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

### Logout
```bash
curl -X POST http://localhost:3000/api/logout \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

## 3. Sử dụng với Postman hoặc Browser

1. Mở Postman hoặc trình duyệt
2. URL: `http://localhost:3000/api/enums/types`
3. Method: GET
4. Send request

## 4. Kiểm tra ứng dụng đang chạy

Mở trình duyệt vào: `http://localhost:3000/api/enums/types`

Kết quả mong đợi:
```json
{
  "types": [
    "userStatus",
    "productStatus",
    "orderStatus",
    ...
  ]
}
```

## Lưu Ý

- Ứng dụng chạy trên port 3000 (có thể thay đổi trong file .env)
- Các endpoints khác (products, orders, etc.) cần implement thêm services và controllers
- Database SQLite được tạo tự động tại `database/nestjs-database.sqlite`
- JWT token sẽ expire sau 7 ngày (có thể thay đổi trong .env)

