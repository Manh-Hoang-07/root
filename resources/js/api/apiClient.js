import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  // Thêm interceptor nếu cần
})

export default api 