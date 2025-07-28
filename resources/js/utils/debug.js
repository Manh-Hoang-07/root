// Debug utility để kiểm tra cookie và token
export function debugCookies() {
  console.log('🍪 All cookies:', document.cookie);
  
  const cookies = document.cookie.split(';');
  console.log('🍪 Parsed cookies:', cookies);
  
  for (let cookie of cookies) {
    const [name, value] = cookie.trim().split('=');
    console.log(`🍪 Cookie: ${name} = ${value ? value.substring(0, 20) + '...' : 'undefined'}`);
  }
  
  // Kiểm tra auth_token cụ thể
  const authToken = getAuthToken();
  console.log('🔑 Auth token found:', !!authToken);
  if (authToken) {
    console.log('🔑 Auth token length:', authToken.length);
    console.log('🔑 Auth token preview:', authToken.substring(0, 20) + '...');
  }
}

export function getAuthToken() {
  const cookies = document.cookie.split(';');
  for (let cookie of cookies) {
    const [name, value] = cookie.trim().split('=');
    if (name === 'auth_token') {
      return decodeURIComponent(value);
    }
  }
  return null;
}

export async function debugAuthStore() {
  const { useAuthStore } = await import('../stores/auth.js');
  const authStore = useAuthStore();
  
  console.log('🔐 Auth Store State:', {
    isAuthenticated: authStore.isAuthenticated,
    user: authStore.user,
    userRole: authStore.userRole,
    isAdmin: authStore.isAdmin,
    isUser: authStore.isUser
  });
} 