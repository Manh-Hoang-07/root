# Hướng dẫn Debug Upload Ảnh

## Vấn đề hiện tại
Component ImageUploader báo "Upload thất bại" trong khi API trả về thành công.

## Nguyên nhân có thể

### 1. **Response Structure Mismatch**
- API trả về: `{ success: true, data: { url: "...", path: "..." } }`
- Component tìm: `res.data.url` (sai)
- Cần tìm: `res.data.data.url` (đúng)

### 2. **Authentication Issues**
- Route `/api/upload-image` không có middleware auth
- Nhưng có thể có middleware global ảnh hưởng

### 3. **File Upload Issues**
- File size quá lớn
- File type không hợp lệ
- Storage permissions

## Các bước Debug

### Bước 1: Test với trang debug
1. Truy cập: `/admin/categories/upload-test`
2. Chọn file ảnh
3. Click "Test Upload" để test endpoint
4. Click "Upload Ảnh" để test upload thật
5. Xem console và debug info

### Bước 2: Kiểm tra Console
Mở Developer Tools > Console và xem:
- API Request logs
- API Response logs
- Error messages

### Bước 3: Kiểm tra Network Tab
1. Mở Developer Tools > Network
2. Upload ảnh
3. Xem request/response details

### Bước 4: Test API trực tiếp
```bash
# Test với curl
curl -X POST http://localhost:8000/api/test-upload \
  -F "image=@test.jpg" \
  -H "Content-Type: multipart/form-data"

# Test upload thật
curl -X POST http://localhost:8000/api/upload-image \
  -F "image=@test.jpg" \
  -H "Content-Type: multipart/form-data"
```

## Các file đã sửa

### 1. ImageUploader.vue
- ✅ Sửa response structure parsing
- ✅ Thêm console logging
- ✅ Cải thiện error handling

### 2. Upload Test Page
- ✅ Tạo trang test riêng
- ✅ Debug info chi tiết
- ✅ Test cả 2 endpoints

### 3. API Routes
- ✅ Thêm test endpoint
- ✅ Kiểm tra middleware

## Cách sửa nhanh

### Nếu vấn đề là response structure:
```javascript
// Trong ImageUploader.vue
const responseData = res.data.data || res.data
const url = responseData.url || responseData.path || responseData.image
```

### Nếu vấn đề là authentication:
```php
// Trong routes/api.php
Route::post('/upload-image', [ImageController::class, 'upload'])->middleware('auto.auth');
```

### Nếu vấn đề là file size:
```php
// Trong ImageController.php
'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120' // Tăng lên 5MB
```

## Kiểm tra Storage

### 1. Storage Link
```bash
php artisan storage:link
```

### 2. Permissions
```bash
# Kiểm tra thư mục
ls -la storage/app/public/images/

# Tạo thư mục nếu chưa có
mkdir -p storage/app/public/images
chmod 755 storage/app/public/images
```

### 3. Test Storage
```php
// Trong tinker
Storage::disk('public')->put('test.txt', 'Hello World');
echo Storage::disk('public')->url('test.txt');
```

## Logs để kiểm tra

### 1. Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### 2. Browser Console
- Network requests
- JavaScript errors
- API responses

### 3. Server Logs
```bash
# Nếu dùng Apache
tail -f /var/log/apache2/error.log

# Nếu dùng Nginx
tail -f /var/log/nginx/error.log
```

## Common Issues & Solutions

### Issue 1: "Upload thất bại" nhưng API thành công
**Cause:** Response structure mismatch
**Solution:** Đã sửa trong ImageUploader.vue

### Issue 2: 413 Payload Too Large
**Cause:** File quá lớn
**Solution:** Tăng upload_max_filesize trong php.ini

### Issue 3: 401 Unauthorized
**Cause:** Authentication required
**Solution:** Thêm middleware hoặc gửi token

### Issue 4: 500 Internal Server Error
**Cause:** Storage permissions hoặc disk space
**Solution:** Kiểm tra storage permissions và disk space

## Test Cases

### Test Case 1: Small Image (< 1MB)
- Format: JPG, PNG, GIF
- Expected: Success
- Check: URL returned

### Test Case 2: Large Image (> 2MB)
- Format: JPG, PNG
- Expected: Validation error
- Check: Error message

### Test Case 3: Invalid Format
- Format: PDF, TXT
- Expected: Validation error
- Check: Error message

### Test Case 4: No File
- Expected: Validation error
- Check: Error message

## Next Steps

1. **Test với trang debug** để xác định vấn đề
2. **Kiểm tra console logs** để xem chi tiết
3. **Test API trực tiếp** để xác nhận
4. **Áp dụng fix** tương ứng với vấn đề
5. **Test lại** để đảm bảo hoạt động

## Contact

Nếu vẫn gặp vấn đề, hãy cung cấp:
- Console logs
- Network tab details
- Error messages
- File details (size, type) 