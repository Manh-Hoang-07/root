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
    <div v-if="loading" class="mb-6">
      <SkeletonLoader type="table" :rows="5" :columns="7" />
    </div>
    
    <div v-else class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên sản phẩm</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá gốc</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá KM</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thương hiệu</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in processedProducts" :key="`product-${product.id}`">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.formattedPrice }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <span v-if="product.sale_price" class="text-red-600 font-medium">{{ product.formattedSalePrice }}</span>
              <span v-else class="text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.brandName }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="product.statusClass"
              >
                {{ product.statusLabel }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <div class="flex space-x-2">
                <button 
                  @click="$router.push(`/admin/products/${product.id}/edit-page`)" 
                  class="text-indigo-600 hover:text-indigo-900"
                  title="Chỉnh sửa chi tiết"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button 
                  @click="$router.push(`/admin/products/${product.id}/variants`)" 
                  class="text-blue-600 hover:text-blue-900"
                  title="Quản lý biến thể"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                  </svg>
                </button>
                <button 
                  @click="$router.push(`/admin/products/${product.id}/images`)" 
                  class="text-green-600 hover:text-green-900"
                  title="Quản lý ảnh"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                  </svg>
                </button>
                <Actions 
                  :item="product"
                  @edit="openEditModal"
                  @delete="confirmDelete"
                />
              </div>
            </td>
          </tr>
          <tr v-if="products.length === 0">
            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
              Không có dữ liệu
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
import { ref, onMounted, computed, defineAsyncComponent } from 'vue'
import ConfirmModal from '@/components/Core/ConfirmModal.vue'
import Actions from '@/components/Core/Actions.vue'
import SkeletonLoader from '@/components/Core/SkeletonLoader.vue'
import endpoints from '@/api/endpoints'
import { useDataTable } from '@/composables/useDataTable'
import { useToast } from '@/composables/useToast'
import { formatDate } from '@/utils/formatDate'

// Lazy load all components
const ProductFilter = defineAsyncComponent(() => import('./filter.vue'))
const CreateProduct = defineAsyncComponent(() => import('./create.vue'))
const EditProduct = defineAsyncComponent(() => import('./edit.vue'))

// Use composables
const { showSuccess, showError } = useToast()
const {
  items: products,
  loading,
  pagination,
  filters,
  fetchData,
  updateFilters,
  deleteItem
} = useDataTable(endpoints.products.list, {
  defaultFilters: {
    search: '',
    status: '',
    sort_by: 'created_at_desc'
  },
  cacheEnabled: true,
  debounceTime: 300
})

const selectedProduct = ref(null)

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

// Computed properties for processed data
const processedProducts = computed(() => {
  return products.value.map(product => ({
    ...product,
    formattedPrice: formatCurrency(product.price),
    formattedSalePrice: product.sale_price ? formatCurrency(product.sale_price) : '-',
    statusLabel: getStatusLabel(product.status),
    statusClass: product.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800',
    brandName: product.brand?.name || 'N/A'
  }))
})

// Fetch data
onMounted(async () => {
  await fetchData()
})

// Filter handlers
function handleFilterChange(newFilters) {
  updateFilters(newFilters)
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
  await fetchData()
  closeCreateModal()
  showSuccess('Tạo sản phẩm thành công!')
}
async function handleProductUpdated() {
  await fetchData()
  closeEditModal()
  showSuccess('Cập nhật sản phẩm thành công!')
}
async function deleteProduct() {
  try {
    await deleteItem(selectedProduct.value.id)
    closeDeleteModal()
    showSuccess('Xóa sản phẩm thành công!')
  } catch (error) {
    showError('Có lỗi xảy ra khi xóa sản phẩm')
  }
}

function changePage(url) {
  if (!url) return
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  fetchData({ page })
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
