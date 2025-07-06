<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
      <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-600">Xin chào, {{ user.name }}!</span>
        <button 
          @click="logout"
          class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors"
        >
          Đăng xuất
        </button>
      </div>
    </div>

    <!-- User Stats -->
    <div class="grid md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Tổng đơn hàng</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.totalOrders }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Tổng chi tiêu</p>
            <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.totalSpent) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100 text-purple-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Điểm tích lũy</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.points }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">Thao tác nhanh</h2>
      <div class="grid md:grid-cols-4 gap-4">
        <button 
          @click="$router.push('/user/orders')"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <div class="p-2 bg-blue-100 rounded-lg mb-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <h3 class="font-medium text-sm">Hồ sơ</h3>
        </button>

        <button 
          @click="$router.push('/user/orders')"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <div class="p-2 bg-green-100 rounded-lg mb-2">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <h3 class="font-medium text-sm">Đơn hàng</h3>
        </button>

        <button 
          @click="$router.push('/user/wishlist')"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <div class="p-2 bg-red-100 rounded-lg mb-2">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
          </div>
          <h3 class="font-medium text-sm">Yêu thích</h3>
        </button>

        <button 
          @click="$router.push('/user/settings')"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <div class="p-2 bg-purple-100 rounded-lg mb-2">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
          </div>
          <h3 class="font-medium text-sm">Cài đặt</h3>
        </button>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">Đơn hàng gần đây</h2>
      <div class="space-y-4">
        <div v-for="order in recentOrders" :key="order.id" class="border border-gray-200 rounded-lg p-4">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="font-medium">Đơn hàng #{{ order.id }}</h3>
              <p class="text-sm text-gray-600">{{ order.items.length }} sản phẩm</p>
              <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
            </div>
            <div class="text-right">
              <p class="font-medium">{{ formatCurrency(order.total) }}</p>
              <span :class="getStatusClass(order.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                {{ getStatusText(order.status) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// State
const user = ref({
  name: 'Nguyễn Văn A',
  email: 'nguyenvana@example.com'
});

const stats = ref({
  totalOrders: 0,
  totalSpent: 0,
  points: 0
});

const recentOrders = ref([]);

// Methods
const fetchUserStats = async () => {
  try {
    // Simulate API call
    stats.value = {
      totalOrders: 15,
      totalSpent: 2500000,
      points: 1250
    };
  } catch (error) {
    console.error('Error fetching user stats:', error);
  }
};

const fetchRecentOrders = async () => {
  try {
    // Simulate API call
    recentOrders.value = [
      {
        id: '001',
        items: ['Sản phẩm A', 'Sản phẩm B'],
        total: 500000,
        status: 'completed',
        created_at: '2024-01-15'
      },
      {
        id: '002',
        items: ['Sản phẩm C'],
        total: 300000,
        status: 'processing',
        created_at: '2024-01-16'
      },
      {
        id: '003',
        items: ['Sản phẩm D', 'Sản phẩm E', 'Sản phẩm F'],
        total: 800000,
        status: 'shipped',
        created_at: '2024-01-17'
      }
    ];
  } catch (error) {
    console.error('Error fetching recent orders:', error);
  }
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('vi-VN');
};

const getStatusClass = (status) => {
  const classes = {
    'completed': 'bg-green-100 text-green-800',
    'processing': 'bg-yellow-100 text-yellow-800',
    'shipped': 'bg-blue-100 text-blue-800',
    'cancelled': 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
  const texts = {
    'completed': 'Hoàn thành',
    'processing': 'Đang xử lý',
    'shipped': 'Đã gửi hàng',
    'cancelled': 'Đã hủy'
  };
  return texts[status] || 'Không xác định';
};

const logout = () => {
  // Simulate logout
  router.push('/login');
};

// Lifecycle
onMounted(() => {
  fetchUserStats();
  fetchRecentOrders();
});
</script> 