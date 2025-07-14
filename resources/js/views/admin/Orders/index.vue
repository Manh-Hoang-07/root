<template>
  <div class="space-y-6 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6 w-full max-w-full overflow-x-hidden">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
  <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Quản lý đơn hàng</h1>
          <p class="text-gray-600">Theo dõi và xử lý tất cả đơn hàng</p>
        </div>
        <button 
          @click="exportOrders"
          class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl font-medium hover:from-green-700 hover:to-emerald-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
        >
          <ArrowDownTrayIcon class="w-5 h-5" />
          <span>Xuất báo cáo</span>
        </button>
      </div>
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

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Search -->
        <div class="relative lg:col-span-2">
          <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
        <input 
          v-model="filters.search"
          type="text" 
            placeholder="Tìm kiếm đơn hàng..."
            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
          />
        </div>

        <!-- Status Filter -->
        <select 
          v-model="filters.status"
          class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
        >
          <option value="">Tất cả trạng thái</option>
          <option value="pending">Chờ xác nhận</option>
          <option value="confirmed">Đã xác nhận</option>
          <option value="processing">Đang xử lý</option>
          <option value="shipped">Đã gửi hàng</option>
          <option value="delivered">Đã giao hàng</option>
          <option value="cancelled">Đã hủy</option>
        </select>

        <!-- Date From -->
        <input 
          v-model="filters.dateFrom"
          type="date"
          class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
        />

        <!-- Date To -->
        <input 
          v-model="filters.dateTo"
          type="date" 
          class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
        />
      </div>
      
      <div class="flex justify-between items-center mt-4">
        <button 
          @click="clearFilters"
          class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 font-medium"
        >
          Xóa bộ lọc
        </button>
        
        <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-600">Hiển thị:</span>
          <select 
            v-model="itemsPerPage"
            class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8">
      <div class="overflow-x-auto">
        <table class="w-full min-w-max text-sm">
          <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
              <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Mã đơn hàng</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Khách hàng</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Sản phẩm</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tổng tiền</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Trạng thái</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Ngày đặt</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Thao tác</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr 
              v-for="order in paginatedOrders" 
              :key="order.id"
              class="hover:bg-gray-50 transition-colors duration-200"
            >
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">#{{ order.order_number }}</div>
                <div class="text-sm text-gray-500">{{ order.items.length }} sản phẩm</div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white font-semibold text-sm mr-3">
                    {{ order.customer.name.charAt(0) }}
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ order.customer.name }}</div>
                    <div class="text-sm text-gray-500">{{ order.customer.email }}</div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center space-x-2">
                  <img 
                    v-for="item in order.items.slice(0, 2)" 
                    :key="item.id"
                    :src="item.product.image" 
                    :alt="item.product.name"
                    class="w-8 h-8 rounded-lg object-cover"
                  />
                  <div v-if="order.items.length > 2" class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-xs text-gray-600">
                    +{{ order.items.length - 2 }}
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ formatCurrency(order.total_amount) }}</div>
                <div class="text-sm text-gray-500">{{ order.payment_method }}</div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getStatusClass(order.status)"
                >
                  {{ getStatusLabel(order.status) }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(order.created_at) }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center space-x-2">
                <button 
                  @click="viewOrder(order)"
                    class="text-indigo-600 hover:text-indigo-900 p-2 rounded-lg hover:bg-indigo-50 transition-all duration-200"
                    title="Xem chi tiết"
                >
                    <EyeIcon class="w-4 h-4" />
                </button>
                <button 
                  @click="updateStatus(order)"
                    class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-all duration-200"
                    title="Cập nhật trạng thái"
                  >
                    <PencilIcon class="w-4 h-4" />
                  </button>
                  <button 
                    @click="printInvoice(order)"
                    class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-all duration-200"
                    title="In hóa đơn"
                  >
                    <PrinterIcon class="w-4 h-4" />
                </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="bg-white rounded-2xl shadow-lg px-6 py-4 border border-gray-100">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Hiển thị {{ startIndex + 1 }} đến {{ endIndex }} trong tổng số {{ filteredOrders.length }} đơn hàng
        </div>
        <div class="flex items-center space-x-2">
          <button 
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
          >
            Trước
          </button>
          <div class="flex items-center space-x-1">
            <button 
              v-for="page in visiblePages" 
              :key="page"
              @click="goToPage(page)"
              :class="[
                'px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                page === currentPage 
                  ? 'bg-indigo-600 text-white' 
                  : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
          </div>
          <button 
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
          >
            Sau
          </button>
        </div>
      </div>
    </div>

    <!-- Update Status Modal -->
    <div 
      v-if="showStatusModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeStatusModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            Cập nhật trạng thái đơn hàng
          </h3>
        </div>
        
        <div class="p-6 space-y-4">
            <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Mã đơn hàng</label>
            <p class="text-lg font-semibold text-gray-900">#{{ selectedOrder?.order_number }}</p>
              </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái hiện tại</label>
            <span 
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="getStatusClass(selectedOrder?.status)"
            >
              {{ getStatusLabel(selectedOrder?.status) }}
            </span>
            </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái mới</label>
            <select 
              v-model="newStatus"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
            >
              <option value="pending">Chờ xác nhận</option>
              <option value="confirmed">Đã xác nhận</option>
              <option value="processing">Đang xử lý</option>
              <option value="shipped">Đã gửi hàng</option>
              <option value="delivered">Đã giao hàng</option>
              <option value="cancelled">Đã hủy</option>
            </select>
                  </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú (tùy chọn)</label>
            <textarea 
              v-model="statusNote"
              rows="3"
              placeholder="Nhập ghi chú về việc cập nhật trạng thái..."
              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
            ></textarea>
          </div>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
          <button 
            @click="closeStatusModal"
            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all duration-200"
          >
            Hủy
          </button>
          <button 
            @click="saveStatus"
            class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200"
          >
            Cập nhật
          </button>
    </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { 
  ArrowDownTrayIcon,
  MagnifyingGlassIcon,
  EyeIcon,
  PencilIcon,
  PrinterIcon,
  ShoppingBagIcon,
  CheckCircleIcon,
  ClockIcon,
  CurrencyDollarIcon
} from '@heroicons/vue/24/outline'

// Stats data
const stats = ref([
  {
    title: 'Tổng đơn hàng',
    value: '1,234',
    change: 15.2,
    icon: ShoppingBagIcon,
    bgColor: 'bg-gradient-to-br from-blue-500 to-blue-600'
  },
  {
    title: 'Đã hoàn thành',
    value: '856',
    change: 8.7,
    icon: CheckCircleIcon,
    bgColor: 'bg-gradient-to-br from-green-500 to-green-600'
  },
  {
    title: 'Đang xử lý',
    value: '234',
    change: -2.1,
    icon: ClockIcon,
    bgColor: 'bg-gradient-to-br from-yellow-500 to-yellow-600'
  },
  {
    title: 'Tổng doanh thu',
    value: '2.5 tỷ',
    change: 22.1,
    icon: CurrencyDollarIcon,
    bgColor: 'bg-gradient-to-br from-purple-500 to-purple-600'
  }
])

// Mock orders data
const orders = ref([
  {
    id: 1,
    order_number: 'ORD-2024-001',
    customer: {
      name: 'Nguyễn Văn A',
      email: 'nguyenvana@example.com',
      phone: '0123456789'
    },
    items: [
      {
        id: 1,
        product: {
          name: 'iPhone 15 Pro Max 256GB',
          image: 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=100'
        },
        quantity: 1,
        price: 29990000
      }
    ],
    total_amount: 29990000,
    payment_method: 'Chuyển khoản',
    status: 'delivered',
    created_at: '2024-12-20T10:30:00Z',
    updated_at: '2024-12-22T14:25:00Z'
  },
  {
    id: 2,
    order_number: 'ORD-2024-002',
    customer: {
      name: 'Trần Thị B',
      email: 'tranthib@example.com',
      phone: '0987654321'
    },
    items: [
      {
        id: 2,
        product: {
          name: 'Samsung Galaxy S24 Ultra',
          image: 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=100'
        },
        quantity: 1,
        price: 24990000
      },
      {
        id: 3,
        product: {
          name: 'AirPods Pro 2nd Gen',
          image: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=100'
        },
        quantity: 1,
        price: 5990000
      }
    ],
    total_amount: 30980000,
    payment_method: 'Tiền mặt',
    status: 'processing',
    created_at: '2024-12-19T15:45:00Z',
    updated_at: '2024-12-21T09:15:00Z'
  },
  {
    id: 3,
    order_number: 'ORD-2024-003',
    customer: {
      name: 'Lê Văn C',
      email: 'levanc@example.com',
      phone: '0369852147'
    },
    items: [
      {
        id: 4,
        product: {
          name: 'MacBook Pro 14" M3 Pro',
          image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=100'
        },
        quantity: 1,
        price: 45990000
      }
    ],
    total_amount: 45990000,
    payment_method: 'Chuyển khoản',
    status: 'confirmed',
    created_at: '2024-12-18T11:20:00Z',
    updated_at: '2024-12-20T16:30:00Z'
  },
  {
    id: 4,
    order_number: 'ORD-2024-004',
    customer: {
      name: 'Phạm Thị D',
      email: 'phamthid@example.com',
      phone: '0523698741'
    },
    items: [
      {
        id: 5,
        product: {
          name: 'iPad Pro 12.9" M2',
          image: 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=100'
        },
        quantity: 1,
        price: 28990000
      }
    ],
    total_amount: 28990000,
    payment_method: 'Tiền mặt',
    status: 'pending',
    created_at: '2024-12-20T08:15:00Z',
    updated_at: '2024-12-20T08:15:00Z'
  },
  {
    id: 5,
    order_number: 'ORD-2024-005',
    customer: {
      name: 'Hoàng Văn E',
      email: 'hoangvane@example.com',
      phone: '0147852369'
    },
    items: [
      {
        id: 6,
        product: {
          name: 'Xiaomi 14 Ultra',
          image: 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=100'
        },
        quantity: 1,
        price: 19990000
      }
    ],
    total_amount: 19990000,
    payment_method: 'Chuyển khoản',
    status: 'cancelled',
    created_at: '2024-12-17T14:30:00Z',
    updated_at: '2024-12-18T10:45:00Z'
  }
])

// Filters
const filters = ref({
  search: '',
  status: '',
  dateFrom: '',
  dateTo: ''
})

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Modal states
const showStatusModal = ref(false)
const selectedOrder = ref(null)
const newStatus = ref('')
const statusNote = ref('')

// Computed properties
const filteredOrders = computed(() => {
  let filtered = orders.value

  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    filtered = filtered.filter(order => 
      order.order_number.toLowerCase().includes(search) ||
      order.customer.name.toLowerCase().includes(search) ||
      order.customer.email.toLowerCase().includes(search)
    )
  }

  if (filters.value.status) {
    filtered = filtered.filter(order => order.status === filters.value.status)
  }

  if (filters.value.dateFrom) {
    filtered = filtered.filter(order => 
      new Date(order.created_at) >= new Date(filters.value.dateFrom)
    )
  }

  if (filters.value.dateTo) {
    filtered = filtered.filter(order => 
      new Date(order.created_at) <= new Date(filters.value.dateTo)
    )
  }

  // Sort by created_at descending
  filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredOrders.value.length / itemsPerPage.value))

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, filteredOrders.value.length))

const paginatedOrders = computed(() => {
  return filteredOrders.value.slice(startIndex.value, endIndex.value)
})

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const formatDate = (dateString) => {
  return new Intl.DateTimeFormat('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(dateString))
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Chờ xác nhận',
    confirmed: 'Đã xác nhận',
    processing: 'Đang xử lý',
    shipped: 'Đã gửi hàng',
    delivered: 'Đã giao hàng',
    cancelled: 'Đã hủy'
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    processing: 'bg-indigo-100 text-indigo-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const clearFilters = () => {
  filters.value = {
    search: '',
    status: '',
    dateFrom: '',
    dateTo: ''
  }
  currentPage.value = 1
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const goToPage = (page) => {
  currentPage.value = page
}

const viewOrder = (order) => {
  console.log('View order:', order)
  // Implement view order logic
}

const updateStatus = (order) => {
  selectedOrder.value = order
  newStatus.value = order.status
  statusNote.value = ''
  showStatusModal.value = true
}

const saveStatus = () => {
  if (selectedOrder.value && newStatus.value) {
    const order = orders.value.find(o => o.id === selectedOrder.value.id)
    if (order) {
      order.status = newStatus.value
      order.updated_at = new Date().toISOString()
      console.log('Updated order status:', {
        orderId: order.id,
        newStatus: newStatus.value,
        note: statusNote.value
      })
    }
  }
  closeStatusModal()
}

const closeStatusModal = () => {
  showStatusModal.value = false
  selectedOrder.value = null
  newStatus.value = ''
  statusNote.value = ''
}

const printInvoice = (order) => {
  console.log('Print invoice for order:', order)
  // Implement print invoice logic
}

const exportOrders = () => {
  console.log('Export orders:', filteredOrders.value)
  // Implement export logic
}

onMounted(() => {
  console.log('Orders page mounted')
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
