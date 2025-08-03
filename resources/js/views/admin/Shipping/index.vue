<template>
  <DataTable :is-all-selected="isAllSelected" @toggle-select-all="toggleSelectAll" class="w-full min-w-full table-fixed">
    <!-- Action bar -->
    <template #actions>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-3 py-1.5 rounded font-medium hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow flex items-center space-x-1.5 text-sm"
      >
        <span>Thêm khu vực vận chuyển</span>
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
      <th class="px-4 py-3 whitespace-nowrap">Tên khu vực</th>
      <th class="px-4 py-3 whitespace-nowrap">Thời gian (ngày)</th>
      <th class="px-4 py-3 whitespace-nowrap">Tỉnh/Thành</th>
      <th class="px-4 py-3 whitespace-nowrap">Trạng thái</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày tạo</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày cập nhật</th>
    </template>
    <!-- Table body -->
    <template #tbody>
      <tr v-if="shippingZones.length === 0">
        <td colspan="12" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
      </tr>
      <tr v-for="zone in shippingZones" :key="zone.id" class="hover:bg-gray-50">
        <td class="px-4 py-3 whitespace-nowrap">{{ zone.id }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ zone.name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ zone.estimated_days }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ zone.provinces?.join(', ') }}</td>
        <td class="px-4 py-3 whitespace-nowrap">
          <span :class="getStatusClass(zone.status)">
            {{ getStatusLabel(zone.status) }}
          </span>
        </td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(zone.created_at) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(zone.updated_at) }}</td>
        <!-- Thao tác -->
        <td class="text-center">
          <button @click="editShippingZone(zone)" class="p-2 rounded-full hover:bg-indigo-100 transition" title="Sửa">
            <PencilIcon class="w-5 h-5 text-indigo-600" />
          </button>
          <button @click="deleteShippingZone(zone)" class="p-2 rounded-full hover:bg-red-100 transition" title="Xóa">
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
  <Create v-if="showAddModal" :show="showAddModal" :onClose="closeModal" @created="fetchShippingZones" />
  <Edit v-if="showEditModal" :show="showEditModal" :shipping-zone="editingShippingZone" :onClose="closeModal" @updated="fetchShippingZones" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
import DataTable from '@/components/Core/DataTable.vue'
import Pagination from '@/components/Core/Pagination.vue'
import Filter from './filter.vue'
import Create from './create.vue'
import Edit from './edit.vue'
import { getEnumLabel } from '@/constants/enums'
import api from '@/api/apiClient'
import endpoints from '@/api/endpoints'
import useTableSelection from '@/composables/useTableSelection'
import usePagination from '@/composables/usePagination'
import useSyncQueryPagination from '@/composables/useSyncQueryPagination'
import { formatDate } from '@/utils/formatDate'

const shippingZones = ref([])
const filters = ref({ search: '' })
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingShippingZone = ref(null)
const loading = ref(false)
const pagination = ref({
  currentPage: 1,
  totalPages: 0,
  totalItems: 0,
  itemsPerPage: 10
})
const { selected, isAllSelected, toggleSelectAll, toggleSelect } = useTableSelection(shippingZones)

const fetchShippingZones = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.currentPage, per_page: pagination.value.itemsPerPage }
    const res = await api.get(endpoints.shipping.list, { params })
    shippingZones.value = res.data.data || []
    pagination.value.totalItems = res.data.meta?.total || 0
    pagination.value.totalPages = res.data.meta?.last_page || 1
    pagination.value.currentPage = res.data.meta?.current_page || 1
  } catch (e) {
    shippingZones.value = []
    pagination.value.totalItems = 0
    pagination.value.totalPages = 1
    pagination.value.currentPage = 1
  } finally {
    loading.value = false
  }
}

const { onPageChange, onUpdateFilters } = useSyncQueryPagination(filters, pagination, fetchShippingZones, ['search'])

const clearFilters = () => {
  filters.value = { search: '' }
  fetchShippingZones()
}

const openAddModal = () => {
  showAddModal.value = true
}
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingShippingZone.value = null
}
const editShippingZone = (zone) => {
  editingShippingZone.value = zone
  showEditModal.value = true
}
const deleteShippingZone = async (zone) => {
  if (confirm(`Bạn có chắc chắn muốn xóa khu vực "${zone.name}"?`)) {
    try {
      await api.delete(endpoints.shipping.delete(zone.id))
      fetchShippingZones()
    } catch (e) {}
  }
}
const deleteSelected = async () => {
  if (!selected.length) return
  if (confirm('Bạn có chắc chắn muốn xóa các khu vực đã chọn?')) {
    try {
      await Promise.all(selected.map(id => api.delete(endpoints.shipping.delete(id))))
      fetchShippingZones()
    } catch (e) {}
  }
}
// Bỏ onMounted vì useSyncQueryPagination đã xử lý

function formatPrice(val) {
  if (val == null) return ''
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}

// Status helper functions
function getStatusLabel(status) {
  return getEnumLabel('basic_status', status) || status || 'Không xác định'
}

function getStatusClass(status) {
  if (status === 'active') return 'bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs'
  if (status === 'inactive') return 'bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs'
  return 'bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs'
}
</script>

<style scoped>
/* Custom styles */
</style> 
