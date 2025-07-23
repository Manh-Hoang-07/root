<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý sản phẩm</h1>
      <button 
        @click="openCreateModal" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm sản phẩm mới
      </button>
    </div>

    <!-- Bộ lọc -->
    <ProductFilter
      :initial-filters="filters"
      @update:filters="handleFilterChange"
    />

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên sản phẩm</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Danh mục</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thương hiệu</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày cập nhật</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in products" :key="product.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ product.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.sku }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.category_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.brand_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(product.price) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="product.status === 'active' ? 'bg-green-100 text-green-800' : product.status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700'"
              >
                {{ getStatusLabel(product.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(product.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(product.updated_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button 
                @click="openEditModal(product)" 
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Sửa
              </button>
              <button 
                @click="confirmDelete(product)" 
                class="text-red-600 hover:text-red-900"
              >
                Xóa
              </button>
            </td>
          </tr>
          <tr v-if="products.length === 0">
            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
              {{ loading ? 'Đang tải dữ liệu...' : 'Không có dữ liệu' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Phân trang -->
    <div v-if="products.length > 0" class="mt-4 flex justify-between items-center">
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
    <CreateProduct
      v-if="showCreateModal"
      :show="showCreateModal"
      :on-close="closeCreateModal"
      @created="handleProductCreated"
    />

    <!-- Modal chỉnh sửa -->
    <EditProduct
      v-if="showEditModal"
      :show="showEditModal"
      :product="selectedProduct"
      :on-close="closeEditModal"
      @updated="handleProductUpdated"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa sản phẩm ${selectedProduct?.name || ''}?`"
      :on-close="closeDeleteModal"
      @confirm="deleteProduct"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import CreateProduct from './create.vue'
import EditProduct from './edit.vue'
import ProductFilter from './filter.vue'
import ConfirmModal from '@/components/ConfirmModal.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'
import { formatDate } from '@/utils/formatDate'

// State
const products = ref([])
const selectedProduct = ref(null)
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
  await fetchProducts()
})

async function fetchProducts(page = 1) {
  loading.value = true
  try {
    const response = await axios.get(endpoints.products.list, {
      params: { 
        page,
        search: filters.search,
        status: filters.status,
        sort_by: filters.sort_by
      }
    })
    products.value = response.data.data
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
    console.error('Error fetching products:', error)
  } finally {
    loading.value = false
  }
}

// Filter handlers
function handleFilterChange(newFilters) {
  Object.assign(filters, newFilters)
  fetchProducts(1)
}

// Modal handlers
function openCreateModal() {
  showCreateModal.value = true
}
function closeCreateModal() {
  showCreateModal.value = false
}
function openEditModal(product) {
  selectedProduct.value = product
  showEditModal.value = true
}
function closeEditModal() {
  showEditModal.value = false
  selectedProduct.value = null
}
function confirmDelete(product) {
  selectedProduct.value = product
  showDeleteModal.value = true
}
function closeDeleteModal() {
  showDeleteModal.value = false
  selectedProduct.value = null
}

// Action handlers
async function handleProductCreated() {
  await fetchProducts()
  closeCreateModal()
}
async function handleProductUpdated() {
  await fetchProducts()
  closeEditModal()
}
async function deleteProduct() {
  try {
    await axios.delete(endpoints.products.delete(selectedProduct.value.id))
    await fetchProducts()
    closeDeleteModal()
  } catch (error) {
    console.error('Error deleting product:', error)
  }
}

function changePage(url) {
  if (!url) return
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  fetchProducts(page)
}

// Helper functions
function formatCurrency(amount) {
  if (!amount) return '0'
  return Number(amount).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })
}
function getStatusLabel(status) {
  if (status === 'active') return 'Đang bán'
  if (status === 'inactive') return 'Ngừng bán'
  if (status === 'draft') return 'Bản nháp'
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

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 