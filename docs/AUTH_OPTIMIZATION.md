# Authentication Optimization

## Vấn đề

API `/api/me` được gọi liên tục mỗi khi load lại trang hoặc navigate, gây ra:
- Tăng tải cho server
- Chậm trễ trong UI
- Trải nghiệm người dùng kém

## Nguyên nhân

1. **App.vue** gọi `useAuthInit()` trong `onMounted()`
2. **useAuthInit** gọi `authStore.checkAuth()` mỗi khi component mount
3. **Router guards** gọi `fetchUserInfo()` khi cần kiểm tra auth
4. **checkAuth()** gọi `fetchUserInfo()` để lấy thông tin user

## Giải pháp

### 1. Cache trong Auth Store

```javascript
// Thêm cache mechanism
const lastFetchTime = ref(0);
const fetchCacheDuration = 30000; // 30 giây

const fetchUserInfo = async (force = false) => {
  // Kiểm tra cache
  const now = Date.now();
  if (!force && user.value && (now - lastFetchTime.value) < fetchCacheDuration) {
    console.log('Using cached user info');
    return !!user.value;
  }
  
  // Fetch từ API nếu cần
  // ...
};
```

### 2. Global Init Flag

```javascript
// useAuthInit.js
export function useAuthInit() {
  onMounted(async () => {
    // Chỉ init một lần
    if (!window.__authInitialized && !isInitializing.value) {
      window.__authInitialized = true;
      await authStore.checkAuth();
    }
  });
}
```

### 3. Optimized Middleware

```javascript
// middleware/auth.js
export function requireAuth(to, from, next) {
  const authStore = useAuthStore();
  
  if (!authStore.user && !authStore.isFetchingUser) {
    // Fetch user info (sử dụng cache)
    authStore.fetchUserInfo().then(success => {
      if (success) next();
      else next('/login');
    });
  } else {
    // Sử dụng cached data
    next();
  }
}
```

## Cách hoạt động

### Trước khi optimize:
```
Page Load → App.vue onMounted → checkAuth() → fetchUserInfo() → API /api/me
Route Change → Router Guard → fetchUserInfo() → API /api/me
Route Change → Router Guard → fetchUserInfo() → API /api/me
```

### Sau khi optimize:
```
Page Load → App.vue onMounted → checkAuth() → fetchUserInfo() → API /api/me (lần đầu)
Route Change → Router Guard → fetchUserInfo() → Use cached data ✅
Route Change → Router Guard → fetchUserInfo() → Use cached data ✅
```

## Cache Strategy

### Cache Duration: 30 giây
- Đủ ngắn để đảm bảo data fresh
- Đủ dài để tránh gọi API trùng lặp

### Cache Invalidation:
- Khi logout: Reset cache time
- Force refresh: Bỏ qua cache
- Token expired: Tự động clear cache

### Cache Scope:
- Per-session cache (không persist)
- Reset khi reload page
- Reset khi logout

## Benefits

1. **Giảm API calls**: Từ N calls xuống 1 call mỗi 30s
2. **Tăng performance**: UI responsive hơn
3. **Giảm server load**: Ít request hơn
4. **Better UX**: Không bị delay khi navigate

## Monitoring

### Console Logs:
```javascript
// Khi sử dụng cache
console.log('Using cached user info');

// Khi fetch từ API
console.log('Fetching user info...');
console.log('User info fetched successfully');
```

### Network Tab:
- Chỉ thấy 1 request `/api/me` mỗi 30s
- Không có duplicate requests

## Troubleshooting

### Cache không hoạt động:
1. Kiểm tra `fetchCacheDuration`
2. Kiểm tra `lastFetchTime` value
3. Kiểm tra `force` parameter

### API vẫn được gọi nhiều:
1. Kiểm tra global init flag
2. Kiểm tra router guards
3. Kiểm tra component lifecycle

### Data không fresh:
1. Giảm `fetchCacheDuration`
2. Sử dụng `refreshUserInfo()` để force refresh
3. Kiểm tra token expiration 