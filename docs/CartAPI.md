# Cart API Documentation

## Overview
Cart API hỗ trợ cả người dùng đã đăng nhập và người dùng chưa đăng nhập (guest). Hệ thống sử dụng nhiều phương pháp để xác định giỏ hàng của người dùng:

1. **Header X-Cart-ID**: Client có thể gửi cart ID trong header
2. **Cookie**: Cart ID được lưu trong cookie `cart_header_id`
3. **Session**: Cart ID được lưu trong session
4. **User ID**: Cho người dùng đã đăng nhập

## Flow cho người dùng chưa đăng nhập

### 1. Lần đầu tiên truy cập API
- Client gọi API `/api/cart` (GET hoặc POST)
- Server kiểm tra các nguồn cart ID theo thứ tự:
  1. Header `X-Cart-ID`
  2. Cookie `cart_header_id`
  3. Session `cart_header_id`
- Nếu không tìm thấy cart ID nào, server tạo một cart ID mới với định dạng `guest_[random_string]`
- Server lưu cart ID vào cookie `cart_header_id` (hết hạn sau 1 năm)
- Server trả về cart ID trong header `X-Cart-ID`

### 2. Các lần truy cập tiếp theo
- Client nên gửi lại cart ID trong header `X-Cart-ID` hoặc để browser tự động gửi cookie
- Server sẽ nhận diện được giỏ hàng của người dùng

## Flow cho người dùng đã đăng nhập

### 1. Lần đầu tiên sau khi đăng nhập
- Server ưu tiên sử dụng session ID thay vì user ID
- Cart ID có định dạng `session_[session_id]`
- Server lưu cả `user_id` và `session_id` vào CartHeader

### 2. Các lần truy cập tiếp theo
- Server sẽ tìm CartHeader dựa trên cart ID
- Nếu CartHeader đã tồn tại nhưng chưa có `user_id`, server sẽ cập nhật `user_id`
- Server luôn cập nhật `session_id` nếu khác với giá trị hiện tại

## API Endpoints

### GET /api/cart
Lấy giỏ hàng hiện tại
- Response header: `X-Cart-ID` chứa cart ID

### POST /api/cart
Thêm sản phẩm vào giỏ hàng
- Request body:
  ```json
  {
    "product_id": 1,
    "product_variant_id": 2,
    "quantity": 1
  }
  ```
- Response header: `X-Cart-ID` chứa cart ID

### PUT /api/cart/{id}
Cập nhật số lượng sản phẩm trong giỏ hàng
- Request body:
  ```json
  {
    "quantity": 2
  }
  ```

### DELETE /api/cart/{id}
Xóa sản phẩm khỏi giỏ hàng

### DELETE /api/cart
Xóa toàn bộ giỏ hàng

## Implementation Notes

### Session Middleware
Cart API sử dụng các middleware sau để đảm bảo session hoạt động:
- `StartSession`: Khởi tạo session
- `AddQueuedCookiesToResponse`: Đảm bảo cookie được gửi về client

### Cart ID Generation
- Guest: `guest_[random_string]`
- Session: `session_[session_id]`
- User: `user_[user_id]` (không còn sử dụng)

### Database Structure
- `cart_headers`: Lưu thông tin giỏ hàng (user_id, session_id, totals)
- `carts`: Lưu các sản phẩm trong giỏ hàng

## Client Implementation

### JavaScript/Frontend
```javascript
// Lấy cart ID từ storage hoặc tạo mới
let cartId = localStorage.getItem('cart_id') || null;

// Gọi API với cart ID
fetch('/api/cart', {
  headers: {
    'X-Cart-ID': cartId,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})
.then(response => {
  // Lưu cart ID từ response header
  cartId = response.headers.get('X-Cart-ID');
  if (cartId) {
    localStorage.setItem('cart_id', cartId);
  }
  return response.json();
})
.then(data => console.log(data));
```

### Mobile App
```swift
// Lưu cart ID trong UserDefaults
let cartId = UserDefaults.standard.string(forKey: "cart_id")

// Gọi API với cart ID
var request = URLRequest(url: URL(string: "/api/cart")!)
request.httpMethod = "GET"
request.setValue(cartId, forHTTPHeaderField: "X-Cart-ID")
request.setValue("application/json", forHTTPHeaderField: "Content-Type")
request.setValue("application/json", forHTTPHeaderField: "Accept")

// Xử lý response và lưu cart ID
if let cartId = response.value(forHTTPHeaderField: "X-Cart-ID") {
    UserDefaults.standard.set(cartId, forKey: "cart_id")
}