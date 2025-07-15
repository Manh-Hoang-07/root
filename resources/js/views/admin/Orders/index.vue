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
          <option v-for="status in statusEnums" :key="status.value" :value="status.value">
            {{ status.label }}
          </option>
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
              <option v-for="status in statusEnums" :key="status.value" :value="status.value">
                {{ status.label }}
              </option>
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
import axios from 'axios'
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

// Stats data giữ nguyên hoặc có thể lấy từ API nếu backend hỗ trợ
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

// Orders và status enums từ API
const orders = ref([])
const statusEnums = ref([]) // [{ value: 'pending', label: 'Chờ xác nhận' }, ...]

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

// Fetch orders from API
const fetchOrders = async () => {
  try {
    const response = await axios.get('/api/orders', {
      params: {
        search: filters.value.search,
        status: filters.value.status,
        date_from: filters.value.dateFrom,
        date_to: filters.value.dateTo,
        per_page: itemsPerPage.value,
        page: currentPage.value
      }
    })
    orders.value = response.data.data || response.data // tuỳ format API
  } catch (e) {
    console.error('Lỗi lấy danh sách đơn hàng:', e)
  }
}

// Fetch status enums from API
const fetchStatusEnums = async () => {
  try {
    const response = await axios.get('/api/enums/order-status')
    statusEnums.value = response.data // [{ value, label }]
  } catch (e) {
    // fallback nếu API không có, có thể lấy từ order đầu tiên
    if (orders.value.length > 0 && orders.value[0].status_label) {
      statusEnums.value = [...new Set(orders.value.map(o => ({ value: o.status, label: o.status_label })))]
    }
  }
}

onMounted(async () => {
  await fetchOrders()
  await fetchStatusEnums()
})

// Watch filter & pagination để fetch lại
import { watch } from 'vue'
watch([filters, itemsPerPage, currentPage], fetchOrders)

// Computed properties
const filteredOrders = computed(() => orders.value) // đã filter từ API
const totalPages = computed(() => Math.ceil((orders.value.meta?.total || orders.value.length) / itemsPerPage.value))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, orders.value.meta?.total || orders.value.length))
const paginatedOrders = computed(() => orders.value.data || orders.value)
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
  const found = statusEnums.value.find(s => s.value === status)
  return found ? found.label : status
}

const getStatusClass = (status) => {
  // Có thể lấy từ backend hoặc định nghĩa mapping ở đây
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

const saveStatus = async () => {
  if (selectedOrder.value && newStatus.value) {
    try {
      await axios.put(`/api/orders/${selectedOrder.value.id}/status`, {
        status: newStatus.value,
        note: statusNote.value
      })
      await fetchOrders()
    } catch (e) {
      console.error('Lỗi cập nhật trạng thái:', e)
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
