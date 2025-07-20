<template>
  <DataTable :is-all-selected="isAllSelected" @toggle-select-all="toggleSelectAll">
    <!-- Action bar -->
    <template #actions>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-3 py-1.5 rounded font-medium hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow flex items-center space-x-1.5 text-sm"
      >
        <span>Thêm đơn hàng</span>
      </button>
      <button
        @click="deleteSelected"
        :disabled="!selected.length"
        class="ml-1 px-3 py-1.5 rounded bg-red-500 text-white font-medium disabled:opacity-50 text-sm"
      >
        Xóa đã chọn
      </button>
    </template>
    <!-- Filter bar -->
    <template #filter>
      <Filter :filters="filters" @update:filters="onUpdateFilters" @clear="clearFilters" />
    </template>
    <!-- Table head -->
    <template #thead>
      <th class="px-4 py-3 whitespace-nowrap">ID</th>
      <th class="px-4 py-3 whitespace-nowrap">Khách hàng</th>
      <th class="px-4 py-3 whitespace-nowrap">Tổng tiền</th>
      <th class="px-4 py-3 whitespace-nowrap">Trạng thái</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày đặt</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày cập nhật</th>
    </template>
    <!-- Table body -->
    <template #tbody>
      <tr v-if="orders.length === 0">
        <td colspan="11" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
      </tr>
      <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
        <td class="w-6 px-1 py-1 text-center align-middle">
          <input type="checkbox" :checked="selected.includes(order.id)" @change="toggleSelect(order.id)" class="accent-indigo-500 w-5 h-5 rounded border-gray-300 focus:ring-indigo-500" />
        </td>
        <td class="px-4 py-3 whitespace-nowrap">{{ order.id }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ order.customer_name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatCurrency(order.total_amount) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">
          <span :class="order.status === 'completed' ? 'bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs' : 'bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs'">
            {{ order.status_label }}
          </span>
        </td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(order.created_at) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(order.updated_at) }}</td>
        <!-- Thao tác -->
        <td class="text-center">
          <button @click="editOrder(order)" class="p-2 rounded-full hover:bg-indigo-100 transition" title="Sửa">
            <PencilIcon class="w-5 h-5 text-indigo-600" />
          </button>
          <button @click="deleteOrder(order)" class="p-2 rounded-full hover:bg-red-100 transition" title="Xóa">
            <TrashIcon class="w-5 h-5 text-red-500" />
          </button>
        </td>
      </tr>
    </template>
    <!-- Pagination -->
    <template #pagination>
      <Pagination
        :current-page="pagination.currentPage"
        :total-pages="pagination.totalPages"
        :page-size="pagination.itemsPerPage"
        :total-items="pagination.totalItems"
        :loading="loading"
        @page-change="onPageChange"
      />
    </template>
  </DataTable>

  <!-- Modal -->
  <Create v-if="showAddModal" :show="showAddModal" :onClose="closeModal" @created="fetchOrders" />
  <Edit v-if="showEditModal" :show="showEditModal" :order="editingOrder" :onClose="closeModal" @updated="fetchOrders" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
import DataTable from '@/components/DataTable.vue'
import Pagination from '@/components/Pagination.vue'
import Filter from './filter.vue'
import Create from './create.vue'
import Edit from './edit.vue'
import api from '@/api/apiClient'
import endpoints from '@/api/endpoints'
import useTableSelection from '@/composables/useTableSelection'
import usePagination from '@/composables/usePagination'
import useSyncQueryPagination from '@/composables/useSyncQueryPagination'
import { formatDate } from '@/utils/formatDate'

const orders = ref([])
const filters = ref({ search: '' })
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingOrder = ref(null)
const loading = ref(false)
const pagination = ref({
  currentPage: 1,
  totalPages: 0,
  totalItems: 0,
  itemsPerPage: 10
})
const { selected, isAllSelected, toggleSelectAll, toggleSelect } = useTableSelection(orders)

const fetchOrders = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.currentPage, per_page: pagination.value.itemsPerPage }
    const res = await api.get(endpoints.orders.list, { params })
    orders.value = res.data.data || []
    pagination.value.totalItems = res.data.meta?.total || 0
    pagination.value.totalPages = res.data.meta?.last_page || 1
    pagination.value.currentPage = res.data.meta?.current_page || 1
  } catch (e) {
    orders.value = []
    pagination.value.totalItems = 0
    pagination.value.totalPages = 1
    pagination.value.currentPage = 1
  } finally {
    loading.value = false
  }
}

const { onPageChange, onUpdateFilters } = useSyncQueryPagination(filters, pagination, fetchOrders, ['search'])

const clearFilters = () => {
  filters.value = { search: '' }
  fetchOrders()
}

const openAddModal = () => {
  showAddModal.value = true
}
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingOrder.value = null
}
const editOrder = (order) => {
  editingOrder.value = order
  showEditModal.value = true
}
const deleteOrder = async (order) => {
  if (confirm(`Bạn có chắc chắn muốn xóa đơn hàng "${order.id}"?`)) {
    try {
      await api.delete(endpoints.orders.delete(order.id))
      fetchOrders()
    } catch (e) {}
  }
}
const deleteSelected = async () => {
  if (!selected.length) return
  if (confirm('Bạn có chắc chắn muốn xóa các đơn hàng đã chọn?')) {
    try {
      await Promise.all(selected.map(id => api.delete(endpoints.orders.delete(id))))
      fetchOrders()
    } catch (e) {}
  }
}
function formatCurrency(val) {
  if (val == null) return ''
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}
onMounted(fetchOrders)
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
