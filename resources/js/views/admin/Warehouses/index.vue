<template>
  <DataTable :is-all-selected="isAllSelected" @toggle-select-all="toggleSelectAll">
    <!-- Action bar -->
    <template #actions>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-3 py-1.5 rounded font-medium hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow flex items-center space-x-1.5 text-sm"
      >
        <span>Thêm kho hàng</span>
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
      <th class="px-4 py-3 whitespace-nowrap">Tên kho</th>
      <th class="px-4 py-3 whitespace-nowrap hidden md:table-cell">Địa chỉ</th>
      <th class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">Thành phố</th>
      <th class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">Tỉnh/Thành</th>
      <th class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">Số điện thoại</th>
      <th class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">Email</th>
      <th class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">Trạng thái</th>
      <th class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">Ngày tạo</th>
      <th class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">Ngày cập nhật</th>
    </template>
    <!-- Table body -->
    <template #tbody>
      <tr v-if="warehouses.length === 0">
        <td colspan="11" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
      </tr>
      <tr v-for="warehouse in warehouses" :key="warehouse.id" class="hover:bg-gray-50">
        <td class="w-6 px-1 py-1 text-center align-middle">
          <input type="checkbox" :checked="selected.includes(warehouse.id)" @change="toggleSelect(warehouse.id)" class="accent-indigo-500 w-5 h-5 rounded border-gray-300 focus:ring-indigo-500" />
        </td>
        <td class="px-4 py-3 whitespace-nowrap">{{ warehouse.name }}</td>
        <td class="px-4 py-3 whitespace-nowrap hidden md:table-cell">{{ warehouse.address }}</td>
        <td class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">{{ warehouse.city }}</td>
        <td class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">{{ warehouse.province }}</td>
        <td class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">{{ warehouse.phone }}</td>
        <td class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">{{ warehouse.email }}</td>
        <td class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">
          <span :class="warehouse.status === 'active' ? 'bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs' : 'bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs'">
            {{ warehouse.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
          </span>
        </td>
        <td class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">{{ formatDate(warehouse.created_at) }}</td>
        <td class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">{{ formatDate(warehouse.updated_at) }}</td>
        <!-- Thao tác -->
        <td class="text-center">
          <button @click="editWarehouse(warehouse)" class="p-2 rounded-full hover:bg-indigo-100 transition" title="Sửa">
            <PencilIcon class="w-5 h-5 text-indigo-600" />
          </button>
          <button @click="deleteWarehouse(warehouse)" class="p-2 rounded-full hover:bg-red-100 transition" title="Xóa">
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
  <Create v-if="showAddModal" :show="showAddModal" :onClose="closeModal" @created="fetchWarehouses" />
  <Edit v-if="showEditModal" :show="showEditModal" :warehouse="editingWarehouse" :onClose="closeModal" @updated="fetchWarehouses" />
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

const warehouses = ref([])
const filters = ref({ search: '' })
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingWarehouse = ref(null)
const loading = ref(false)
const pagination = ref({
  currentPage: 1,
  totalPages: 0,
  totalItems: 0,
  itemsPerPage: 10
})
const { selected, isAllSelected, toggleSelectAll, toggleSelect } = useTableSelection(warehouses)

const fetchWarehouses = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.currentPage, per_page: pagination.value.itemsPerPage }
    const res = await api.get(endpoints.warehouses.list, { params })
    warehouses.value = res.data.data || []
    pagination.value.totalItems = res.data.meta?.total || 0
    pagination.value.totalPages = res.data.meta?.last_page || 1
    pagination.value.currentPage = res.data.meta?.current_page || 1
  } catch (e) {
    warehouses.value = []
    pagination.value.totalItems = 0
    pagination.value.totalPages = 1
    pagination.value.currentPage = 1
  } finally {
    loading.value = false
  }
}

const { onPageChange, onUpdateFilters } = useSyncQueryPagination(filters, pagination, fetchWarehouses, ['search'])

const clearFilters = () => {
  filters.value = { search: '' }
  fetchWarehouses()
}

const openAddModal = () => {
  showAddModal.value = true
}
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingWarehouse.value = null
}
const editWarehouse = (warehouse) => {
  editingWarehouse.value = warehouse
  showEditModal.value = true
}
const deleteWarehouse = async (warehouse) => {
  if (confirm(`Bạn có chắc chắn muốn xóa kho "${warehouse.name}"?`)) {
    try {
      await api.delete(endpoints.warehouses.delete(warehouse.id))
      fetchWarehouses()
    } catch (e) {}
  }
}
const deleteSelected = async () => {
  if (!selected.length) return
  if (confirm('Bạn có chắc chắn muốn xóa các kho đã chọn?')) {
    try {
      await Promise.all(selected.map(id => api.delete(endpoints.warehouses.delete(id))))
      fetchWarehouses()
    } catch (e) {}
  }
}
onMounted(fetchWarehouses)
</script>

<style scoped>
/* Custom styles */
</style> 