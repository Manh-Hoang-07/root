import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useUserStore = defineStore('user', () => {
  // State
  const users = ref([]);
  const loading = ref(false);
  const error = ref(null);

  // Getters
  const getUserById = computed(() => {
    return (id) => users.value.find(user => user.id === id);
  });

  const totalUsers = computed(() => users.value.length);

  const activeUsers = computed(() => {
    return users.value.filter(user => user.status !== 'inactive');
  });

  // Actions
  const fetchUsers = async () => {
    loading.value = true;
    error.value = null;
    
    try {
      // Simulate API call - replace with actual Laravel API endpoint
      const response = await fetch('/api/users');
      if (!response.ok) throw new Error('Không thể tải dữ liệu người dùng');
      
      const data = await response.json();
      users.value = data;
    } catch (err) {
      error.value = err.message;
      // Fallback data for demo
      users.value = [
        { id: 1, name: 'Nguyễn Văn A', email: 'nguyenvana@example.com', created_at: '2024-01-15', status: 'active' },
        { id: 2, name: 'Trần Thị B', email: 'tranthib@example.com', created_at: '2024-01-16', status: 'active' },
        { id: 3, name: 'Lê Văn C', email: 'levanc@example.com', created_at: '2024-01-17', status: 'inactive' }
      ];
    } finally {
      loading.value = false;
    }
  };

  const createUser = async (userData) => {
    try {
      // Simulate API call
      const newUser = {
        id: users.value.length + 1,
        ...userData,
        created_at: new Date().toISOString().split('T')[0],
        status: 'active'
      };
      
      users.value.push(newUser);
      return newUser;
    } catch (err) {
      error.value = 'Không thể tạo người dùng';
      throw err;
    }
  };

  const updateUser = async (id, userData) => {
    try {
      const index = users.value.findIndex(u => u.id === id);
      if (index !== -1) {
        users.value[index] = { ...users.value[index], ...userData };
        return users.value[index];
      }
      throw new Error('Không tìm thấy người dùng');
    } catch (err) {
      error.value = 'Không thể cập nhật người dùng';
      throw err;
    }
  };

  const deleteUser = async (id) => {
    try {
      const index = users.value.findIndex(u => u.id === id);
      if (index !== -1) {
        users.value.splice(index, 1);
        return true;
      }
      throw new Error('Không tìm thấy người dùng');
    } catch (err) {
      error.value = 'Không thể xóa người dùng';
      throw err;
    }
  };

  const clearError = () => {
    error.value = null;
  };

  return {
    // State
    users,
    loading,
    error,
    
    // Getters
    getUserById,
    totalUsers,
    activeUsers,
    
    // Actions
    fetchUsers,
    createUser,
    updateUser,
    deleteUser,
    clearError
  };
}); 