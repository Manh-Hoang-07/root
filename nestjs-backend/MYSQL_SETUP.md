# Hướng Dẫn Cấu Hình MySQL với XAMPP

## Bước 1: Tạo Database trong MySQL

Mở phpMyAdmin hoặc MySQL CLI và chạy:

```sql
CREATE DATABASE nestjs_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Bước 2: Cập Nhật File .env

Mở file `.env` trong thư mục nestjs-backend và đảm bảo có các dòng sau:

```env
# Application
PORT=3000
NODE_ENV=development

# Database - MySQL (XAMPP)
DB_TYPE=mysql
DB_HOST=localhost
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=nestjs_database
DB_SYNCHRONIZE=true
DB_LOGGING=false

# JWT
JWT_SECRET=your-secret-key-change-this-in-production
JWT_EXPIRATION=7d

# CORS
CORS_ORIGIN=*

# File Upload
UPLOAD_DEST=./storage/uploads
MAX_FILE_SIZE=10485760
```

**Lưu ý:**
- `DB_USERNAME`: Thường là `root` nếu dùng XAMPP mặc định
- `DB_PASSWORD`: Nếu XAMPP không có password thì để trống ``
- `DB_PASSWORD`: Nếu có password thì điền vào (ví dụ: `DB_PASSWORD=yourpassword`)
- `DB_DATABASE`: Tên database bạn vừa tạo (nestjs_database)

## Bước 3: Kiểm Tra MySQL Đang Chạy

Đảm bảo MySQL trong XAMPP đang chạy (nút Start phải màu xanh).

## Bước 4: Chạy Ứng Dụng

```bash
cd nestjs-backend
npm run start:dev
```

## Bước 5: Kiểm Tra

Ứng dụng sẽ tự động tạo các bảng trong database `nestjs_database`.

Kiểm tra trong phpMyAdmin:
- Vào phpMyAdmin: http://localhost/phpmyadmin
- Chọn database `nestjs_database`
- Xem các bảng đã được tạo tự động

## Troubleshooting

### Lỗi: Connection refused
- Kiểm tra MySQL trong XAMPP đã Start chưa
- Kiểm tra port 3306 có đang bị chiếm không

### Lỗi: Access denied
- Kiểm tra username và password trong file .env
- Nếu MySQL có password, cập nhật `DB_PASSWORD=your_password`

### Lỗi: Unknown database 'nestjs_database'
- Tạo database trước: `CREATE DATABASE nestjs_database;`

