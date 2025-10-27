# Hướng Dẫn Chạy Ứng Dụng NestJS

## Các Bước Thực Hiện

### 1. Cài Đặt Dependencies
```bash
cd nestjs-backend
npm install
```

### 2. Tạo File Environment
Tạo file `.env` trong thư mục nestjs-backend với nội dung sau:

```env
PORT=3000
NODE_ENV=development

DB_TYPE=better-sqlite3
DB_DATABASE=database/nestjs-database.sqlite
DB_SYNCHRONIZE=true
DB_LOGGING=false

JWT_SECRET=your-secret-key-change-this-in-production
JWT_EXPIRATION=7d

CORS_ORIGIN=*

UPLOAD_DEST=./storage/uploads
MAX_FILE_SIZE=10485760
```

### 3. Tạo Thư Mục Database
```bash
mkdir database
```

### 4. Chạy Ứng Dụng
```bash
npm run start:dev
```

Ứng dụng sẽ chạy tại: `http://localhost:3000`

### 5. Kiểm Tra API

#### Test API Enum
```bash
curl http://localhost:3000/api/enums/types
curl http://localhost:3000/api/enums/userStatus
```

#### Test Login
```bash
curl -X POST http://localhost:3000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
```

#### Test Register
```bash
curl -X POST http://localhost:3000/api/register \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123","username":"user1"}'
```

## Lưu Ý

- Database SQLite sẽ được tạo tự động tại `database/nestjs-database.sqlite`
- Nếu gặp lỗi TypeScript, cần hoàn thiện các entities và services còn thiếu
- Các routes đang ở mức skeleton, cần implement đầy đủ business logic

## Tiếp Theo

Để hoàn thiện ứng dụng, cần:

1. Implement đầy đủ các services cho từng module
2. Tạo controllers xử lý CRUD operations
3. Thêm validation với DTOs
4. Implement authentication guards
5. Thêm file upload functionality

Xem `CONVERSION_NOTES.md` để biết chi tiết.

