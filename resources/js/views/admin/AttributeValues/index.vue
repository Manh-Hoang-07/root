<template>
  <DataTable :is-all-selected="isAllSelected" @toggle-select-all="toggleSelectAll">
    <!-- Action bar -->
    <template #actions>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-3 py-1.5 rounded font-medium hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow flex items-center space-x-1.5 text-sm"
      >
        <span>Thêm giá trị</span>
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
      <th class="px-4 py-3 whitespace-nowrap">Thuộc tính</th>
      <th class="px-4 py-3 whitespace-nowrap">Giá trị</th>
      <th class="px-4 py-3 whitespace-nowrap">Hiển thị</th>
      <th class="px-4 py-3 whitespace-nowrap">Thứ tự</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày tạo</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày cập nhật</th>
    </template>
    <!-- Table body -->
    <template #tbody>
      <tr v-if="attributeValues.length === 0">
        <td colspan="10" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
      </tr>
      <tr v-for="val in attributeValues" :key="val.id" class="hover:bg-gray-50">
        <td class="px-4 py-3 whitespace-nowrap">{{ val.id }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ val.attribute_name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ val.value }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ val.display_value }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ val.sort_order }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(val.created_at) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(val.updated_at) }}</td>
        <!-- Thao tác -->
        <td class="text-center">
          <button @click="editAttributeValue(val)" class="p-2 rounded-full hover:bg-indigo-100 transition" title="Sửa">
            <PencilIcon class="w-5 h-5 text-indigo-600" />
          </button>
          <button @click="deleteAttributeValue(val)" class="p-2 rounded-full hover:bg-red-100 transition" title="Xóa">
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
  <Create v-if="showAddModal" :show="showAddModal" :onClose="closeModal" @created="fetchAttributeValues" />
  <Edit v-if="showEditModal" :show="showEditModal" :attribute-value="editingAttributeValue" :onClose="closeModal" @updated="fetchAttributeValues" />
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

const attributeValues = ref([])
const filters = ref({ search: '' })
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingAttributeValue = ref(null)
const loading = ref(false)
const pagination = ref({
  currentPage: 1,
  totalPages: 0,
  totalItems: 0,
  itemsPerPage: 10
})
const { selected, isAllSelected, toggleSelectAll, toggleSelect } = useTableSelection(attributeValues)

const fetchAttributeValues = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.currentPage, per_page: pagination.value.itemsPerPage }
    const res = await api.get(endpoints.attributeValues.list, { params })
    attributeValues.value = res.data.data || []
    pagination.value.totalItems = res.data.meta?.total || 0
    pagination.value.totalPages = res.data.meta?.last_page || 1
    pagination.value.currentPage = res.data.meta?.current_page || 1
  } catch (e) {
    attributeValues.value = []
    pagination.value.totalItems = 0
    pagination.value.totalPages = 1
    pagination.value.currentPage = 1
  } finally {
    loading.value = false
  }
}

const { onPageChange, onUpdateFilters } = useSyncQueryPagination(filters, pagination, fetchAttributeValues, ['search'])

const clearFilters = () => {
  filters.value = { search: '' }
  fetchAttributeValues()
}

const openAddModal = () => {
  showAddModal.value = true
}
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingAttributeValue.value = null
}
const editAttributeValue = (val) => {
  editingAttributeValue.value = val
  showEditModal.value = true
}
const deleteAttributeValue = async (val) => {
  if (confirm(`Bạn có chắc chắn muốn xóa giá trị "${val.value}"?`)) {
    try {
      await api.delete(endpoints.attributeValues.delete(val.id))
      fetchAttributeValues()
    } catch (e) {}
  }
}
const deleteSelected = async () => {
  if (!selected.length) return
  if (confirm('Bạn có chắc chắn muốn xóa các giá trị đã chọn?')) {
    try {
      await Promise.all(selected.map(id => api.delete(endpoints.attributeValues.delete(id))))
      fetchAttributeValues()
    } catch (e) {}
  }
}
onMounted(fetchAttributeValues)
</script>

<style scoped>
</style> 