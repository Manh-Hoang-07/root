<template>
  <DataTable :is-all-selected="isAllSelected" @toggle-select-all="toggleSelectAll">
    <!-- Action bar -->
    <template #actions>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-3 py-1.5 rounded font-medium hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow flex items-center space-x-1.5 text-sm"
      >
        <span>Thêm danh mục</span>
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
      <th class="px-4 py-3 whitespace-nowrap">Danh mục cha</th>
      <th class="px-4 py-3 whitespace-nowrap">Mô tả</th>
      <th class="px-4 py-3 whitespace-nowrap">Ảnh</th>
      <th class="px-4 py-3 whitespace-nowrap">Trạng thái</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày tạo</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày cập nhật</th>
    </template>
    <!-- Table body -->
    <template #tbody>
      <tr v-if="categories.length === 0">
        <td colspan="10" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
      </tr>
      <tr v-for="cat in categories" :key="cat.id" class="hover:bg-gray-50">
        <td class="w-6 px-1 py-1 text-center align-middle">
          <input type="checkbox" :checked="selected.includes(cat.id)" @change="toggleSelect(cat.id)" class="accent-indigo-500 w-5 h-5 rounded border-gray-300 focus:ring-indigo-500" />
        </td>
        <td class="px-4 py-3 whitespace-nowrap">{{ cat.id }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ cat.name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ cat.parent_name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ cat.description }}</td>
        <td class="px-4 py-3 whitespace-nowrap">
          <img v-if="cat.image" :src="getImageUrl(cat.image)" alt="ảnh" class="w-10 h-10 rounded object-contain bg-gray-50 border" />
        </td>
        <td class="px-4 py-3 whitespace-nowrap">
          <span :class="cat.status === 'active' ? 'bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs' : 'bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs'">
            {{ cat.status === 'active' ? 'Hoạt động' : 'Ngừng hoạt động' }}
          </span>
        </td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(cat.created_at) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(cat.updated_at) }}</td>
        <!-- Thao tác -->
        <td class="text-center">
          <button @click="editCategory(cat)" class="p-2 rounded-full hover:bg-indigo-100 transition" title="Sửa">
            <PencilIcon class="w-5 h-5 text-indigo-600" />
          </button>
          <button @click="deleteCategory(cat)" class="p-2 rounded-full hover:bg-red-100 transition" title="Xóa">
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
  <Create v-if="showAddModal" :show="showAddModal" :onClose="closeModal" @created="fetchCategories" />
  <Edit v-if="showEditModal" :show="showEditModal" :category="editingCategory" :onClose="closeModal" @updated="fetchCategories" />
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

function getImageUrl(url) {
  if (!url) return ''
  if (url.startsWith('http')) return url
  if (url.startsWith('/storage/')) return url
  return '/storage/' + url.replace(/^\/+/,'')
}

const categories = ref([])
const filters = ref({ search: '' })
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingCategory = ref(null)
const loading = ref(false)
const pagination = ref({
  currentPage: 1,
  totalPages: 0,
  totalItems: 0,
  itemsPerPage: 10
})
const { selected, isAllSelected, toggleSelectAll, toggleSelect } = useTableSelection(categories)

const fetchCategories = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.currentPage, per_page: pagination.value.itemsPerPage }
    const res = await api.get(endpoints.categories.list, { params })
    categories.value = res.data.data || []
    pagination.value.totalItems = res.data.meta?.total || 0
    pagination.value.totalPages = res.data.meta?.last_page || 1
    pagination.value.currentPage = res.data.meta?.current_page || 1
  } catch (e) {
    categories.value = []
    pagination.value.totalItems = 0
    pagination.value.totalPages = 1
    pagination.value.currentPage = 1
  } finally {
    loading.value = false
  }
}

const { onPageChange, onUpdateFilters } = useSyncQueryPagination(filters, pagination, fetchCategories, ['search'])

const clearFilters = () => {
  filters.value = { search: '' }
  fetchCategories()
}

const openAddModal = () => {
  showAddModal.value = true
}
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingCategory.value = null
}
const editCategory = (cat) => {
  editingCategory.value = cat
  showEditModal.value = true
}
const deleteCategory = async (cat) => {
  if (confirm(`Bạn có chắc chắn muốn xóa danh mục "${cat.name}"?`)) {
    try {
      await api.delete(endpoints.categories.delete(cat.id))
      fetchCategories()
    } catch (e) {}
  }
}
const deleteSelected = async () => {
  if (!selected.length) return
  if (confirm('Bạn có chắc chắn muốn xóa các danh mục đã chọn?')) {
    try {
      await Promise.all(selected.map(id => api.delete(endpoints.categories.delete(id))))
      fetchCategories()
    } catch (e) {}
  }
}
onMounted(fetchCategories)
</script>

<style scoped>
</style> 