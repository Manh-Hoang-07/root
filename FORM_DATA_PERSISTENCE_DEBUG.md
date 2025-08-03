# Hướng Dẫn Debug Form Data Bị Mất

## 🔍 **Vấn Đề Hiện Tại**
```
Login attempt started
Form data: Proxy(Object) {email: '', password: '', remember: false}
Validation failed
```

Dữ liệu form bị mất khi validation chạy, mặc dù user đã nhập đủ thông tin.

## 🛠️ **Các Bước Debug**

### **1. Kiểm Tra Console Logs**
Mở Developer Tools (F12) và kiểm tra Console để xem:
- Form data trước và sau validation
- Validation errors chi tiết
- FormField updateValue logs

### **2. Chạy Debug Tests**
Trong Console, chạy:
```javascript
// Test form data persistence
import { testFormDataPersistence, testValidationWithoutClearing } from '@/utils/debugLogin'

// Test với form hiện tại
testFormDataPersistence(form)
testValidationWithoutClearing(form, validationRules)
```

### **3. Kiểm Tra Form State**
Trong Console, kiểm tra:
```javascript
// Kiểm tra form state
console.log('Current form state:', { ...form })

// Kiểm tra từng field
console.log('Email:', form.email)
console.log('Password:', form.password)
console.log('Remember:', form.remember)

// Kiểm tra form type
console.log('Form type:', typeof form)
console.log('Is reactive:', form && form.__v_isRef)
```

## 🔧 **Các Lỗi Đã Sửa**

### **1. Sửa `validateForm.js`**
- ✅ Không trim password field
- ✅ Thêm debug logging chi tiết
- ✅ Xử lý khác nhau cho từng loại field

### **2. Sửa `Login.vue`**
- ✅ Không clear form data khi validation
- ✅ Thêm debug logging chi tiết
- ✅ Tách riêng login data object

### **3. Sửa `FormField.vue`**
- ✅ Thêm debug logging cho updateValue
- ✅ Xử lý an toàn cho event handling

## 🚀 **Các Bước Kiểm Tra**

### **Bước 1: Refresh Trang**
1. Refresh trang login
2. Mở Developer Tools (F12)
3. Kiểm tra Console có debug logs không

### **Bước 2: Nhập Dữ Liệu**
1. Nhập email: `test@example.com`
2. Nhập password: `password123`
3. Check "Remember me"
4. Kiểm tra Console có FormField logs không

### **Bước 3: Click Submit**
1. Click "Đăng nhập" button
2. Kiểm tra Console logs:
   - Form data before validation
   - Validation process
   - Form data after validation

### **Bước 4: Kiểm Tra Kết Quả**
1. Nếu validation failed: Kiểm tra lỗi cụ thể
2. Nếu validation passed: Kiểm tra login process

## 📋 **Checklist Debug**

- [ ] Form data được set đúng khi user nhập
- [ ] FormField updateValue logs hiển thị
- [ ] Form data không bị mất khi validation
- [ ] Validation errors hiển thị đúng
- [ ] Login process chạy với dữ liệu đúng

## 🔄 **Các Thay Đổi Đã Thực Hiện**

### **1. `validateForm.js`**
```javascript
// Trước
const value = fieldValue.toString().trim()

// Sau
let value
if (field === 'password') {
  value = fieldValue.toString() // Không trim password
} else {
  value = fieldValue.toString().trim()
}
console.log(`Validating field: ${field}, value: "${value}"`)
```

### **2. `Login.vue`**
```javascript
// Trước
clearAllErrors() // Có thể clear form data

// Sau
// KHÔNG clear form data, chỉ clear errors
clearAllErrors()

// Thêm debug logging
console.log('Form before validation:', { ...form })
console.log('Form after validation:', { ...form })
```

### **3. `FormField.vue`**
```javascript
// Thêm debug logging
console.log(`FormField updateValue: field="${props.name}", newValue="${newValue}"`)
```

## 🎯 **Kết Quả Mong Đợi**

Sau khi sửa, bạn sẽ thấy:
- ✅ Form data không bị mất khi validation
- ✅ Console logs hiển thị chi tiết quá trình
- ✅ Validation chạy đúng với dữ liệu user nhập
- ✅ Login process hoạt động bình thường

## 📞 **Nếu Vẫn Có Lỗi**

### **Kiểm tra thêm:**

1. **Vue DevTools**: 
   - Mở Vue DevTools
   - Kiểm tra component state
   - Xem reactive data có bị reset không

2. **Network Tab**:
   - Kiểm tra API calls
   - Xem request payload có đúng không

3. **Test Manual**:
   ```javascript
   // Trong Console
   form.email = 'test@example.com'
   form.password = 'password123'
   console.log('Manual set:', { ...form })
   ```

## 🔗 **Files Liên Quan**

- `resources/js/views/Login.vue` - Trang đăng nhập
- `resources/js/components/Core/FormField.vue` - Component form field
- `resources/js/utils/validateForm.js` - Validation utility
- `resources/js/utils/debugLogin.js` - Debug utilities

## 🚨 **Lưu Ý Quan Trọng**

- **Password không nên trim**: Có thể user nhập space ở đầu/cuối
- **Reactive data**: Đảm bảo không bị reset bởi validation
- **Event handling**: Kiểm tra FormField có emit đúng không
- **Debug logging**: Sử dụng để track quá trình xử lý

## 🧪 **Test Cases**

### **Test Case 1: Normal Input**
- Email: `test@example.com`
- Password: `password123`
- Remember: `true`
- Expected: Validation passed, login successful

### **Test Case 2: Empty Fields**
- Email: `""`
- Password: `""`
- Remember: `false`
- Expected: Validation failed, show errors

### **Test Case 3: Invalid Email**
- Email: `invalid-email`
- Password: `password123`
- Remember: `true`
- Expected: Validation failed, email error

### **Test Case 4: Short Password**
- Email: `test@example.com`
- Password: `123`
- Remember: `true`
- Expected: Validation failed, password too short 