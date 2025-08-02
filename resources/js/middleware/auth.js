import { useAuthStore } from '../stores/auth.js';

// Hàm helper để lấy token từ cookie
function getTokenFromCookie() {
  const cookies = document.cookie.split(';');
  
  for (let cookie of cookies) {
    const [name, value] = cookie.trim().split('=');
    if (name === 'auth_token') {
      const token = decodeURIComponent(value);
      return token;
    }
  }
  return null;
}

export function requireAuth(to, from, next) {
  const authStore = useAuthStore();
  
  // Kiểm tra token trong cookie
  const token = getTokenFromCookie();
  
  if (!token) {
    next('/login');
    return;
  }
  
  // Có token, kiểm tra thông tin user
  if (!authStore.user && !authStore.isFetchingUser) {
    // Chưa có thông tin user và không đang fetch, thử fetch
    authStore.fetchUserInfo().then(success => {
      if (success) {
        next();
      } else {
        // Token không hợp lệ, chỉ redirect về login, không gọi logout
        next('/login');
      }
    });
  } else if (authStore.isFetchingUser) {
    // Đang fetch, đợi một chút rồi kiểm tra lại
    setTimeout(() => {
      if (authStore.user) {
        next();
      } else {
        // Chỉ redirect, không gọi logout
        next('/login');
      }
    }, 500);
  } else {
    // Đã có thông tin user (có thể từ cache)
    next();
  }
}

export function requireAdmin(to, from, next) {
  const authStore = useAuthStore();
  
  // Kiểm tra token trong cookie
  const token = getTokenFromCookie();
  
  if (!token) {
    next('/login');
    return;
  }
  
  // Có token, kiểm tra thông tin user
  if (!authStore.user && !authStore.isFetchingUser) {
    // Chưa có thông tin user và không đang fetch, thử fetch
    authStore.fetchUserInfo().then(success => {
      if (success) {
        // Kiểm tra role admin
        if (authStore.userRole !== 'admin') {
          next('/dashboard');
          return;
        }
        next();
      } else {
        // Chỉ redirect, không gọi logout
        next('/login');
      }
    });
  } else if (authStore.isFetchingUser) {
    // Đang fetch, đợi một chút rồi kiểm tra lại
    setTimeout(() => {
      if (authStore.user) {
        // Kiểm tra role admin
        if (authStore.userRole !== 'admin') {
          next('/dashboard');
          return;
        }
        next();
      } else {
        // Chỉ redirect, không gọi logout
        next('/login');
      }
    }, 500);
  } else {
    // Đã có thông tin user (có thể từ cache), kiểm tra role admin
    if (authStore.userRole !== 'admin') {
      next('/dashboard');
      return;
    }
    next();
  }
}

export function requireGuest(to, from, next) {
  const token = getTokenFromCookie();
  
  if (token) {
    // Đã đăng nhập, redirect về dashboard
    next('/dashboard');
  } else {
    // Chưa đăng nhập, cho phép vào trang login/register
    next();
  }
} 