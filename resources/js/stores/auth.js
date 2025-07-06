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
        user.value = data.data.user;
        userRole.value = data.data.user.role;
        
        // Lưu vào localStorage
        localStorage.setItem('isAuthenticated', 'true');
        localStorage.setItem('userRole', data.data.user.role);
        localStorage.setItem('user', JSON.stringify(data.data.user));
        
        return { success: true, data: data.data };
      } else {
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

  const logout = () => {
    isAuthenticated.value = false;
    user.value = null;
    userRole.value = '';
    
    // Xóa khỏi localStorage
    localStorage.removeItem('isAuthenticated');
    localStorage.removeItem('userRole');
    localStorage.removeItem('user');
  };

  const checkAuth = () => {
    const storedAuth = localStorage.getItem('isAuthenticated');
    const storedRole = localStorage.getItem('userRole');
    const storedUser = localStorage.getItem('user');

    if (storedAuth === 'true' && storedRole && storedUser) {
      isAuthenticated.value = true;
      userRole.value = storedRole;
      user.value = JSON.parse(storedUser);
      return true;
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

  // Khởi tạo từ localStorage
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
    updateProfile,
    changePassword
  };
}); 