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
        
        // Token đã được lưu vào cookie bởi backend
        // Không cần lưu vào localStorage nữa
        
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
      // Gọi API logout (backend sẽ xóa cookie)
      await fetch('/api/logout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        }
      });
    } catch (error) {
      console.error('Logout API error:', error);
    } finally {
      // Xóa state
      isAuthenticated.value = false;
      user.value = null;
      userRole.value = '';
    }
  };

  const fetchUserInfo = async () => {
    try {
      // Không cần gửi token trong header, backend sẽ tự động lấy từ cookie
      const response = await fetch('/api/me', {
        headers: {
          'Content-Type': 'application/json',
        }
      });

      const data = await response.json();

      if (response.ok && data.success) {
        user.value = data.data;
        userRole.value = data.data.role;
        
        return true;
      }
      return false;
    } catch (error) {
      console.error('Fetch user info error:', error);
      return false;
    }
  };

  const checkAuth = async () => {
    // Kiểm tra token trong cookie
    const cookies = document.cookie.split(';');
    let hasToken = false;
    for (let cookie of cookies) {
      const [name] = cookie.trim().split('=');
      if (name === 'auth_token') {
        hasToken = true;
        break;
      }
    }

    if (hasToken) {
      // Thử lấy thông tin user từ API
      const success = await fetchUserInfo();
      if (success) {
        isAuthenticated.value = true;
        return true;
      } else {
        // Nếu không lấy được thông tin user, xóa state
        isAuthenticated.value = false;
        user.value = null;
        userRole.value = '';
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
  // checkAuth(); // Comment out để tránh gọi async function khi store được tạo

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