import { ref, computed } from 'vue'

// Global auth state
const isAuthenticated = ref(false)
const user = ref(null)
const userRole = ref('')

// Auth helper class giống Laravel
class Auth {
  /**
   * Kiểm tra user đã đăng nhập chưa
   */
  static check() {
    return isAuthenticated.value
  }

  /**
   * Lấy thông tin user hiện tại
   */
  static user() {
    return user.value
  }

  /**
   * Kiểm tra user chưa đăng nhập (guest)
   */
  static guest() {
    return !isAuthenticated.value
  }

  /**
   * Lấy role của user
   */
  static role() {
    return userRole.value
  }

  /**
   * Kiểm tra user có role admin không
   */
  static isAdmin() {
    return userRole.value === 'admin'
  }

  /**
   * Kiểm tra user có role user không
   */
  static isUser() {
    return userRole.value === 'user'
  }

  /**
   * Kiểm tra user có permission không
   */
  static can(permission) {
    if (!user.value || !user.value.permissions) return false
    return user.value.permissions.includes(permission)
  }

  /**
   * Kiểm tra user có role không
   */
  static hasRole(role) {
    if (!user.value || !user.value.roles) return false
    return user.value.roles.includes(role)
  }

  /**
   * Khởi tạo auth state từ localStorage
   */
  static async init() {
    const storedAuth = localStorage.getItem('isAuthenticated')
    const token = localStorage.getItem('auth_token')
    const storedUser = localStorage.getItem('user')
    const storedRole = localStorage.getItem('userRole')

    if (storedAuth === 'true' && token && storedUser) {
      try {
        // Thử lấy thông tin user từ API
        const response = await fetch('/api/me', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
          }
        })

        const data = await response.json()

        if (response.ok && data.success) {
          // Cập nhật state
          isAuthenticated.value = true
          user.value = data.data
          userRole.value = data.data.role
          
          // Cập nhật localStorage
          localStorage.setItem('user', JSON.stringify(data.data))
          localStorage.setItem('userRole', data.data.role)
          
          return true
        } else {
          // Token không hợp lệ, xóa state
          Auth.logout()
          return false
        }
      } catch (error) {
        console.error('Auth init error:', error)
        Auth.logout()
        return false
      }
    } else if (storedUser) {
      // Có user data nhưng không có token, xóa state
      Auth.logout()
      return false
    }

    return false
  }

  /**
   * Đăng nhập
   */
  static async login(credentials) {
    try {
      const response = await fetch('/api/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(credentials)
      })

      const data = await response.json()

      if (response.ok && data.success) {
        // Cập nhật state
        isAuthenticated.value = true
        
        // Lưu token
        if (data.data.token) {
          localStorage.setItem('auth_token', data.data.token)
        }
        localStorage.setItem('isAuthenticated', 'true')

        // Lấy thông tin user
        await Auth.fetchUserInfo()
        
        return { success: true, data: data.data }
      } else {
        return { 
          success: false, 
          message: data.message || 'Đăng nhập thất bại',
          errors: data.errors
        }
      }
    } catch (error) {
      console.error('Login error:', error)
      return { success: false, message: 'Lỗi kết nối' }
    }
  }

  /**
   * Đăng xuất
   */
  static async logout() {
    try {
      const token = localStorage.getItem('auth_token')
      if (token) {
        await fetch('/api/logout', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
          }
        })
      }
    } catch (error) {
      console.error('Logout API error:', error)
    } finally {
      // Xóa state
      isAuthenticated.value = false
      user.value = null
      userRole.value = ''
      
      // Xóa localStorage
      localStorage.removeItem('auth_token')
      localStorage.removeItem('isAuthenticated')
      localStorage.removeItem('user')
      localStorage.removeItem('userRole')
    }
  }

  /**
   * Lấy thông tin user từ API
   */
  static async fetchUserInfo() {
    try {
      const token = localStorage.getItem('auth_token')
      if (!token) return false

      const response = await fetch('/api/me', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        }
      })

      const data = await response.json()

      if (response.ok && data.success) {
        user.value = data.data
        userRole.value = data.data.role
        
        localStorage.setItem('user', JSON.stringify(data.data))
        localStorage.setItem('userRole', data.data.role)
        
        return true
      }
      return false
    } catch (error) {
      console.error('Fetch user info error:', error)
      return false
    }
  }

  /**
   * Đổi mật khẩu
   */
  static async changePassword(passwordData) {
    try {
      const token = localStorage.getItem('auth_token')
      const response = await fetch('/api/change-password', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(passwordData)
      })

      const data = await response.json()

      if (response.ok && data.success) {
        return { success: true, message: data.message }
      } else {
        return { success: false, message: data.message || 'Đổi mật khẩu thất bại' }
      }
    } catch (error) {
      console.error('Change password error:', error)
      return { success: false, message: 'Lỗi kết nối' }
    }
  }
}

// Reactive state cho Vue
const authState = {
  isAuthenticated: computed(() => isAuthenticated.value),
  user: computed(() => user.value),
  userRole: computed(() => userRole.value),
  isAdmin: computed(() => userRole.value === 'admin'),
  isUser: computed(() => userRole.value === 'user')
}

export { Auth, authState } 