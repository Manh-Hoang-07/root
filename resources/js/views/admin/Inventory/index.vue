<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý tồn kho</h1>
      <div class="flex space-x-2">
        <button 
          @click="openImportModal" 
          class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none"
        >
          Nhập kho
        </button>
        <button 
          @click="openCreateModal" 
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
        >
          Thêm tồn kho mới
        </button>
      </div>
    </div>



    <!-- Bộ lọc -->
    <InventoryFilter 
      :initial-filters="currentFilters"
      :warehouses="filterOptions.warehouses || []"
      :brands="filterOptions.brands || []"
      :categories="filterOptions.categories || []"
      @update:filters="handleFilterUpdate" 
    />

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kho hàng</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lô hàng</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hạn SD</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng SL</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đã giữ</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Có thể bán</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá vốn</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="inventory in inventories" :key="inventory.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <img
                    v-if="inventory.product?.image"
                    :src="inventory.product.image"
                    :alt="inventory.product.name"
                    class="h-10 w-10 rounded-lg object-cover"
                  />
                  <div
                    v-else
                    class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center"
                  >
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ inventory.product?.name }}</div>
                  <div class="text-sm text-gray-500">{{ inventory.product?.brand?.name }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ inventory.warehouse?.name || 'N/A' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              <div>
                <div v-if="inventory.batch_no" class="font-medium">{{ inventory.batch_no }}</div>
                <div v-if="inventory.lot_no" class="text-xs text-gray-500">{{ inventory.lot_no }}</div>
                <div v-if="!inventory.batch_no && !inventory.lot_no" class="text-gray-400">-</div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <div v-if="inventory.expiry_date">
                <span 
                  :class="inventory.expiry_status_class"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ formatDate(inventory.expiry_date) }}
                </span>
              </div>
              <span v-else class="text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="text-sm font-medium text-gray-900">{{ inventory.quantity || 0 }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="text-sm text-gray-900">{{ inventory.reserved_quantity || 0 }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="text-sm font-medium text-gray-900">{{ inventory.available_quantity || 0 }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              <span v-if="inventory.cost_price">
                {{ formatCurrency(inventory.cost_price) }}
              </span>
              <span v-else class="text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="inventory.stock_status_class"
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ inventory.stock_status_text }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <Actions 
                :item="inventory"
                @edit="openEditModal"
                @delete="confirmDelete"
              />
            </td>
          </tr>
          <tr v-if="inventories.length === 0">
            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
              {{ loading ? 'Đang tải dữ liệu...' : 'Không có dữ liệu' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Phân trang -->
    <div v-if="inventories.length > 0" class="mt-4 flex justify-between items-center">
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
    <CreateInventory
      v-if="showCreateModal"
      :show="showCreateModal"
      :products="filterOptions.products || []"
      :warehouses="filterOptions.warehouses || []"
      :on-close="closeCreateModal"
      @created="handleInventoryCreated"
    />

    <!-- Modal chỉnh sửa -->
    <EditInventory
      v-if="showEditModal"
      :show="showEditModal"
      :inventory="selectedInventory"
      :products="filterOptions.products || []"
      :warehouses="filterOptions.warehouses || []"
      :on-close="closeEditModal"
      @updated="handleInventoryUpdated"
    />

    <!-- Modal nhập kho -->
    <ImportInventory
      v-if="showImportModal"
      :show="showImportModal"
      :products="filterOptions.products || []"
      :warehouses="filterOptions.warehouses || []"
      :on-close="closeImportModal"
      @imported="handleInventoryImported"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa tồn kho này?`"
      :on-close="closeDeleteModal"
      @confirm="deleteInventory"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import CreateInventory from './create.vue'
import EditInventory from './edit.vue'
import ImportInventory from './import.vue'
import InventoryFilter from './filter.vue'
import ConfirmModal from '@/components/Core/ConfirmModal.vue'
import Actions from '@/components/Core/Actions.vue'
import endpoints from '@/api/endpoints'
import apiClient from '@/api/apiClient'

// State
const inventories = ref([])
const selectedInventory = ref(null)
const currentFilters = ref({
  search: '',
  warehouse_id: '',
  brand_id: '',
  category_id: '',
  stock_status: '',
  expiry_status: '',
  sort_by: 'updated_at_desc'
})

const filterOptions = ref({
  brands: [],
  categories: [],
  warehouses: [],
  products: []
})



const pagination = reactive({
  current_page: 1,
  from: 0,
  to: 0,
  total: 0,
  per_page: 10,
  links: []
})

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showImportModal = ref(false)
const showDeleteModal = ref(false)
const loading = ref(false)

// Fetch data
onMounted(async () => {
  await Promise.all([
    loadFilterOptions(),
    fetchInventories()
  ])
})

async function fetchInventories(page = 1) {
  try {
    loading.value = true
    const response = await apiClient.get(endpoints.inventory.list, {
      params: { 
        page,
        ...currentFilters.value
      }
    })
    
    if (response.data.success) {
      inventories.value = response.data.data.data || []
      
      // Update pagination
      const meta = response.data.data.pagination
      if (meta) {
        pagination.current_page = meta.current_page || 1
        pagination.from = meta.from || 0
        pagination.to = meta.to || 0
        pagination.total = meta.total || 0
        pagination.per_page = meta.per_page || 10
        pagination.links = meta.links || []
      }
    } else {
      console.error('API returned success: false')
      inventories.value = []
    }
  } catch (error) {
    console.error('Error fetching inventories:', error)
    inventories.value = []
  } finally {
    loading.value = false
  }
}

async function loadFilterOptions() {
  try {
    const response = await apiClient.get('/api/admin/inventory/filter-options')
    if (response.data.success) {
      filterOptions.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading filter options:', error)
  }
}



function handleFilterUpdate(filters) {
  currentFilters.value = filters
  fetchInventories(1)
}

// Modal handlers
function openCreateModal() {
  showCreateModal.value = true
}

function closeCreateModal() {
  showCreateModal.value = false
}

function openEditModal(inventory) {
  selectedInventory.value = inventory
  showEditModal.value = true
}

function closeEditModal() {
  showEditModal.value = false
  selectedInventory.value = null
}

function openImportModal() {
  showImportModal.value = true
}

function closeImportModal() {
  showImportModal.value = false
}

function confirmDelete(inventory) {
  selectedInventory.value = inventory
  showDeleteModal.value = true
}

function closeDeleteModal() {
  showDeleteModal.value = false
  selectedInventory.value = null
}

// Action handlers
async function handleInventoryCreated() {
  await fetchInventories()
  closeCreateModal()
}

async function handleInventoryUpdated() {
  await fetchInventories()
  closeEditModal()
}

async function handleInventoryImported() {
  await fetchInventories()
  closeImportModal()
}

async function deleteInventory() {
  try {
    await apiClient.delete(endpoints.inventory.delete(selectedInventory.value.id))
    await fetchInventories()
    closeDeleteModal()
  } catch (error) {
    console.error('Error deleting inventory:', error)
  }
}

function changePage(url) {
  if (!url) return
  
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  fetchInventories(page)
}

// Helper functions
function formatDate(timestamp) {
  if (!timestamp) return 'N/A'
  const date = new Date(timestamp)
  return date.toLocaleDateString('vi-VN')
}

function formatCurrency(amount) {
  if (!amount) return '0'
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}
</script>

<style scoped>
.inventory-management {
  padding: 1.5rem;
}
</style> 