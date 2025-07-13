<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
      <p class="text-gray-600">Tổng quan hệ thống quản lý</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div 
        v-for="stat in stats" 
        :key="stat.title"
        class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300 border border-gray-100"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">{{ stat.title }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stat.value }}</p>
            <div class="flex items-center mt-2">
              <span 
                :class="[
                  'text-sm font-medium',
                  stat.change >= 0 ? 'text-green-600' : 'text-red-600'
                ]"
              >
                {{ stat.change >= 0 ? '+' : '' }}{{ stat.change }}%
              </span>
              <span class="text-gray-500 text-sm ml-1">so với tháng trước</span>
            </div>
          </div>
          <div 
            class="w-12 h-12 rounded-xl flex items-center justify-center"
            :class="stat.bgColor"
          >
            <component :is="stat.icon" class="w-6 h-6 text-white" />
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Revenue Chart -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900">Doanh thu theo tháng</h3>
          <button class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            Xem chi tiết
          </button>
        </div>
        <div class="h-64 flex items-end justify-between space-x-2">
          <div 
            v-for="(item, index) in revenueData" 
            :key="index"
            class="flex-1 flex flex-col items-center"
          >
            <div 
              class="w-full bg-gradient-to-t from-indigo-500 to-indigo-300 rounded-t-lg transition-all duration-300 hover:from-indigo-600 hover:to-indigo-400"
              :style="{ height: `${(item.value / maxRevenue) * 200}px` }"
            ></div>
            <p class="text-xs text-gray-600 mt-2">{{ item.month }}</p>
            <p class="text-xs font-medium text-gray-900">{{ formatCurrency(item.value) }}</p>
          </div>
        </div>
      </div>

      <!-- Orders Chart -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900">Đơn hàng theo trạng thái</h3>
          <button class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            Xem chi tiết
          </button>
        </div>
        <div class="space-y-4">
          <div 
            v-for="order in orderStatusData" 
            :key="order.status"
            class="flex items-center justify-between"
          >
            <div class="flex items-center">
              <div 
                class="w-3 h-3 rounded-full mr-3"
                :class="order.color"
              ></div>
              <span class="text-sm text-gray-700">{{ order.status }}</span>
            </div>
            <div class="flex items-center">
              <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                <div 
                  class="h-2 rounded-full transition-all duration-500"
                  :class="order.color"
                  :style="{ width: `${order.percentage}%` }"
                ></div>
              </div>
              <span class="text-sm font-medium text-gray-900">{{ order.count }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent Orders -->
      <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900">Đơn hàng gần đây</h3>
          <button class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            Xem tất cả
          </button>
        </div>
        <div class="space-y-4">
          <div 
            v-for="order in recentOrders" 
            :key="order.id"
            class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200"
          >
            <div class="flex items-center">
              <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                <ShoppingBagIcon class="w-5 h-5 text-indigo-600" />
              </div>
              <div>
                <p class="font-medium text-gray-900">#{{ order.id }}</p>
                <p class="text-sm text-gray-600">{{ order.customer }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-medium text-gray-900">{{ formatCurrency(order.amount) }}</p>
              <span 
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="getStatusClass(order.status)"
              >
                {{ order.status }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Thao tác nhanh</h3>
        <div class="space-y-3">
          <button 
            v-for="action in quickActions" 
            :key="action.title"
            @click="handleQuickAction(action.action)"
            class="w-full flex items-center p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl hover:from-indigo-100 hover:to-purple-100 transition-all duration-300 transform hover:scale-105 border border-indigo-100"
          >
            <component :is="action.icon" class="w-5 h-5 text-indigo-600 mr-3" />
            <span class="font-medium text-gray-900">{{ action.title }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { 
  UsersIcon, 
  ShoppingBagIcon, 
  CurrencyDollarIcon, 
  ChartBarIcon,
  PlusIcon,
  CogIcon,
  DocumentTextIcon,
  BellIcon
} from '@heroicons/vue/24/outline'

// Stats data
const stats = ref([
  {
    title: 'Tổng doanh thu',
    value: '2.5 tỷ VNĐ',
    change: 12.5,
    icon: CurrencyDollarIcon,
    bgColor: 'bg-gradient-to-br from-green-500 to-green-600'
  },
  {
    title: 'Đơn hàng',
    value: '1,234',
    change: 8.2,
    icon: ShoppingBagIcon,
    bgColor: 'bg-gradient-to-br from-blue-500 to-blue-600'
  },
  {
    title: 'Khách hàng',
    value: '856',
    change: -2.1,
    icon: UsersIcon,
    bgColor: 'bg-gradient-to-br from-purple-500 to-purple-600'
  },
  {
    title: 'Tỷ lệ chuyển đổi',
    value: '3.2%',
    change: 15.8,
    icon: ChartBarIcon,
    bgColor: 'bg-gradient-to-br from-orange-500 to-orange-600'
  }
])

// Revenue chart data
const revenueData = ref([
  { month: 'T1', value: 150000000 },
  { month: 'T2', value: 180000000 },
  { month: 'T3', value: 220000000 },
  { month: 'T4', value: 190000000 },
  { month: 'T5', value: 250000000 },
  { month: 'T6', value: 280000000 },
  { month: 'T7', value: 320000000 },
  { month: 'T8', value: 300000000 },
  { month: 'T9', value: 350000000 },
  { month: 'T10', value: 380000000 },
  { month: 'T11', value: 420000000 },
  { month: 'T12', value: 450000000 }
])

const maxRevenue = Math.max(...revenueData.value.map(item => item.value))

// Order status data
const orderStatusData = ref([
  { status: 'Đã hoàn thành', count: 456, percentage: 65, color: 'bg-green-500' },
  { status: 'Đang xử lý', count: 234, percentage: 33, color: 'bg-blue-500' },
  { status: 'Đã hủy', count: 23, percentage: 3, color: 'bg-red-500' },
  { status: 'Chờ xác nhận', count: 12, percentage: 2, color: 'bg-yellow-500' }
])

// Recent orders
const recentOrders = ref([
  { id: 'ORD-001', customer: 'Nguyễn Văn A', amount: 2500000, status: 'Đã hoàn thành' },
  { id: 'ORD-002', customer: 'Trần Thị B', amount: 1800000, status: 'Đang xử lý' },
  { id: 'ORD-003', customer: 'Lê Văn C', amount: 3200000, status: 'Chờ xác nhận' },
  { id: 'ORD-004', customer: 'Phạm Thị D', amount: 1500000, status: 'Đã hoàn thành' },
  { id: 'ORD-005', customer: 'Hoàng Văn E', amount: 2800000, status: 'Đang xử lý' }
])

// Quick actions
const quickActions = ref([
  { title: 'Thêm sản phẩm mới', action: 'add-product', icon: PlusIcon },
  { title: 'Tạo đơn hàng', action: 'create-order', icon: ShoppingBagIcon },
  { title: 'Cài đặt hệ thống', action: 'settings', icon: CogIcon },
  { title: 'Xem báo cáo', action: 'reports', icon: DocumentTextIcon }
])

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const getStatusClass = (status) => {
  const classes = {
    'Đã hoàn thành': 'bg-green-100 text-green-800',
    'Đang xử lý': 'bg-blue-100 text-blue-800',
    'Chờ xác nhận': 'bg-yellow-100 text-yellow-800',
    'Đã hủy': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const handleQuickAction = (action) => {
  console.log('Quick action:', action)
  // Implement quick action logic here
}

onMounted(() => {
  console.log('Dashboard mounted')
})
</script>

<style scoped>
/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}
</style> 