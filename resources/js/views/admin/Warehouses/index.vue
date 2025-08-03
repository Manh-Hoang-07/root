<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý kho hàng</h1>
      <button 
        @click="openCreateModal" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm kho hàng mới
      </button>
    </div>

    <!-- Bộ lọc -->
    <WarehouseFilter
      :initial-filters="filters"
      @update:filters="handleFilterChange"
    />

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <SkeletonLoader v-if="loading" type="table" :rows="5" :columns="10" />
      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên kho</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa chỉ</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thành phố</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tỉnh/Thành</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số điện thoại</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="warehouse in items" :key="warehouse.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ warehouse.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ warehouse.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ warehouse.address }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ warehouse.city }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ warehouse.province }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ warehouse.phone }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ warehouse.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="getStatusClass(warehouse.status)"
              >
                {{ getStatusLabel(warehouse.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(warehouse.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <Actions 
                :item="warehouse"
                @edit="openEditModal"
                @delete="confirmDelete"
              />
            </td>
          </tr>
          <tr v-if="items.length === 0">
            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
              Không có dữ liệu
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Phân trang -->
    <div v-if="items.length > 0" class="mt-4 flex justify-between items-center">
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
    <CreateWarehouse
      v-if="showCreateModal"
      :show="showCreateModal"
      :on-close="closeCreateModal"
      @created="handleWarehouseCreated"
    />

    <!-- Modal chỉnh sửa -->
    <EditWarehouse
      v-if="showEditModal"
      :show="showEditModal"
      :warehouse="selectedWarehouse"
      :on-close="closeEditModal"
      @updated="handleWarehouseUpdated"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa kho hàng ${selectedWarehouse?.name || ''}?`"
      :on-close="closeDeleteModal"
      @confirm="deleteWarehouse"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, defineAsyncComponent } from 'vue'
import { useDataTable } from '@/composables/useDataTable'
import { useToast } from '@/composables/useToast'
import SkeletonLoader from '@/components/Core/SkeletonLoader.vue'
import ConfirmModal from '@/components/Core/ConfirmModal.vue'
import Actions from '@/components/Core/Actions.vue'
import { getEnumLabel } from '@/constants/enums'
import endpoints from '@/api/endpoints'
import { formatDate } from '@/utils/formatDate'

// Lazy load components
const CreateWarehouse = defineAsyncComponent(() => import('./create.vue'))
const EditWarehouse = defineAsyncComponent(() => import('./edit.vue'))
const WarehouseFilter = defineAsyncComponent(() => import('./filter.vue'))

// Use composables
const { 
  items, 
  loading, 
  pagination, 
  filters, 
  fetchData, 
  updateFilters, 
  deleteItem 
} = useDataTable(endpoints.warehouses.list, {
  defaultFilters: {
    search: '',
    status: '',
    sort_by: 'created_at_desc'
  }
})

const { showSuccess, showError } = useToast()

// State
const selectedWarehouse = ref(null)

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

// Fetch data
onMounted(async () => {
  await fetchData()
})

// Filter handlers
function handleFilterChange(newFilters) {
  updateFilters(newFilters)
  fetchData({ page: 1 })
}

// Modal handlers
function openCreateModal() {
  showCreateModal.value = true
}

function closeCreateModal() {
  showCreateModal.value = false
}

function openEditModal(warehouse) {
  selectedWarehouse.value = warehouse
  showEditModal.value = true
}

function closeEditModal() {
  showEditModal.value = false
  selectedWarehouse.value = null
}

function confirmDelete(warehouse) {
  selectedWarehouse.value = warehouse
  showDeleteModal.value = true
}

function closeDeleteModal() {
  showDeleteModal.value = false
  selectedWarehouse.value = null
}

// Action handlers
async function handleWarehouseCreated() {
  await fetchData()
  closeCreateModal()
  showSuccess('Kho hàng đã được tạo thành công')
}

async function handleWarehouseUpdated() {
  await fetchData()
  closeEditModal()
  showSuccess('Kho hàng đã được cập nhật thành công')
}

async function deleteWarehouse() {
  try {
    await deleteItem(selectedWarehouse.value.id)
    closeDeleteModal()
    showSuccess('Kho hàng đã được xóa thành công')
  } catch (error) {
    showError('Không thể xóa kho hàng')
  }
}

function changePage(url) {
  if (!url) return
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  fetchData({ page })
}

// Status helper functions
function getStatusLabel(status) {
  return getEnumLabel('basic_status', status) || status || 'Không xác định'
}

function getStatusClass(status) {
  if (status === 'active') return 'bg-green-100 text-green-800'
  if (status === 'inactive') return 'bg-red-100 text-red-800'
  return 'bg-gray-100 text-gray-800'
}
</script>

<style scoped>
/* Custom styles */
</style> 
