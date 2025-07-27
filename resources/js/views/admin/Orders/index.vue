<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý đơn hàng</h1>
      <button 
        @click="openCreateModal" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm đơn hàng mới
      </button>
    </div>

    <!-- Bộ lọc -->
    <OrderFilter
      :initial-filters="filters"
      @update:filters="handleFilterChange"
    />

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày đặt</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày cập nhật</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in orders" :key="order.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ order.customer_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(order.total_amount) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="order.status === 'completed' ? 'bg-green-100 text-green-800' : order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'"
              >
                {{ getStatusLabel(order.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(order.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(order.updated_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button 
                @click="openEditModal(order)" 
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Sửa
              </button>
              <button 
                @click="confirmDelete(order)" 
                class="text-red-600 hover:text-red-900"
              >
                Xóa
              </button>
            </td>
          </tr>
          <tr v-if="orders.length === 0">
            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
              {{ loading ? 'Đang tải dữ liệu...' : 'Không có dữ liệu' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Phân trang -->
    <div v-if="orders.length > 0" class="mt-4 flex justify-between items-center">
      <div class="text-sm text-gray-700">
        Hiển thị {{ pagination.from || 0 }} đến {{ pagination.to || 0 }} trên tổng số {{ pagination.total || 0 }} bản ghi
      </div>
      <div class="flex space-x-1">
        <button 
          v-for="page in pagination.links" 
          :key="page.label"
          @click="changePage(page.url)"
          :disabled="!page.url"
          :class="[
            'px-3 py-1 border rounded',
            page.active 
              ? 'bg-blue-600 text-white' 
              : 'bg-white text-gray-700 hover:bg-gray-50',
            !page.url && 'opacity-50 cursor-not-allowed'
          ]"
          v-html="page.label"
        ></button>
      </div>
    </div>

    <!-- Modal thêm mới -->
    <CreateOrder
      v-if="showCreateModal"
      :show="showCreateModal"
      :on-close="closeCreateModal"
      @created="handleOrderCreated"
    />

    <!-- Modal chỉnh sửa -->
    <EditOrder
      v-if="showEditModal"
      :show="showEditModal"
      :order="selectedOrder"
      :on-close="closeEditModal"
      @updated="handleOrderUpdated"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa đơn hàng #${selectedOrder?.id || ''}?`"
      :on-close="closeDeleteModal"
      @confirm="deleteOrder"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import CreateOrder from './create.vue'
import EditOrder from './edit.vue'
import OrderFilter from './filter.vue'
import ConfirmModal from '@/components/Core/ConfirmModal.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'
import { formatDate } from '@/utils/formatDate'

// State
const orders = ref([])
const selectedOrder = ref(null)
const pagination = reactive({
  current_page: 1,
  from: 0,
  to: 0,
  total: 0,
  per_page: 10,
  links: []
})
const filters = reactive({
  search: '',
  status: '',
  sort_by: 'created_at_desc'
})
const loading = ref(false)

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

// Fetch data
onMounted(async () => {
  await fetchOrders()
})

async function fetchOrders(page = 1) {
  loading.value = true
  try {
    const response = await axios.get(endpoints.orders.list, {
      params: { 
        page,
        search: filters.search,
        status: filters.status,
        sort_by: filters.sort_by
      }
    })
    orders.value = response.data.data
    // Update pagination
    const meta = response.data.meta
    if (meta) {
      pagination.current_page = meta.current_page
      pagination.from = meta.from
      pagination.to = meta.to
      pagination.total = meta.total
      pagination.per_page = meta.per_page
      pagination.links = meta.links
    }
  } catch (error) {
    console.error('Error fetching orders:', error)
  } finally {
    loading.value = false
  }
}

// Filter handlers
function handleFilterChange(newFilters) {
  Object.assign(filters, newFilters)
  fetchOrders(1)
}

// Modal handlers
function openCreateModal() {
  showCreateModal.value = true
}
function closeCreateModal() {
  showCreateModal.value = false
}
function openEditModal(order) {
  selectedOrder.value = order
  showEditModal.value = true
}
function closeEditModal() {
  showEditModal.value = false
  selectedOrder.value = null
}
function confirmDelete(order) {
  selectedOrder.value = order
  showDeleteModal.value = true
}
function closeDeleteModal() {
  showDeleteModal.value = false
  selectedOrder.value = null
}
// Action handlers
async function handleOrderCreated() {
  await fetchOrders()
  closeCreateModal()
}
async function handleOrderUpdated() {
  await fetchOrders()
  closeEditModal()
}
async function deleteOrder() {
  try {
    await axios.delete(endpoints.orders.delete(selectedOrder.value.id))
    await fetchOrders()
    closeDeleteModal()
  } catch (error) {
    console.error('Error deleting order:', error)
  }
}
function changePage(url) {
  if (!url) return
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  fetchOrders(page)
}
// Helper functions
function formatCurrency(amount) {
  if (!amount) return '0'
  return Number(amount).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })
}
function getStatusLabel(status) {
  if (status === 'completed') return 'Hoàn thành'
  if (status === 'pending') return 'Chờ xử lý'
  if (status === 'cancelled') return 'Đã huỷ'
  return status
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
