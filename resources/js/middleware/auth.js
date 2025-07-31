import { useAuthStore } from '../stores/auth.js';

// Hàm helper để lấy token từ cookie
function getTokenFromCookie() {
  console.log('🍪 Debug: All cookies:', document.cookie);
  console.log('🌐 Debug: Current domain:', window.location.hostname);
  console.log('🌐 Debug: Current URL:', window.location.href);
  
  const cookies = document.cookie.split(';');
  console.log('🍪 Debug: Parsed cookies:', cookies);
  
  for (let cookie of cookies) {
    const [name, value] = cookie.trim().split('=');
    console.log(`🍪 Debug: Checking cookie: ${name} = ${value ? value.substring(0, 20) + '...' : 'undefined'}`);
    if (name === 'auth_token') {
      const token = decodeURIComponent(value);
      console.log('🔑 Debug: Found auth_token, length:', token.length);
      return token;
    }
  }
  console.log('❌ Debug: No auth_token found in cookies');
  return null;
}

export function requireAuth(to, from, next) {
  const authStore = useAuthStore();
  
  // Kiểm tra token trong cookie
  const token = getTokenFromCookie();
  
  console.log('🔍 requireAuth check:', { 
    token: !!token, 
    user: !!authStore.user,
    isFetchingUser: authStore.isFetchingUser 
  });
  
  if (!token) {
    console.log('❌ No token in cookie, redirect to login');
    next('/login');
    return;
  }
  
  // Có token, kiểm tra thông tin user
  if (!authStore.user && !authStore.isFetchingUser) {
    console.log('🔄 Fetching user info...');
    // Chưa có thông tin user và không đang fetch, thử fetch
    authStore.fetchUserInfo().then(success => {
      if (success) {
        console.log('✅ User info fetched successfully');
        next();
      } else {
        console.log('❌ Failed to fetch user info, logout and redirect');
        // Token không hợp lệ, redirect về login
        authStore.logout();
        next('/login');
      }
    });
  } else if (authStore.isFetchingUser) {
    console.log('⏳ User info is being fetched, waiting...');
    // Đang fetch, đợi một chút rồi kiểm tra lại
    setTimeout(() => {
      if (authStore.user) {
        console.log('✅ User info available after waiting');
        next();
      } else {
        console.log('❌ Still no user info after waiting, redirect to login');
        authStore.logout();
        next('/login');
      }
    }, 500);
  } else {
    console.log('✅ User info already available');
    // Đã có thông tin user
    next();
  }
}

export function requireAdmin(to, from, next) {
  const authStore = useAuthStore();
  
  // Kiểm tra token trong cookie
  const token = getTokenFromCookie();
  
  console.log('🔍 requireAdmin check:', { 
    token: !!token,
    storeUserRole: authStore.userRole,
    storeUser: !!authStore.user,
    isFetchingUser: authStore.isFetchingUser
  });
  
  if (!token) {
    console.log('❌ No token in cookie, redirect to login');
    next('/login');
    return;
  }
  
  // Có token, kiểm tra thông tin user
  if (!authStore.user && !authStore.isFetchingUser) {
    console.log('🔄 Fetching user info for admin check...');
    // Chưa có thông tin user và không đang fetch, thử fetch
    authStore.fetchUserInfo().then(success => {
      if (success) {
        console.log('✅ User info fetched, checking admin role...');
        console.log('🔍 After fetch:', { 
          userRole: authStore.userRole, 
          user: authStore.user 
        });
        // Kiểm tra role admin
        if (authStore.userRole !== 'admin') {
          console.log('❌ Not admin role, redirect to dashboard');
          next('/dashboard');
          return;
        }
        console.log('✅ Admin access granted');
        next();
      } else {
        console.log('❌ Failed to fetch user info, logout and redirect');
        authStore.logout();
        next('/login');
      }
    });
  } else if (authStore.isFetchingUser) {
    console.log('⏳ User info is being fetched for admin check, waiting...');
    // Đang fetch, đợi một chút rồi kiểm tra lại
    setTimeout(() => {
      if (authStore.user) {
        console.log('✅ User info available after waiting, checking admin role...');
        if (authStore.userRole !== 'admin') {
          console.log('❌ Not admin role, redirect to dashboard');
          next('/dashboard');
          return;
        }
        console.log('✅ Admin access granted after waiting');
        next();
      } else {
        console.log('❌ Still no user info after waiting, redirect to login');
        authStore.logout();
        next('/login');
      }
    }, 500);
  } else {
    console.log('✅ User info available, checking admin role...');
    console.log('🔍 Current user:', { 
      userRole: authStore.userRole, 
      user: authStore.user 
    });
    // Kiểm tra role admin
    if (authStore.userRole !== 'admin') {
      console.log('❌ Not admin role, redirect to dashboard');
      next('/dashboard');
      return;
    }
    console.log('✅ Admin access granted');
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