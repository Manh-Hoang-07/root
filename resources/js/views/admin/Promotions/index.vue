<template>
  <DataTable :is-all-selected="isAllSelected" @toggle-select-all="toggleSelectAll">
    <!-- Action bar -->
    <template #actions>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-3 py-1.5 rounded font-medium hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow flex items-center space-x-1.5 text-sm"
      >
        <span>Thêm khuyến mãi</span>
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
      <th class="px-4 py-3 whitespace-nowrap">Tên</th>
      <th class="px-4 py-3 whitespace-nowrap">Mã</th>
      <th class="px-4 py-3 whitespace-nowrap">Loại</th>
      <th class="px-4 py-3 whitespace-nowrap">Giá trị</th>
      <th class="px-4 py-3 whitespace-nowrap">Trạng thái</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày bắt đầu</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày kết thúc</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày tạo</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày cập nhật</th>
    </template>
    <!-- Table body -->
    <template #tbody>
      <tr v-if="promotions.length === 0">
        <td colspan="12" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
      </tr>
      <tr v-for="promo in promotions" :key="promo.id" class="hover:bg-gray-50">
        <td class="px-4 py-3 whitespace-nowrap">{{ promo.id }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ promo.name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ promo.code }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ promo.type }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ promo.value }}</td>
        <td class="px-4 py-3 whitespace-nowrap">
          <span :class="promo.status === 'active' ? 'bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs' : 'bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs'">
            {{ promo.status === 'active' ? 'Hoạt động' : 'Ngừng hoạt động' }}
          </span>
        </td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(promo.start_date) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(promo.end_date) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(promo.created_at) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(promo.updated_at) }}</td>
        <!-- Thao tác -->
        <td class="text-center">
          <button @click="editPromotion(promo)" class="p-2 rounded-full hover:bg-indigo-100 transition" title="Sửa">
            <PencilIcon class="w-5 h-5 text-indigo-600" />
          </button>
          <button @click="deletePromotion(promo)" class="p-2 rounded-full hover:bg-red-100 transition" title="Xóa">
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
  <Create v-if="showAddModal" :show="showAddModal" :onClose="closeModal" @created="fetchPromotions" />
  <Edit v-if="showEditModal" :show="showEditModal" :promotion="editingPromotion" :onClose="closeModal" @updated="fetchPromotions" />
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

const promotions = ref([])
const filters = ref({ search: '' })
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingPromotion = ref(null)
const loading = ref(false)
const pagination = ref({
  currentPage: 1,
  totalPages: 0,
  totalItems: 0,
  itemsPerPage: 10
})
const { selected, isAllSelected, toggleSelectAll, toggleSelect } = useTableSelection(promotions)

const fetchPromotions = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.currentPage, per_page: pagination.value.itemsPerPage }
    const res = await api.get(endpoints.promotions.list, { params })
    promotions.value = res.data.data || []
    pagination.value.totalItems = res.data.meta?.total || 0
    pagination.value.totalPages = res.data.meta?.last_page || 1
    pagination.value.currentPage = res.data.meta?.current_page || 1
  } catch (e) {
    promotions.value = []
    pagination.value.totalItems = 0
    pagination.value.totalPages = 1
    pagination.value.currentPage = 1
  } finally {
    loading.value = false
  }
}

const { onPageChange, onUpdateFilters } = useSyncQueryPagination(filters, pagination, fetchPromotions, ['search'])

const clearFilters = () => {
  filters.value = { search: '' }
  fetchPromotions()
}

const openAddModal = () => {
  showAddModal.value = true
}
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingPromotion.value = null
}
const editPromotion = (promo) => {
  editingPromotion.value = promo
  showEditModal.value = true
}
const deletePromotion = async (promo) => {
  if (confirm(`Bạn có chắc chắn muốn xóa khuyến mãi "${promo.name}"?`)) {
    try {
      await api.delete(endpoints.promotions.delete(promo.id))
      fetchPromotions()
    } catch (e) {}
  }
}
const deleteSelected = async () => {
  if (!selected.length) return
  if (confirm('Bạn có chắc chắn muốn xóa các khuyến mãi đã chọn?')) {
    try {
      await Promise.all(selected.map(id => api.delete(endpoints.promotions.delete(id))))
      fetchPromotions()
    } catch (e) {}
  }
}
// Bỏ onMounted vì useSyncQueryPagination đã xử lý
</script>

<style scoped>
/* Add any specific styles here if needed */
</style> 