// Debug utility để kiểm tra cookie và token
export function debugCookies() {
  // Debug cookies functionality
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
  
  // Debug auth store functionality
} 