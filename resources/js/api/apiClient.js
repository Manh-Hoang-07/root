import axios from 'axios'

const api = axios.create({
  // baseURL: '/api', // Đã bỏ dòng này để tránh lặp /api/api
  withCredentials: true, // Gửi cookies với mọi request
  // Thêm interceptor nếu cần
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Không cần thêm token vào header, backend sẽ tự động lấy từ cookie
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
    }
    
    return Promise.reject(error)
  }
)

export default api 