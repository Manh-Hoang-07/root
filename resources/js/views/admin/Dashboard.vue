<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
      <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-600">Xin chào, Admin!</span>
        <button 
          @click="logout"
          class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors"
        >
          Đăng xuất
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid md:grid-cols-4 gap-6">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Tổng người dùng</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.totalUsers }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Người dùng hoạt động</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.activeUsers }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Người dùng mới (tháng)</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.newUsers }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100 text-purple-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Tăng trưởng</p>
            <p class="text-2xl font-semibold text-gray-900">+{{ stats.growth }}%</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">Thao tác nhanh</h2>
      <div class="grid md:grid-cols-3 gap-4">
        <button 
          @click="$router.push('/admin/users')"
          class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="font-medium">Quản lý người dùng</h3>
            <p class="text-sm text-gray-600">Thêm, sửa, xóa người dùng</p>
          </div>
        </button>

        <button 
          @click="$router.push('/admin/settings')"
          class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="font-medium">Cài đặt hệ thống</h3>
            <p class="text-sm text-gray-600">Cấu hình ứng dụng</p>
          </div>
        </button>

        <button 
          @click="$router.push('/admin/reports')"
          class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="font-medium">Báo cáo</h3>
            <p class="text-sm text-gray-600">Xem thống kê chi tiết</p>
          </div>
        </button>
      </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">Người dùng gần đây</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày tạo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in recentUsers" :key="user.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ user.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ user.email }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(user.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ user.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// State
const stats = ref({
  totalUsers: 0,
  activeUsers: 0,
  newUsers: 0,
  growth: 0
});

const recentUsers = ref([]);

// Methods
const fetchStats = async () => {
  try {
    // Simulate API call
    stats.value = {
      totalUsers: 1250,
      activeUsers: 1180,
      newUsers: 45,
      growth: 12
    };
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
};

const fetchRecentUsers = async () => {
  try {
    // Simulate API call
    recentUsers.value = [
      { id: 1, name: 'Nguyễn Văn A', email: 'nguyenvana@example.com', created_at: '2024-01-15', status: 'active' },
      { id: 2, name: 'Trần Thị B', email: 'tranthib@example.com', created_at: '2024-01-16', status: 'active' },
      { id: 3, name: 'Lê Văn C', email: 'levanc@example.com', created_at: '2024-01-17', status: 'inactive' },
      { id: 4, name: 'Phạm Thị D', email: 'phamthid@example.com', created_at: '2024-01-18', status: 'active' }
    ];
  } catch (error) {
    console.error('Error fetching recent users:', error);
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('vi-VN');
};

const logout = () => {
  // Simulate logout
  router.push('/login');
};

// Lifecycle
onMounted(() => {
  fetchStats();
  fetchRecentUsers();
});
</script> 