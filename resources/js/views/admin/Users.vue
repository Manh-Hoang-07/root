<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold text-gray-800">Quản lý người dùng</h1>
      <button 
        @click="showCreateForm = true"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
      >
        Thêm người dùng
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
      <p class="mt-2 text-gray-600">Đang tải dữ liệu...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      <p>{{ error }}</p>
    </div>

    <!-- Users Table -->
    <div v-else class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              ID
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tên
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Email
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Ngày tạo
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Thao tác
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ user.id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ user.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ user.email }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
              <button 
                @click="editUser(user)"
                class="text-blue-600 hover:text-blue-900"
              >
                Sửa
              </button>
              <button 
                @click="deleteUser(user.id)"
                class="text-red-600 hover:text-red-900"
              >
                Xóa
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit User Modal -->
    <div v-if="showCreateForm || editingUser" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ editingUser ? 'Sửa người dùng' : 'Thêm người dùng mới' }}
          </h3>
          <form @submit.prevent="editingUser ? updateUser() : createUser()" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Tên</label>
              <input 
                v-model="userForm.name"
                type="text" 
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input 
                v-model="userForm.email"
                type="email" 
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div v-if="!editingUser">
              <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
              <input 
                v-model="userForm.password"
                type="password" 
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div class="flex justify-end space-x-3">
              <button 
                type="button"
                @click="closeForm"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
              >
                Hủy
              </button>
              <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
              >
                {{ editingUser ? 'Cập nhật' : 'Tạo' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// State
const users = ref([]);
const loading = ref(false);
const error = ref(null);
const showCreateForm = ref(false);
const editingUser = ref(null);
const userForm = ref({
  name: '',
  email: '',
  password: ''
});

// Methods
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
      { id: 1, name: 'Nguyễn Văn A', email: 'nguyenvana@example.com', created_at: '2024-01-15' },
      { id: 2, name: 'Trần Thị B', email: 'tranthib@example.com', created_at: '2024-01-16' },
      { id: 3, name: 'Lê Văn C', email: 'levanc@example.com', created_at: '2024-01-17' }
    ];
  } finally {
    loading.value = false;
  }
};

const createUser = async () => {
  try {
    // Simulate API call
    const newUser = {
      id: users.value.length + 1,
      name: userForm.value.name,
      email: userForm.value.email,
      created_at: new Date().toISOString().split('T')[0]
    };
    
    users.value.push(newUser);
    closeForm();
  } catch (err) {
    error.value = 'Không thể tạo người dùng';
  }
};

const updateUser = async () => {
  try {
    const index = users.value.findIndex(u => u.id === editingUser.value.id);
    if (index !== -1) {
      users.value[index] = { ...editingUser.value, ...userForm.value };
    }
    closeForm();
  } catch (err) {
    error.value = 'Không thể cập nhật người dùng';
  }
};

const deleteUser = async (id) => {
  if (confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
    try {
      users.value = users.value.filter(u => u.id !== id);
    } catch (err) {
      error.value = 'Không thể xóa người dùng';
    }
  }
};

const editUser = (user) => {
  editingUser.value = user;
  userForm.value = { ...user, password: '' };
};

const closeForm = () => {
  showCreateForm.value = false;
  editingUser.value = null;
  userForm.value = { name: '', email: '', password: '' };
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('vi-VN');
};

// Lifecycle
onMounted(() => {
  fetchUsers();
});
</script> 