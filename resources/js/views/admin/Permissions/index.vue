<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý quyền</h1>
      <button 
        @click="openCreateModal" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm quyền mới
      </button>
    </div>

    <!-- Bộ lọc -->
    <PermissionFilter 
      :initial-filters="filters"
      @update:filters="handleFilterUpdate" 
    />

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <SkeletonLoader v-if="loading" type="table" :rows="5" :columns="7" />
      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên quyền</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên hiển thị</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guard</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quyền cha</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="permission in items" :key="permission.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ permission.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ permission.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ permission.display_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ permission.guard_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ permission.parent_name || '-- Không có --' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="getStatusClass(permission.status)"
              >
                {{ getStatusLabel(permission.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <Actions 
                v-if="!permission.has_children"
                :item="permission"
                @edit="openEditModal"
                @delete="confirmDelete"
              />
              <span v-if="permission.has_children" class="text-gray-500 text-sm">
                Không thể sửa/xóa (có {{ permission.children_count }} quyền con)
              </span>
            </td>
          </tr>
          <tr v-if="items.length === 0">
            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
              Không có dữ liệu
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Phân trang -->
    <div class="mt-4 flex justify-between items-center">
      <div class="text-sm text-gray-700">
        Hiển thị {{ pagination.from }} đến {{ pagination.to }} trên tổng số {{ pagination.total }} bản ghi
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
    <CreatePermission
      v-if="showCreateModal"
      :show="showCreateModal"
      :on-close="closeCreateModal"
      @created="handlePermissionCreated"
    />

    <!-- Modal chỉnh sửa -->
    <EditPermission
      v-if="showEditModal"
      :show="showEditModal"
      :permission="selectedPermission"
      :on-close="closeEditModal"
      @updated="handlePermissionUpdated"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      message="Bạn có chắc chắn muốn xóa quyền này không?"
      @confirm="deletePermission"
      @cancel="closeDeleteModal"
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
const CreatePermission = defineAsyncComponent(() => import('./create.vue'))
const EditPermission = defineAsyncComponent(() => import('./edit.vue'))
const PermissionFilter = defineAsyncComponent(() => import('./filter.vue'))

// Use composables
const { 
  items, 
  loading, 
  pagination, 
  filters, 
  fetchData, 
  updateFilters, 
  deleteItem 
} = useDataTable(endpoints.permissions.list, {
  defaultFilters: {
    search: '',
    status: '',
    sort_by: 'created_at_desc'
  }
})

const { showSuccess, showError } = useToast()

// State
const selectedPermission = ref(null)

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

// Fetch data
onMounted(async () => {
  await fetchData()
})

// Filter handlers
function handleFilterUpdate(newFilters) {
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

function openEditModal(permission) {
  selectedPermission.value = permission
  showEditModal.value = true
}

function closeEditModal() {
  showEditModal.value = false
  selectedPermission.value = null
}

function confirmDelete(permission) {
  selectedPermission.value = permission
  showDeleteModal.value = true
}

function closeDeleteModal() {
  showDeleteModal.value = false
  selectedPermission.value = null
}

// Action handlers
async function handlePermissionCreated() {
  await fetchData()
  closeCreateModal()
  showSuccess('Quyền đã được tạo thành công')
}

async function handlePermissionUpdated() {
  await fetchData()
  closeEditModal()
  showSuccess('Quyền đã được cập nhật thành công')
}

async function deletePermission() {
  try {
    await deleteItem(selectedPermission.value.id)
    closeDeleteModal()
    showSuccess('Quyền đã được xóa thành công')
  } catch (error) {
    showError('Không thể xóa quyền')
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

<style>
/* Đảm bảo table luôn cuộn ngang được trên mobile */
.bg-white > .overflow-x-auto {
  width: 100%;
}
</style>
