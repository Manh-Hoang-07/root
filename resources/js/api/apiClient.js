import axios from 'axios'

const api = axios.create({
  // baseURL: '/api', // Đã bỏ dòng này để tránh lặp /api/api
  withCredentials: true, // Gửi cookies với mọi request
  // Thêm interceptor nếu cần
})

// Helper function để lấy token từ cookie
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

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Tự động thêm token vào header nếu có trong cookie
    const token = getTokenFromCookie()
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    console.error('API Request Error:', error)
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    console.error('API Response Error:', {
      status: error.response?.status,
      data: error.response?.data,
      url: error.config?.url,
      message: error.message
    })
    
    // Xử lý lỗi authentication
    if (error.response?.status === 401 || error.response?.status === 403) {
      // Không cần xóa localStorage nữa, chỉ log
      console.warn('Authentication error detected:', error.response?.data)
    }
    
    return Promise.reject(error)
  }
)

export default api 