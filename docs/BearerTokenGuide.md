# Hướng dẫn sử dụng Bearer Token trong API

## Tính năng xác thực toàn cục

Hệ thống đã được cấu hình với middleware xác thực toàn cục, cho phép:

1. **Tất cả API đều có thể nhận diện user nếu có token**
2. **Không cần token vẫn hoạt động bình thường** (cho guest users)
3. **Tự động xử lý token** từ header, query parameter hoặc cookie

## Cách hoạt động

- Nếu request có bearer token hợp lệ, user sẽ được xác thực tự động
- Nếu không có token, API vẫn hoạt động bình thường với quyền guest
- Tất cả controller đều có thể sử dụng `Auth::user()` để lấy thông tin user

## Cách sử dụng Bearer Token

### 1. Đăng nhập để lấy token

Gửi request POST đến endpoint `/api/login`:

```json
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

Response sẽ chứa token:

```json
{
    "success": true,
    "message": "Đăng nhập thành công.",
    "data": {
        "token": "1|abc123def456ghi789..."
    }
}
```

### 2. Sử dụng Bearer Token

Có 3 cách để gửi token cùng với request:

#### Cách 1: Authorization Header (Khuyến khích)

```http
GET /api/cart
Authorization: Bearer 1|abc123def456ghi789...
Content-Type: application/json
```

#### Cách 2: Query Parameter

```http
GET /api/cart?token=1|abc123def456ghi789...
```

#### Cách 3: Cookie

Token sẽ tự động được lưu trong cookie `auth_token` khi đăng nhập.

### 3. Ví dụ sử dụng với curl

```bash
# Đăng nhập
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Thêm sản phẩm vào giỏ hàng với token
curl -X POST http://localhost:8000/api/cart \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|abc123def456ghi789..." \
  -d '{"product_id": 1, "quantity": 2}'
```

### 4. Ví dụ sử dụng với JavaScript

```javascript
// Đăng nhập
const loginResponse = await fetch('/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password'
  })
});

const loginData = await loginResponse.json();
const token = loginData.data.token;

// Thêm sản phẩm vào giỏ hàng với token
const cartResponse = await fetch('/api/cart', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    product_id: 1,
    quantity: 2
  })
});

const cartData = await cartResponse.json();
```

### 5. Ví dụ sử dụng với Postman

1. Đăng nhập để lấy token
2. Trong các request tiếp theo, vào tab Authorization:
   - Type: Bearer Token
   - Token: Dán token nhận được từ API login

### 6. Refresh Token

Khi token sắp hết hạn, bạn có thể làm mới token:

```bash
curl -X POST http://localhost:8000/api/user/refresh-token \
  -H "Authorization: Bearer 1|abc123def456ghi789..."
```

### 7. Đăng xuất

Để vô hiệu hóa token:

```bash
curl -X POST http://localhost:8000/api/user/logout \
  -H "Authorization: Bearer 1|abc123def456ghi789..."
```

## Lợi ích của xác thực toàn cục

1. **API công cộng vẫn hoạt động với guest**: Người dùng chưa đăng nhập vẫn có thể sử dụng các API như xem sản phẩm, thêm vào giỏ hàng
2. **API công cộng hoạt động tốt hơn với user đã đăng nhập**: Khi có token, giỏ hàng sẽ được lưu theo user ID, lịch sử mua hàng được lưu trữ
3. **Không cần quản lý middleware phức tạp**: Tất cả API đều có thể truy cập thông tin user nếu cần

## Lưu ý quan trọng

1. **Thời gian hết hạn**: Token có hiệu lực trong 60 phút (1 giờ)
2. **Lưu trữ token**: Hãy lưu token an toàn ở client-side
3. **HTTPS**: Luôn sử dụng HTTPS trong production để bảo vệ token
4. **Token scope**: Token được tạo với đầy đủ quyền (`['*']`)

## Các endpoint cần xác thực bắt buộc

### User API
- `GET /api/user/me` - Lấy thông tin user
- `POST /api/user/logout` - Đăng xuất
- `POST /api/user/refresh-token` - Làm mới token
- `POST /api/user/change-password` - Đổi mật khẩu

### Admin API
Tất cả các endpoint dưới `/api/admin/*` đều cần xác thực bắt buộc

## Các endpoint hỗ trợ xác thực tùy chọn

### Public API (hoạt động với cả guest và authenticated user)
- `GET /api/cart` - Xem giỏ hàng (guest: session, user: user_id)
- `POST /api/cart` - Thêm sản phẩm vào giỏ hàng
- `PUT /api/cart/{id}` - Cập nhật giỏ hàng
- `DELETE /api/cart/{id}` - Xóa sản phẩm khỏi giỏ hàng
- `DELETE /api/cart` - Xóa toàn bộ giỏ hàng
- `POST /api/orders` - Tạo đơn hàng
- `GET /api/orders/{id}` - Xem đơn hàng

## Troubleshooting

### Lỗi "Unauthenticated"
1. Kiểm tra token có đúng không
2. Kiểm tra token đã hết hạn chưa
3. Đảm bảo gửi token với đúng format: `Bearer <token>`

### Token không hoạt động
1. Kiểm tra cấu hình CORS nếu gọi từ frontend khác domain
2. Đảm bảo server đã được restart sau khi thay đổi config
3. Kiểm tra database có bảng `personal_access_tokens` không