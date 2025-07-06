<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Báo cáo thống kê</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 text-blue-600">
            <span class="text-2xl">💰</span>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Doanh thu tháng</p>
            <p class="text-2xl font-semibold text-gray-900">{{ formatPrice(stats.monthlyRevenue) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-600">
            <span class="text-2xl">📦</span>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Đơn hàng mới</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.newOrders }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
            <span class="text-2xl">👥</span>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Khách hàng mới</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.newCustomers }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100 text-purple-600">
            <span class="text-2xl">📈</span>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Tăng trưởng</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.growth }}%</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Revenue Chart -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Doanh thu theo tháng</h3>
        <div class="space-y-3">
          <div 
            v-for="(revenue, month) in revenueData" 
            :key="month"
            class="flex items-center justify-between"
          >
            <span class="text-sm text-gray-600">{{ month }}</span>
            <div class="flex items-center space-x-2">
              <div class="w-32 bg-gray-200 rounded-full h-2">
                <div 
                  class="bg-blue-600 h-2 rounded-full"
                  :style="{ width: `${(revenue / maxRevenue) * 100}%` }"
                ></div>
              </div>
              <span class="text-sm font-medium text-gray-900">{{ formatPrice(revenue) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Products -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sản phẩm bán chạy</h3>
        <div class="space-y-3">
          <div 
            v-for="product in topProducts" 
            :key="product.id"
            class="flex items-center justify-between"
          >
            <div class="flex items-center space-x-3">
              <img 
                :src="product.image" 
                :alt="product.name"
                class="w-10 h-10 rounded-lg object-cover"
              >
              <div>
                <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                <p class="text-xs text-gray-500">{{ product.category }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-sm font-medium text-gray-900">{{ product.sales }}</p>
              <p class="text-xs text-gray-500">đã bán</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Đơn hàng gần đây</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Mã đơn hàng
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Khách hàng
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tổng tiền
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Trạng thái
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ngày tạo
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="order in recentOrders" :key="order.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                #{{ order.order_number }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ order.customer_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatPrice(order.total_amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="{
                    'px-2 py-1 text-xs font-semibold rounded-full': true,
                    'bg-green-100 text-green-800': order.status === 'completed',
                    'bg-yellow-100 text-yellow-800': order.status === 'processing',
                    'bg-red-100 text-red-800': order.status === 'cancelled'
                  }"
                >
                  {{ getStatusText(order.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(order.created_at) }}
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

const stats = ref({
  monthlyRevenue: 0,
  newOrders: 0,
  newCustomers: 0,
  growth: 0
});

const revenueData = ref({});
const topProducts = ref([]);
const recentOrders = ref([]);

const loadStats = async () => {
  try {
    const response = await fetch('/api/admin/stats');
    const data = await response.json();
    stats.value = data.data.stats || {};
    revenueData.value = data.data.revenue || {};
    topProducts.value = data.data.topProducts || [];
    recentOrders.value = data.data.recentOrders || [];
  } catch (error) {
    console.error('Error loading stats:', error);
    // Mock data for demo
    stats.value = {
      monthlyRevenue: 15000000,
      newOrders: 45,
      newCustomers: 23,
      growth: 12.5
    };
    revenueData.value = {
      'Tháng 1': 12000000,
      'Tháng 2': 13500000,
      'Tháng 3': 14200000,
      'Tháng 4': 15000000
    };
    topProducts.value = [
      {
        id: 1,
        name: 'iPhone 15 Pro',
        category: 'Điện thoại',
        image: 'https://via.placeholder.com/40',
        sales: 156
      },
      {
        id: 2,
        name: 'MacBook Air M2',
        category: 'Laptop',
        image: 'https://via.placeholder.com/40',
        sales: 89
      },
      {
        id: 3,
        name: 'AirPods Pro',
        category: 'Phụ kiện',
        image: 'https://via.placeholder.com/40',
        sales: 234
      }
    ];
    recentOrders.value = [
      {
        id: 1,
        order_number: 'ORD001',
        customer_name: 'Nguyễn Văn A',
        total_amount: 25000000,
        status: 'completed',
        created_at: '2024-01-15'
      },
      {
        id: 2,
        order_number: 'ORD002',
        customer_name: 'Trần Thị B',
        total_amount: 18000000,
        status: 'processing',
        created_at: '2024-01-14'
      }
    ];
  }
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN');
};

const getStatusText = (status) => {
  const statusMap = {
    'completed': 'Hoàn thành',
    'processing': 'Đang xử lý',
    'cancelled': 'Đã hủy'
  };
  return statusMap[status] || status;
};

const maxRevenue = computed(() => {
  return Math.max(...Object.values(revenueData.value));
});

onMounted(() => {
  loadStats();
});
</script> 