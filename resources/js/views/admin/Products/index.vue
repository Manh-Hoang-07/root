<template>
  <DataTable :is-all-selected="isAllSelected" @toggle-select-all="toggleSelectAll" class="w-full min-w-full table-fixed">
    <!-- Action bar -->
    <template #actions>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-3 py-1.5 rounded font-medium hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow flex items-center space-x-1.5 text-sm"
      >
        <span>Thêm sản phẩm</span>
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
      <th class="px-4 py-3 whitespace-nowrap">Tên sản phẩm</th>
      <th class="px-4 py-3 whitespace-nowrap">SKU</th>
      <th class="px-4 py-3 whitespace-nowrap">Danh mục</th>
      <th class="px-4 py-3 whitespace-nowrap">Thương hiệu</th>
      <th class="px-4 py-3 whitespace-nowrap">Giá</th>
      <th class="px-4 py-3 whitespace-nowrap">Trạng thái</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày tạo</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày cập nhật</th>
    </template>
    <!-- Table body -->
    <template #tbody>
      <tr v-if="products.length === 0">
        <td colspan="12" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
      </tr>
      <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50">
        <td class="px-4 py-3 whitespace-nowrap">{{ product.id }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ product.name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ product.sku }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ product.category_name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ product.brand_name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatCurrency(product.price) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">
          <span :class="product.status === 'active' ? 'bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs' : 'bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs'">
            {{ product.status_label }}
          </span>
        </td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(product.created_at) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(product.updated_at) }}</td>
        <!-- Thao tác -->
        <td class="text-center">
          <button @click="editProduct(product)" class="p-2 rounded-full hover:bg-indigo-100 transition" title="Sửa">
            <PencilIcon class="w-5 h-5 text-indigo-600" />
          </button>
          <button @click="deleteProduct(product)" class="p-2 rounded-full hover:bg-red-100 transition" title="Xóa">
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
  <Create v-if="showAddModal" :show="showAddModal" :onClose="closeModal" @created="fetchProducts" />
  <Edit v-if="showEditModal" :show="showEditModal" :product="editingProduct" :onClose="closeModal" @updated="fetchProducts" />
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

const products = ref([])
const filters = ref({ search: '' })
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingProduct = ref(null)
const loading = ref(false)
const pagination = ref({
  currentPage: 1,
  totalPages: 0,
  totalItems: 0,
  itemsPerPage: 10
})
const { selected, isAllSelected, toggleSelectAll, toggleSelect } = useTableSelection(products)

const fetchProducts = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.currentPage, per_page: pagination.value.itemsPerPage }
    const res = await api.get(endpoints.products.list, { params })
    products.value = res.data.data || []
    pagination.value.totalItems = res.data.meta?.total || 0
    pagination.value.totalPages = res.data.meta?.last_page || 1
    pagination.value.currentPage = res.data.meta?.current_page || 1
  } catch (e) {
    products.value = []
    pagination.value.totalItems = 0
    pagination.value.totalPages = 1
    pagination.value.currentPage = 1
  } finally {
    loading.value = false
  }
}

const { onPageChange, onUpdateFilters } = useSyncQueryPagination(filters, pagination, fetchProducts, ['search'])

const clearFilters = () => {
  filters.value = { search: '' }
  fetchProducts()
}

const openAddModal = () => {
  showAddModal.value = true
}
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingProduct.value = null
}
const editProduct = (product) => {
  editingProduct.value = product
  showEditModal.value = true
}
const deleteProduct = async (product) => {
  if (confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${product.name}"?`)) {
    try {
      await api.delete(endpoints.products.delete(product.id))
      fetchProducts()
    } catch (e) {}
  }
}
const deleteSelected = async () => {
  if (!selected.length) return
  if (confirm('Bạn có chắc chắn muốn xóa các sản phẩm đã chọn?')) {
    try {
      await Promise.all(selected.map(id => api.delete(endpoints.products.delete(id))))
      fetchProducts()
    } catch (e) {}
  }
}
function formatCurrency(val) {
  if (val == null) return ''
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}
onMounted(fetchProducts)
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

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 