# Hướng Dẫn Debug Authentication

## 🔍 **Vấn Đề Hiện Tại**
Bạn đang gặp lỗi `Unauthenticated. Vui lòng đăng nhập.` khi truy cập các trang admin. Điều này có nghĩa là token authentication không được gửi đúng cách.

## 🛠️ **Các Bước Debug**

### **1. Kiểm Tra Token**
Mở Developer Tools (F12) và chạy trong Console:
```javascript
// Kiểm tra cookies
console.log('Cookies:', document.cookie)

// Kiểm tra localStorage
console.log('localStorage auth_token:', localStorage.getItem('auth_token'))

// Chạy debug function
import { debugAuth } from '@/utils/authDebug'
debugAuth()
```

### **2. Sử Dụng Component Debug**
- Component `AuthDebug` đã được thêm vào App.vue
- Nó sẽ hiển thị ở góc dưới bên phải màn hình
- Click các button để debug:
  - **Debug Auth**: Kiểm tra token trong cookie và localStorage
  - **Test API**: Test API call với authentication
  - **Check Auth Store**: Kiểm tra trạng thái auth store

### **3. Kiểm Tra Network Tab**
- Mở Developer Tools → Network tab
- Truy cập trang admin
- Kiểm tra request headers có `Authorization: Bearer <token>` không
- Kiểm tra response status có 401/403 không

## 🔧 **Các Lỗi Thường Gặp**

### **Lỗi 1: Token không được gửi**
**Nguyên nhân**: `apiClient` không lấy được token từ cookie
**Giải pháp**: Đã sửa trong `apiClient.js` để lấy token từ cookie

### **Lỗi 2: Cookie không được set**
**Nguyên nhân**: Backend không set cookie đúng cách
**Kiểm tra**: 
```javascript
// Trong Console
document.cookie.split(';').forEach(cookie => {
  const [name, value] = cookie.trim().split('=')
  if (name === 'auth_token') {
    console.log('Found auth_token:', value)
  }
})
```

### **Lỗi 3: CORS Issues**
**Nguyên nhân**: Cookie không được gửi do CORS
**Giải pháp**: Đảm bảo `withCredentials: true` trong axios config

### **Lỗi 4: Domain/Path Issues**
**Nguyên nhân**: Cookie được set cho domain/path khác
**Kiểm tra**: Cookie domain và path phải match với current domain

## 🚀 **Các Bước Sửa Lỗi**

### **Bước 1: Đăng nhập lại**
```javascript
// Clear tất cả auth data
localStorage.clear()
document.cookie.split(";").forEach(function(c) { 
  document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
});

// Đăng nhập lại
```

### **Bước 2: Kiểm tra Backend**
Đảm bảo backend:
- Set cookie với `httpOnly: false` (để JS có thể đọc)
- Set `secure: false` (cho development)
- Set `sameSite: 'lax'` hoặc `'none'`

### **Bước 3: Test API trực tiếp**
```javascript
// Test API call trực tiếp
fetch('/api/me', {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json',
  },
  credentials: 'include'
}).then(res => res.json()).then(console.log)
```

## 📋 **Checklist Debug**

- [ ] Token có trong cookie không?
- [ ] Token có trong localStorage không?
- [ ] Auth store có authenticated không?
- [ ] API call có gửi Authorization header không?
- [ ] Backend có nhận được token không?
- [ ] Response có 401/403 không?

## 🔄 **Các Thay Đổi Đã Thực Hiện**

### **1. Sửa `useDataTable.js`**
- Thay `axios` bằng `apiClient` để sử dụng authentication

### **2. Sửa `apiClient.js`**
- Thêm function `getTokenFromCookie()`
- Sử dụng token từ cookie thay vì localStorage

### **3. Tạo Debug Tools**
- `authDebug.js`: Utility functions để debug
- `AuthDebug.vue`: Component debug UI
- Thêm vào App.vue (chỉ development mode)

## 🎯 **Kết Quả Mong Đợi**

Sau khi sửa, bạn sẽ thấy:
- ✅ Token được lấy từ cookie
- ✅ Authorization header được gửi đúng
- ✅ API calls thành công
- ✅ Không còn lỗi 401/403

## 📞 **Nếu Vẫn Có Lỗi**

1. **Kiểm tra Console**: Xem có lỗi JavaScript nào không
2. **Kiểm tra Network**: Xem request/response details
3. **Kiểm tra Backend**: Xem log backend có lỗi gì không
4. **Test với Postman**: Test API trực tiếp với token

## 🔗 **Files Liên Quan**

- `resources/js/api/apiClient.js` - API client với authentication
- `resources/js/composables/useDataTable.js` - Data table composable
- `resources/js/stores/auth.js` - Auth store
- `resources/js/utils/authDebug.js` - Debug utilities
- `resources/js/components/Core/AuthDebug.vue` - Debug component
- `resources/js/App.vue` - Main app với debug component 