import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useAuthStore = defineStore('auth', () => {
  // State
  const isAuthenticated = ref(false);
  const user = ref(null);
  const userRole = ref('');

  // Getters
  const isAdmin = computed(() => userRole.value === 'admin');
  const isUser = computed(() => userRole.value === 'user');

  // Actions
  const login = async (credentials) => {
    try {
      const response = await fetch('/api/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(credentials)
      });

      const data = await response.json();

      if (response.ok && data.success) {
        isAuthenticated.value = true;
        
        // Lưu token nếu có
        if (data.data.token) {
          localStorage.setItem('auth_token', data.data.token);
        }
        
        // Lưu trạng thái đăng nhập
        localStorage.setItem('isAuthenticated', 'true');
        
        // Lấy thông tin user từ API /me
        await fetchUserInfo();
        
        return { success: true, data: data.data };
      } else {
        // Xử lý lỗi validation từ API
        if (response.status === 422 && data.errors) {
          return { 
            success: false, 
            message: data.message || 'Dữ liệu không hợp lệ',
            errors: data.errors 
          };
        }
        return { success: false, message: data.message || 'Đăng nhập thất bại' };
      }
    } catch (error) {
      console.error('Login error:', error);
      return { success: false, message: 'Lỗi kết nối' };
    }
  };

  const register = async (userData) => {
    try {
      const response = await fetch('/api/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
      });

      const data = await response.json();

      if (response.ok && data.success) {
        return { success: true, data: data.data };
      } else {
        return { success: false, message: data.message || 'Đăng ký thất bại' };
      }
    } catch (error) {
      console.error('Register error:', error);
      return { success: false, message: 'Lỗi kết nối' };
    }
  };

  const logout = async () => {
    try {
      // Gọi API logout nếu có token
      const token = localStorage.getItem('auth_token');
      if (token) {
        await fetch('/api/logout', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
          }
        });
      }
    } catch (error) {
      console.error('Logout API error:', error);
    } finally {
      // Xóa state và localStorage
      isAuthenticated.value = false;
      user.value = null;
      userRole.value = '';
      
      localStorage.removeItem('isAuthenticated');
      localStorage.removeItem('userRole');
      localStorage.removeItem('user');
      localStorage.removeItem('auth_token');
    }
  };

  const fetchUserInfo = async () => {
    try {
      const token = localStorage.getItem('auth_token');
      if (!token) return false;

      const response = await fetch('/api/me', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        }
      });

      const data = await response.json();

      if (response.ok && data.success) {
        user.value = data.data;
        userRole.value = data.data.role;
        
        // Lưu vào localStorage
        localStorage.setItem('userRole', data.data.role);
        localStorage.setItem('user', JSON.stringify(data.data));
        
        return true;
      }
      return false;
    } catch (error) {
      console.error('Fetch user info error:', error);
      return false;
    }
  };

  const checkAuth = async () => {
    const storedAuth = localStorage.getItem('isAuthenticated');
    const token = localStorage.getItem('auth_token');

    if (storedAuth === 'true' && token) {
      // Thử lấy thông tin user từ API
      const success = await fetchUserInfo();
      if (success) {
        isAuthenticated.value = true;
        return true;
      } else {
        // Nếu không lấy được thông tin user, chỉ xóa localStorage, không gọi logout API
        isAuthenticated.value = false;
        user.value = null;
        userRole.value = '';
        
        localStorage.removeItem('isAuthenticated');
        localStorage.removeItem('userRole');
        localStorage.removeItem('user');
        localStorage.removeItem('auth_token');
        
        return false;
      }
    }
    return false;
  };

  const updateProfile = async (profileData) => {
    try {
      const response = await fetch('/api/profile', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(profileData)
      });

      const data = await response.json();

      if (response.ok && data.success) {
        user.value = data.data.user;
        localStorage.setItem('user', JSON.stringify(data.data.user));
        return { success: true, data: data.data };
      } else {
        return { success: false, message: data.message || 'Cập nhật thất bại' };
      }
    } catch (error) {
      console.error('Update profile error:', error);
      return { success: false, message: 'Lỗi kết nối' };
    }
  };

  const changePassword = async (passwordData) => {
    try {
      const response = await fetch('/api/change-password', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(passwordData)
      });

      const data = await response.json();

      if (response.ok && data.success) {
        return { success: true, message: data.message };
      } else {
        return { success: false, message: data.message || 'Đổi mật khẩu thất bại' };
      }
    } catch (error) {
      console.error('Change password error:', error);
      return { success: false, message: 'Lỗi kết nối' };
    }
  };

  // Khởi tạo từ localStorage (async)
  checkAuth();

  return {
    // State
    isAuthenticated,
    user,
    userRole,
    
    // Getters
    isAdmin,
    isUser,
    
    // Actions
    login,
    register,
    logout,
    checkAuth,
    fetchUserInfo,
    updateProfile,
    changePassword
  };
}); 