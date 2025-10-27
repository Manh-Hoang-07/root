# Cấu Hình Database NestJS

## ✅ Đã Cấu Hình

NestJS backend hiện đang sử dụng **CÙNG DATABASE** với Laravel:

- **Database**: `base` (database của Laravel)
- **Host**: `127.0.0.1` (localhost)
- **Port**: `3306`
- **Username**: `root`
- **Password**: (trống)
- **Synchronize**: `false` (không tự động thay đổi database)

## ⚠️ Lưu Ý Quan Trọng

Vì NestJS dùng **cùng database** với Laravel:

1. **Không thay đổi structure** - Entities trong NestJS phải khớp với bảng hiện có
2. **Không dùng synchronize: true** - Đã tắt để bảo vệ database
3. **Không chạy migrations mới** - Sử dụng migrations của Laravel

## 🎯 Cách Hoạt Động

1. Laravel quản lý database structure (migrations, models)
2. NestJS chỉ đọc/ghi data, không thay đổi structure
3. Cả 2 ứng dụng dùng chung bảng dữ liệu

## 📝 Nếu Cần Thêm Bảng Mới

1. Tạo migration trong Laravel
2. Chạy migration: `php artisan migrate`
3. Cập nhật Entity trong NestJS cho khớp

## 🚀 Chạy Ứng Dụng

```bash
cd nestjs-backend
npm run start:dev
```

App sẽ kết nối vào database `base` của Laravel và chạy tại `http://localhost:3000`

