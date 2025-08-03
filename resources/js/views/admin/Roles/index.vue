<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý vai trò</h1>
      <button 
        @click="openCreateModal" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm vai trò mới
      </button>
    </div>

    <!-- Bộ lọc -->
    <RoleFilter 
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
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên vai trò</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên hiển thị</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guard</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò cha</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="role in items" :key="role.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ role.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ role.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ role.display_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ role.guard_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getParentName(role.parent_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="getStatusClass(role.status)"
              >
                {{ getStatusLabel(role.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <Actions 
                :item="role"
                @edit="openEditModal"
                @delete="confirmDelete"
              />
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
    <CreateRole
      v-if="showCreateModal"
      :show="showCreateModal"
      :status-enums="statusEnums"
      :on-close="closeCreateModal"
      @created="handleRoleCreated"
    />

    <!-- Modal chỉnh sửa -->
    <EditRole
      v-if="showEditModal"
      :show="showEditModal"
      :role="selectedRole"
      :status-enums="statusEnums"
      :on-close="closeEditModal"
      @updated="handleRoleUpdated"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa vai trò ${selectedRole?.name || ''}?`"
      :on-close="closeDeleteModal"
      @confirm="deleteRole"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, defineAsyncComponent } from 'vue'
import { getEnumSync, getEnumLabel } from '@/constants/enums'
import { useDataTable } from '@/composables/useDataTable'
import { useToast } from '@/composables/useToast'
import SkeletonLoader from '@/components/Core/SkeletonLoader.vue'
import ConfirmModal from '@/components/Core/ConfirmModal.vue'
import Actions from '@/components/Core/Actions.vue'
import endpoints from '@/api/endpoints'

// Lazy load components
const CreateRole = defineAsyncComponent(() => import('./create.vue'))
const EditRole = defineAsyncComponent(() => import('./edit.vue'))
const RoleFilter = defineAsyncComponent(() => import('./filter.vue'))

// Use composables
const { 
  items, 
  loading, 
  pagination, 
  filters, 
  fetchData, 
  updateFilters, 
  deleteItem 
} = useDataTable(endpoints.roles.list, {
  defaultFilters: {
    search: '',
    status: '',
    sort_by: 'created_at_desc'
  }
})

const { showSuccess, showError } = useToast()

// State
const selectedRole = ref(null)
const statusEnums = ref([])

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

// Fetch data
onMounted(async () => {
  // Load enums immediately (static)
  fetchEnums()
  
  // Fetch roles
  await fetchData()
})

function handleFilterUpdate(newFilters) {
  updateFilters(newFilters)
  fetchData({ page: 1 })
}

function fetchEnums() {
  // Sử dụng static enum thay vì gọi API
  statusEnums.value = getEnumSync('basic_status')
}

// Modal handlers
function openCreateModal() {
  showCreateModal.value = true
}

function closeCreateModal() {
  showCreateModal.value = false
}

function openEditModal(role) {
  selectedRole.value = role
  showEditModal.value = true
}

function closeEditModal() {
  showEditModal.value = false
  selectedRole.value = null
}

function confirmDelete(role) {
  selectedRole.value = role
  showDeleteModal.value = true
}

function closeDeleteModal() {
  showDeleteModal.value = false
  selectedRole.value = null
}

// Action handlers
async function handleRoleCreated() {
  await fetchData()
  closeCreateModal()
  showSuccess('Vai trò đã được tạo thành công')
}

async function handleRoleUpdated() {
  await fetchData()
  closeEditModal()
  showSuccess('Vai trò đã được cập nhật thành công')
}

async function deleteRole() {
  try {
    await deleteItem(selectedRole.value.id)
    closeDeleteModal()
    showSuccess('Vai trò đã được xóa thành công')
  } catch (error) {
    showError('Không thể xóa vai trò')
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

// Map id -> vai trò để tra nhanh tên vai trò cha
const roleMap = computed(() => {
  const map = {}
  items.value.forEach(r => { map[r.id] = r })
  return map
})

function getParentName(parent_id) {
  if (!parent_id) return '—'
  const r = roleMap.value[parent_id]
  return r ? (r.display_name || r.name) : parent_id
}
</script>

<style>
/* Cho phép cuộn ngang table khi màn hình nhỏ */
.table-responsive {
  overflow-x: auto;
}
</style> 
