<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý thuộc tính</h1>
      <button 
        @click="openCreateModal" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm thuộc tính
      </button>
    </div>

    <!-- Bộ lọc -->
    <AttributeFilter
      :initial-filters="filters"
      @update:filters="handleFilterChange"
    />

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <SkeletonLoader v-if="loading" type="table" :rows="5" :columns="6" />
      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên thuộc tính</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kiểu</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tùy chọn</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="attribute in items" :key="attribute.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ attribute.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              <div>{{ attribute.name }}</div>
              <div v-if="attribute.description" class="text-xs text-gray-500 mt-1">{{ attribute.description }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                {{ attribute.type }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <div class="flex flex-wrap gap-1">
                <span v-if="attribute.is_required" class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Bắt buộc</span>
                <span v-if="attribute.is_unique" class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">Duy nhất</span>
                <span v-if="attribute.is_filterable" class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Lọc</span>
                <span v-if="attribute.is_searchable" class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Tìm</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="getStatusClass(attribute.status)"
              >
                {{ getStatusLabel(attribute.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <Actions 
                :item="attribute"
                @edit="openEditModal"
                @delete="confirmDelete"
              />
            </td>
          </tr>
          <tr v-if="items.length === 0">
            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
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
    <CreateAttribute
      v-if="showCreateModal"
      :show="showCreateModal"
      :on-close="closeCreateModal"
      @created="handleAttributeCreated"
    />

    <!-- Modal chỉnh sửa -->
    <EditAttribute
      v-if="showEditModal"
      :show="showEditModal"
      :attribute="selectedAttribute"
      :on-close="closeEditModal"
      @updated="handleAttributeUpdated"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa thuộc tính ${selectedAttribute?.name || ''}?`"
      :on-close="closeDeleteModal"
      @confirm="deleteAttribute"
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

// Lazy load components
const CreateAttribute = defineAsyncComponent(() => import('./create.vue'))
const EditAttribute = defineAsyncComponent(() => import('./edit.vue'))
const AttributeFilter = defineAsyncComponent(() => import('./filter.vue'))

// Use composables
const { 
  items, 
  loading, 
  pagination, 
  filters, 
  fetchData, 
  updateFilters, 
  deleteItem 
} = useDataTable(endpoints.attributes.list, {
  defaultFilters: {
    search: '',
    status: '',
    sort_by: 'created_at_desc'
  }
})

const { showSuccess, showError } = useToast()

// State
const selectedAttribute = ref(null)

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

function openEditModal(attribute) {
  selectedAttribute.value = attribute
  showEditModal.value = true
}

function closeEditModal() {
  showEditModal.value = false
  selectedAttribute.value = null
}

function confirmDelete(attribute) {
  selectedAttribute.value = attribute
  showDeleteModal.value = true
}

function closeDeleteModal() {
  showDeleteModal.value = false
  selectedAttribute.value = null
}

// Action handlers
async function handleAttributeCreated() {
  await fetchData()
  closeCreateModal()
  showSuccess('Thuộc tính đã được tạo thành công')
}

async function handleAttributeUpdated() {
  await fetchData()
  closeEditModal()
  showSuccess('Thuộc tính đã được cập nhật thành công')
}

async function deleteAttribute() {
  try {
    await deleteItem(selectedAttribute.value.id)
    closeDeleteModal()
    showSuccess('Thuộc tính đã được xóa thành công')
  } catch (error) {
    showError('Không thể xóa thuộc tính')
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
</style> 
