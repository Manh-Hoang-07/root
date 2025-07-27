import axios from 'axios'

const api = axios.create({
  // baseURL: '/api', // Đã bỏ dòng này để tránh lặp /api/api
  // Thêm interceptor nếu cần
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Thêm token vào header nếu có
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    
    console.log('API Request:', {
      method: config.method,
      url: config.url,
      data: config.data,
      headers: config.headers
    })
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
    console.log('API Response:', {
      status: response.status,
      data: response.data,
      url: response.config.url
    })
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
      // Chỉ xóa token, không redirect
      localStorage.removeItem('auth_token')
      localStorage.removeItem('isAuthenticated')
      localStorage.removeItem('user')
      localStorage.removeItem('userRole')
      
      // Không redirect tự động, để user tự xử lý
      console.log('Authentication failed, token cleared')
    }
    
    return Promise.reject(error)
  }
)

export default api 