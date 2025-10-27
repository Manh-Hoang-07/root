# Hướng Dẫn Chạy Ứng Dụng NestJS

## ✅ Đã Hoàn Thành

1. ✅ Cài đặt dependencies
2. ✅ Tạo file `.env`
3. ✅ Tạo thư mục `database`
4. ✅ Compile thành công (có thư mục `dist`)

## 🚀 Cách Chạy

### Windows PowerShell
```powershell
cd nestjs-backend
npm run start:dev
```

### Hoặc chạy trực tiếp file JavaScript đã compile:
```powershell
cd nestjs-backend
node dist/main.js
```

## 🌐 Truy Cập

Sau khi chạy, ứng dụng sẽ có tại: `http://localhost:3000`

## 📝 Test API

### 1. Test Enum Endpoints
```bash
# Lấy danh sách các types
curl http://localhost:3000/api/enums/types

# Lấy User Status
curl http://localhost:3000/api/enums/userStatus

# Lấy Product Status
curl http://localhost:3000/api/enums/productStatus
```

### 2. Test Authentication

#### Register
```bash
curl -X POST http://localhost:3000/api/register \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"user@example.com\",\"password\":\"password123\",\"username\":\"user1\"}"
```

#### Login
```bash
curl -X POST http://localhost:3000/api/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"user@example.com\",\"password\":\"password123\"}"
```

### 3. Test với Auth Token
Sau khi login, bạn sẽ nhận được `accessToken`. Sử dụng token này để truy cập các endpoints protected:

```bash
curl http://localhost:3000/api/me \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

## ⚠️ Lưu Ý

- Các endpoints khác (products, orders, etc.) đang ở mức skeleton
- Cần implement đầy đủ services và controllers cho từng module
- Database sẽ được tạo tự động tại `database/nestjs-database.sqlite`

## 📚 Xem Thêm

- `CONVERSION_NOTES.md` - Chi tiết về conversion
- `README.md` - Tổng quan dự án
- `INSTALLATION.md` - Hướng dẫn cài đặt chi tiết

