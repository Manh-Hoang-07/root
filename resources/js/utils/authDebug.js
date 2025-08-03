// Utility để debug authentication
export function debugAuth() {
  console.log('=== AUTH DEBUG ===')
  
  // Kiểm tra cookies
  const cookies = document.cookie.split(';')
  console.log('All cookies:', cookies)
  
  // Kiểm tra auth_token cookie
  let authToken = null
  for (let cookie of cookies) {
    const [name, value] = cookie.trim().split('=')
    if (name === 'auth_token') {
      authToken = decodeURIComponent(value)
      break
    }
  }
  console.log('Auth token from cookie:', authToken ? 'EXISTS' : 'NOT FOUND')
  
  // Kiểm tra localStorage
  const localToken = localStorage.getItem('auth_token')
  console.log('Auth token from localStorage:', localToken ? 'EXISTS' : 'NOT FOUND')
  
  // Kiểm tra current domain
  console.log('Current domain:', window.location.hostname)
  console.log('Current protocol:', window.location.protocol)
  
  // Kiểm tra vớiCredentials setting
  console.log('withCredentials should be true for cross-origin requests')
  
  return {
    hasCookieToken: !!authToken,
    hasLocalToken: !!localToken,
    cookieToken: authToken,
    localToken: localToken
  }
}

// Function để test API call với authentication
export async function testAuthAPI() {
  try {
    console.log('Testing API call with authentication...')
    
    const response = await fetch('/api/me', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'include' // Quan trọng: gửi cookies
    })
    
    console.log('API Response status:', response.status)
    console.log('API Response headers:', response.headers)
    
    const data = await response.json()
    console.log('API Response data:', data)
    
    return { success: response.ok, data }
  } catch (error) {
    console.error('API Test error:', error)
    return { success: false, error: error.message }
  }
}

// Function để kiểm tra token trong header
export function checkAuthHeader() {
  const token = getTokenFromCookie()
  if (token) {
    console.log('Authorization header would be:', `Bearer ${token}`)
    return `Bearer ${token}`
  } else {
    console.log('No token found for Authorization header')
    return null
  }
}

// Helper function để lấy token từ cookie
function getTokenFromCookie() {
  const cookies = document.cookie.split(';')
  
  for (let cookie of cookies) {
    const [name, value] = cookie.trim().split('=')
    if (name === 'auth_token') {
      const token = decodeURIComponent(value)
      return token
    }
  }
  return null
} 