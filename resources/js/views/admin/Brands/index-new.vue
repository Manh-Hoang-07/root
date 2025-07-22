<template>
  <div class="container mx-auto p-4">
    <AdminTable
      title="Quản lý thương hiệu"
      :items="items"
      :loading="loading"
      :pagination="pagination"
      :selected-items="selectedItems"
      add-button-text="Thêm thương hiệu mới"
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
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên thương hiệu</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
      </template>
      
      <!-- Table row -->
      <template #row="{ item }">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.id }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.name }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.slug }}</td>
        <td class="px-6 py-4 whitespace-nowrap">
          <img v-if="item.logo" :src="item.logo" alt="Logo" class="h-10 w-10 object-contain" />
          <span v-else class="text-gray-400">Không có logo</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <span 
            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
            :class="item.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
          >
            {{ item.status ? 'Hoạt động' : 'Không hoạt động' }}
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
              placeholder="Tìm theo tên thương hiệu" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              @keyup.enter="applyFilters"
            />
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

    <!-- Modal thêm mới -->
    <BrandForm
      v-if="showCreateModal"
      :show="showCreateModal"
      :api-errors="apiErrors"
      @submit="createItem"
      @cancel="closeCreateModal"
    />

    <!-- Modal chỉnh sửa -->
    <BrandForm
      v-if="showEditModal"
      :show="showEditModal"
      :brand="selectedItem"
      :api-errors="apiErrors"
      @submit="updateItem"
      @cancel="closeEditModal"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa thương hiệu ${selectedItem?.name || ''}?`"
      :on-close="closeDeleteModal"
      @confirm="deleteItem"
    />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import AdminTable from '@/components/AdminTable.vue'
import BrandForm from './BrandForm.vue'
import ConfirmModal from '@/components/ConfirmModal.vue'
import useCrudAdmin from '@/composables/useCrudAdmin.js'
import endpoints from '@/api/endpoints'

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
  endpoints: endpoints.brands,
  resourceName: 'thương hiệu',
  transformItem: (item) => ({
    ...item,
    logo: item.logo ? (item.logo.startsWith('http') ? item.logo : `/storage/${item.logo}`) : null
  })
})

// Khởi tạo
onMounted(() => {
  fetchItems()
})
</script> 