<template>
  <div class="container mx-auto p-4">
    <AdminTable
      title="Quản lý sản phẩm"
      :items="items"
      :loading="loading"
      :pagination="pagination"
      :selected-items="selectedItems"
      add-button-text="Thêm sản phẩm mới"
      @add="openCreateModal"
      @edit="openEditModal"
      @delete="openDeleteModal"
      @page-change="changePage"
      @toggle-all="toggleSelectAll"
      @toggle-item="toggleSelectItem"
    >
      <!-- Table header -->
      <template #header>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hình ảnh</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên sản phẩm</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Danh mục</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thương hiệu</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
      </template>
      
      <!-- Table row -->
      <template #row="{ item }">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.id }}</td>
        <td class="px-6 py-4 whitespace-nowrap">
          <img v-if="item.thumbnail" :src="item.thumbnail" alt="Thumbnail" class="h-10 w-10 object-contain" />
          <span v-else class="text-gray-400">Không có ảnh</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.name }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.category_name }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.brand_name }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.price) }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <span 
            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
            :class="getStatusClass(item.status)"
          >
            {{ getStatusName(item.status) }}
          </span>
        </td>
      </template>
      
      <!-- Filters -->
      <template #filters>
        <div class="flex flex-wrap gap-4">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
            <input 
              v-model="filters.search" 
              type="text" 
              placeholder="Tìm theo tên sản phẩm" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              @keyup.enter="applyFilters"
            />
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
            <select 
              v-model="filters.category_id" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">Tất cả danh mục</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Thương hiệu</label>
            <select 
              v-model="filters.brand_id" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">Tất cả thương hiệu</option>
              <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                {{ brand.name }}
              </option>
            </select>
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select 
              v-model="filters.status" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">Tất cả trạng thái</option>
              <option value="1">Hoạt động</option>
              <option value="0">Không hoạt động</option>
            </select>
          </div>
          <div class="flex-none self-end">
            <button 
              @click="applyFilters" 
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none mr-2"
            >
              Lọc
            </button>
            <button 
              @click="resetFilters" 
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none"
            >
              Đặt lại
            </button>
          </div>
        </div>
      </template>
    </AdminTable>

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa sản phẩm ${selectedItem?.name || ''}?`"
      :on-close="closeDeleteModal"
      @confirm="deleteItem"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminTable from '@/components/Admin/AdminTable.vue'
import ConfirmModal from '@/components/Core/ConfirmModal.vue'
import useCrudAdmin from '@/composables/useCrudAdmin.js'
import endpoints from '@/api/endpoints'
import axios from 'axios'

// Danh sách danh mục và thương hiệu
const categories = ref([])
const brands = ref([])

// Sử dụng composable useCrudAdmin
const { 
  // State
  items,
  selectedItems,
  selectedItem,
  loading,
  apiErrors,
  pagination,
  filters,
  
  // Modal state
  showCreateModal,
  showEditModal,
  showDeleteModal,
  
  // CRUD operations
  fetchItems,
  createItem,
  updateItem,
  deleteItem,
  
  // Selection handlers
  toggleSelectAll,
  toggleSelectItem,
  
  // Modal handlers
  openCreateModal,
  closeCreateModal,
  openEditModal,
  closeEditModal,
  openDeleteModal,
  closeDeleteModal,
  
  // Pagination handler
  changePage,
  
  // Filter handlers
  applyFilters,
  resetFilters
} = useCrudAdmin({
  endpoints: endpoints.products,
  resourceName: 'sản phẩm',
  transformItem: (item) => ({
    ...item,
    thumbnail: item.thumbnail ? (item.thumbnail.startsWith('http') ? item.thumbnail : `/storage/${item.thumbnail}`) : null,
    category_name: item.category ? item.category.name : 'Không có',
    brand_name: item.brand ? item.brand.name : 'Không có'
  }),
  afterFetch: (data) => {
    // Cập nhật danh sách danh mục và thương hiệu nếu cần
    if (categories.value.length === 0) {
      fetchCategories()
    }
    if (brands.value.length === 0) {
      fetchBrands()
    }
  }
})

// Lấy danh sách danh mục
async function fetchCategories() {
  try {
    const response = await axios.get(endpoints.categories.list, { params: { all: true } })
    categories.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

// Lấy danh sách thương hiệu
async function fetchBrands() {
  try {
    const response = await axios.get(endpoints.brands.list, { params: { all: true } })
    brands.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching brands:', error)
  }
}

// Helper functions
function getStatusName(status) {
  switch (parseInt(status)) {
    case 1: return 'Hoạt động'
    case 0: return 'Không hoạt động'
    default: return 'Không xác định'
  }
}

function getStatusClass(status) {
  switch (parseInt(status)) {
    case 1: return 'bg-green-100 text-green-800'
    case 0: return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}

// Khởi tạo
onMounted(() => {
  fetchItems()
  fetchCategories()
  fetchBrands()
})
</script> 
