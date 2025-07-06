<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý tài khoản</h1>
      <button 
        @click="showAddModal = true"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
      >
        Thêm tài khoản
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Tên, email, số điện thoại..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò</label>
          <select 
            v-model="roleFilter"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Tất cả</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
          <select 
            v-model="statusFilter"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Tất cả</option>
            <option value="active">Hoạt động</option>
            <option value="inactive">Không hoạt động</option>
          </select>
        </div>
        <div class="flex items-end">
          <button 
            @click="loadUsers"
            class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
          >
            Lọc
          </button>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Họ tên
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Trạng thái
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
            <tr v-for="user in users" :key="user.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="{
                  'px-2 py-1 text-xs font-semibold rounded-full': true,
                  'bg-green-100 text-green-800': user.status === 'active',
                  'bg-red-100 text-red-800': user.status !== 'active'
                }">
                  {{ user.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <button 
                    @click="viewUser(user)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Xem
                  </button>
                  <button 
                    @click="editUser(user)"
                    class="text-green-600 hover:text-green-900"
                  >
                    Sửa
                  </button>
                  <button 
                    @click="toggleUserStatus(user)"
                    :class="{
                      'text-yellow-600 hover:text-yellow-900': user.status === 'active',
                      'text-green-600 hover:text-green-900': user.status !== 'active'
                    }"
                  >
                    {{ user.status === 'active' ? 'Khóa' : 'Mở khóa' }}
                  </button>
                  <button 
                    @click="deleteUser(user.id)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Xóa
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4">
      <div class="flex-1 flex justify-between sm:hidden">
        <button 
          @click="previousPage"
          :disabled="currentPage === 1"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >
          Trước
        </button>
        <button 
          @click="nextPage"
          :disabled="currentPage === totalPages"
          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >
          Sau
        </button>
      </div>
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Hiển thị <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span> đến 
            <span class="font-medium">{{ Math.min(currentPage * perPage, total) }}</span> trong tổng số 
            <span class="font-medium">{{ total }}</span> kết quả
          </p>
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            <button 
              @click="previousPage"
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
            >
              Trước
            </button>
            <button 
              v-for="page in visiblePages"
              :key="page"
              @click="goToPage(page)"
              :class="{
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium': true,
                'z-10 bg-blue-50 border-blue-500 text-blue-600': page === currentPage,
                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': page !== currentPage
              }"
            >
              {{ page }}
            </button>
            <button 
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
            >
              Sau
            </button>
          </nav>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 flex items-center justify-center">
      <div class="fixed inset-0 z-40 bg-white bg-opacity-10 backdrop-blur-md"></div>
      <div class="relative z-50 bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ showEditModal ? 'Sửa tài khoản' : 'Thêm tài khoản' }}
          </h3>
          <form @submit.prevent="saveUser">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Họ tên</label>
                <input v-model="form.name" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input v-model="form.email" type="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>
              <div v-if="!showEditModal">
                <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                <input v-model="form.password" type="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select v-model="form.status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="active">Hoạt động</option>
                  <option value="inactive">Không hoạt động</option>
                </select>
              </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
              <button type="button" @click="closeModal" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Hủy</button>
              <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">{{ showEditModal ? 'Cập nhật' : 'Thêm' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const users = ref([]);
const searchQuery = ref('');
const roleFilter = ref('');
const statusFilter = ref('');
const showAddModal = ref(false);
const showEditModal = ref(false);
const editingUser = ref(null);
const currentPage = ref(1);
const perPage = ref(10);
const total = ref(0);

const form = ref({
  name: '',
  email: '',
  password: '',
  status: 'active'
});

const loadUsers = async () => {
  try {
    const params = new URLSearchParams({
      page: currentPage.value,
      per_page: perPage.value,
      search: searchQuery.value,
      status: statusFilter.value
    });
    const response = await fetch(`/api/admin/users?${params}`);
    const data = await response.json();
    users.value = data.data.data || [];
    total.value = data.data.total || 0;
  } catch (error) {
    console.error('Error loading users:', error);
  }
};

const editUser = (user) => {
  editingUser.value = user;
  form.value = {
    name: user.name,
    email: user.email,
    status: user.status
  };
  showEditModal.value = true;
};

const viewUser = (user) => {
  // Implement user detail view
  alert(`Xem chi tiết user: ${user.name}`);
};

const toggleUserStatus = async (user) => {
  try {
    const response = await fetch(`/api/admin/users/${user.id}/toggle-status`, {
      method: 'PATCH'
    });
    
    if (response.ok) {
      loadUsers();
    }
  } catch (error) {
    console.error('Error toggling user status:', error);
  }
};

const saveUser = async () => {
  try {
    const url = showEditModal.value 
      ? `/api/admin/users/${editingUser.value.id}` 
      : '/api/admin/users';
    
    const method = showEditModal.value ? 'PUT' : 'POST';
    
    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(form.value)
    });
    
    if (response.ok) {
      closeModal();
      loadUsers();
    }
  } catch (error) {
    console.error('Error saving user:', error);
  }
};

const deleteUser = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa tài khoản này?')) return;
  
  try {
    const response = await fetch(`/api/admin/users/${id}`, {
      method: 'DELETE'
    });
    
    if (response.ok) {
      loadUsers();
    }
  } catch (error) {
    console.error('Error deleting user:', error);
  }
};

const closeModal = () => {
  showAddModal.value = false;
  showEditModal.value = false;
  editingUser.value = null;
  form.value = {
    name: '',
    email: '',
    password: '',
    status: 'active'
  };
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN');
};

const totalPages = computed(() => Math.ceil(total.value / perPage.value));

const visiblePages = computed(() => {
  const pages = [];
  const start = Math.max(1, currentPage.value - 2);
  const end = Math.min(totalPages.value, currentPage.value + 2);
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    loadUsers();
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    loadUsers();
  }
};

const goToPage = (page) => {
  currentPage.value = page;
  loadUsers();
};

onMounted(() => {
  loadUsers();
});
</script> 