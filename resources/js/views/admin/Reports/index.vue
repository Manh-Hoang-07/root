<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
  <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Báo cáo & Thống kê</h1>
          <p class="text-gray-600">Phân tích dữ liệu và báo cáo chi tiết</p>
          </div>
        <div class="flex items-center space-x-3">
          <button 
            @click="refreshData"
            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
          >
            <ArrowPathIcon class="w-5 h-5" />
            <span>Làm mới</span>
          </button>
          <button 
            @click="exportReport"
            class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl font-medium hover:from-green-700 hover:to-emerald-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
          >
            <ArrowDownTrayIcon class="w-5 h-5" />
            <span>Xuất báo cáo</span>
          </button>
        </div>
      </div>
          </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Date From -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Từ ngày</label>
          <input 
            v-model="filters.dateFrom"
            type="date"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
          />
          </div>

        <!-- Date To -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Đến ngày</label>
          <input 
            v-model="filters.dateTo"
            type="date"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
          />
        </div>

        <!-- Report Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Loại báo cáo</label>
          <select 
            v-model="filters.reportType"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
          >
            <option value="sales">Báo cáo doanh thu</option>
            <option value="orders">Báo cáo đơn hàng</option>
            <option value="products">Báo cáo sản phẩm</option>
            <option value="customers">Báo cáo khách hàng</option>
          </select>
      </div>

        <!-- Generate Report -->
        <div class="flex items-end">
          <button 
            @click="generateReport"
            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transition-all duration-200"
          >
            Tạo báo cáo
          </button>
        </div>
        </div>
      </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div 
        v-for="metric in keyMetrics" 
        :key="metric.title"
        class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300 border border-gray-100"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">{{ metric.title }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ metric.value }}</p>
            <div class="flex items-center mt-2">
              <span 
                :class="[
                  'text-sm font-medium',
                  metric.change >= 0 ? 'text-green-600' : 'text-red-600'
                ]"
              >
                {{ metric.change >= 0 ? '+' : '' }}{{ metric.change }}%
              </span>
              <span class="text-gray-500 text-sm ml-1">so với tháng trước</span>
            </div>
          </div>
          <div 
            class="w-12 h-12 rounded-xl flex items-center justify-center"
            :class="metric.bgColor"
          >
            <component :is="metric.icon" class="w-6 h-6 text-white" />
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Revenue Chart -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900">Doanh thu theo thời gian</h3>
          <div class="flex items-center space-x-2">
            <button 
              v-for="period in chartPeriods" 
              :key="period.value"
              @click="selectedPeriod = period.value"
              :class="[
                'px-3 py-1 text-sm rounded-lg transition-all duration-200',
                selectedPeriod === period.value 
                  ? 'bg-indigo-100 text-indigo-700' 
                  : 'text-gray-500 hover:bg-gray-100'
              ]"
            >
              {{ period.label }}
            </button>
          </div>
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
            <p class="text-xs text-gray-600 mt-2">{{ item.label }}</p>
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

    <!-- Top Products & Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Top Products -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900">Sản phẩm bán chạy</h3>
          <button class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            Xem tất cả
          </button>
        </div>
        <div class="space-y-4">
          <div 
            v-for="product in topProducts" 
            :key="product.id"
            class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200"
          >
            <div class="flex items-center">
              <img 
                :src="product.image" 
                :alt="product.name"
                class="w-12 h-12 rounded-lg object-cover mr-4"
              />
              <div>
                <p class="font-medium text-gray-900">{{ product.name }}</p>
                <p class="text-sm text-gray-600">{{ product.category }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-medium text-gray-900">{{ formatCurrency(product.revenue) }}</p>
              <p class="text-sm text-gray-600">{{ product.sold }} đã bán</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
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
                <p class="font-medium text-gray-900">#{{ order.order_number }}</p>
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
    </div>

    <!-- Customer Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Customer Growth -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900">Tăng trưởng khách hàng</h3>
          <button class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            Xem chi tiết
          </button>
        </div>
        <div class="h-64 flex items-end justify-between space-x-2">
          <div 
            v-for="(item, index) in customerGrowthData" 
            :key="index"
            class="flex-1 flex flex-col items-center"
          >
            <div 
              class="w-full bg-gradient-to-t from-green-500 to-green-300 rounded-t-lg transition-all duration-300 hover:from-green-600 hover:to-green-400"
              :style="{ height: `${(item.value / maxCustomers) * 200}px` }"
            ></div>
            <p class="text-xs text-gray-600 mt-2">{{ item.month }}</p>
            <p class="text-xs font-medium text-gray-900">{{ item.value }}</p>
          </div>
        </div>
      </div>

      <!-- Top Customers -->
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900">Khách hàng VIP</h3>
          <button class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            Xem tất cả
          </button>
        </div>
        <div class="space-y-4">
          <div 
            v-for="customer in topCustomers" 
            :key="customer.id"
            class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200"
          >
            <div class="flex items-center">
              <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold mr-4">
                {{ customer.name.charAt(0) }}
              </div>
              <div>
                <p class="font-medium text-gray-900">{{ customer.name }}</p>
                <p class="text-sm text-gray-600">{{ customer.email }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-medium text-gray-900">{{ formatCurrency(customer.total_spent) }}</p>
              <p class="text-sm text-gray-600">{{ customer.orders }} đơn hàng</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { 
  ArrowPathIcon,
  ArrowDownTrayIcon,
  CurrencyDollarIcon,
  ShoppingBagIcon,
  UsersIcon,
  ChartBarIcon,
  ArrowTrendingUpIcon
} from '@heroicons/vue/24/outline'

// Filters
const filters = ref({
  dateFrom: '',
  dateTo: '',
  reportType: 'sales'
})

// Chart periods
const chartPeriods = ref([
  { label: '7 ngày', value: '7d' },
  { label: '30 ngày', value: '30d' },
  { label: '90 ngày', value: '90d' }
])

const selectedPeriod = ref('30d')

// Key metrics
const keyMetrics = ref([
  {
    title: 'Tổng doanh thu',
    value: '2.5 tỷ VNĐ',
    change: 15.2,
    icon: CurrencyDollarIcon,
    bgColor: 'bg-gradient-to-br from-green-500 to-green-600'
  },
  {
    title: 'Đơn hàng',
    value: '1,234',
    change: 8.7,
    icon: ShoppingBagIcon,
    bgColor: 'bg-gradient-to-br from-blue-500 to-blue-600'
  },
  {
    title: 'Khách hàng mới',
    value: '89',
    change: 12.1,
    icon: UsersIcon,
    bgColor: 'bg-gradient-to-br from-purple-500 to-purple-600'
  },
  {
    title: 'Tỷ lệ chuyển đổi',
    value: '3.2%',
    change: -2.1,
    icon: ChartBarIcon,
    bgColor: 'bg-gradient-to-br from-orange-500 to-orange-600'
  }
])

// Revenue chart data
const revenueData = ref([
  { label: 'T1', value: 150000000 },
  { label: 'T2', value: 180000000 },
  { label: 'T3', value: 220000000 },
  { label: 'T4', value: 190000000 },
  { label: 'T5', value: 250000000 },
  { label: 'T6', value: 280000000 },
  { label: 'T7', value: 320000000 },
  { label: 'T8', value: 300000000 },
  { label: 'T9', value: 350000000 },
  { label: 'T10', value: 380000000 },
  { label: 'T11', value: 420000000 },
  { label: 'T12', value: 450000000 }
])

const maxRevenue = Math.max(...revenueData.value.map(item => item.value))

// Order status data
const orderStatusData = ref([
  { status: 'Đã hoàn thành', count: 456, percentage: 65, color: 'bg-green-500' },
  { status: 'Đang xử lý', count: 234, percentage: 33, color: 'bg-blue-500' },
  { status: 'Đã hủy', count: 23, percentage: 3, color: 'bg-red-500' },
  { status: 'Chờ xác nhận', count: 12, percentage: 2, color: 'bg-yellow-500' }
])

// Top products
const topProducts = ref([
  {
    id: 1,
    name: 'iPhone 15 Pro Max',
    category: 'Điện thoại',
    revenue: 450000000,
    sold: 15,
    image: 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=100'
  },
  {
    id: 2,
    name: 'MacBook Pro 14"',
    category: 'Laptop',
    revenue: 380000000,
    sold: 8,
    image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=100'
  },
  {
    id: 3,
    name: 'Samsung Galaxy S24',
    category: 'Điện thoại',
    revenue: 320000000,
    sold: 12,
    image: 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=100'
  },
  {
    id: 4,
    name: 'AirPods Pro',
    category: 'Phụ kiện',
    revenue: 180000000,
    sold: 30,
    image: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=100'
  }
])

// Recent orders
const recentOrders = ref([
  { id: 1, order_number: 'ORD-001', customer: 'Nguyễn Văn A', amount: 2500000, status: 'Đã hoàn thành' },
  { id: 2, order_number: 'ORD-002', customer: 'Trần Thị B', amount: 1800000, status: 'Đang xử lý' },
  { id: 3, order_number: 'ORD-003', customer: 'Lê Văn C', amount: 3200000, status: 'Chờ xác nhận' },
  { id: 4, order_number: 'ORD-004', customer: 'Phạm Thị D', amount: 1500000, status: 'Đã hoàn thành' }
])

// Customer growth data
const customerGrowthData = ref([
  { month: 'T1', value: 120 },
  { month: 'T2', value: 145 },
  { month: 'T3', value: 168 },
  { month: 'T4', value: 189 },
  { month: 'T5', value: 210 },
  { month: 'T6', value: 234 },
  { month: 'T7', value: 256 },
  { month: 'T8', value: 278 },
  { month: 'T9', value: 295 },
  { month: 'T10', value: 312 },
  { month: 'T11', value: 328 },
  { month: 'T12', value: 345 }
])

const maxCustomers = Math.max(...customerGrowthData.value.map(item => item.value))

// Top customers
const topCustomers = ref([
  { id: 1, name: 'Nguyễn Văn A', email: 'nguyenvana@example.com', total_spent: 45000000, orders: 12 },
  { id: 2, name: 'Trần Thị B', email: 'tranthib@example.com', total_spent: 38000000, orders: 8 },
  { id: 3, name: 'Lê Văn C', email: 'levanc@example.com', total_spent: 32000000, orders: 15 },
  { id: 4, name: 'Phạm Thị D', email: 'phamthid@example.com', total_spent: 28000000, orders: 6 }
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

const refreshData = () => {
  // Implement data refresh logic
}

const exportReport = () => {
  // Implement export logic
}

const generateReport = () => {
  // Implement report generation logic
}

onMounted(() => {
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
