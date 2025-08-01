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
      :initial-filters="currentFilters"
      @update:filters="handleFilterUpdate" 
    />

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
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
          <tr v-for="role in roles" :key="role.id">
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
                {{ getStatusName(role.status) }}
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
          <tr v-if="roles.length === 0">
            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
              {{ loading ? 'Đang tải dữ liệu...' : 'Không có dữ liệu' }}
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
import { ref, onMounted, reactive, computed } from 'vue'
import { getEnumSync } from '@/constants/enums'
import CreateRole from './create.vue'
import EditRole from './edit.vue'
import RoleFilter from './filter.vue'
import ConfirmModal from '@/components/Core/ConfirmModal.vue'
import Actions from '@/components/Core/Actions.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'

// State
const roles = ref([])
const selectedRole = ref(null)
const currentFilters = ref({
  search: '',
  status: '',
  sort_by: 'created_at_desc'
})
const statusEnums = ref([
  { value: 1, name: 'Hoạt động' },
  { value: 2, name: 'Không hoạt động' }
])
const pagination = reactive({
  current_page: 1,
  from: 0,
  to: 0,
  total: 0,
  per_page: 10,
  links: []
})
const loading = ref(false)

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

// Fetch data
onMounted(async () => {
  // Load enums immediately (static)
  fetchEnums()
  
  // Fetch roles
  await fetchRoles()
})

async function fetchRoles(page = 1) {
  loading.value = true
  try {
    const response = await axios.get(endpoints.roles.list, {
      params: { 
        page,
        ...currentFilters.value
      }
    })
    roles.value = response.data.data
    
    // Update pagination
    const meta = response.data.meta
    pagination.current_page = meta.current_page
    pagination.from = meta.from
    pagination.to = meta.to
    pagination.total = meta.total
    pagination.per_page = meta.per_page
    pagination.links = meta.links
  } catch (error) {
    console.error('Error fetching roles:', error)
  } finally {
    loading.value = false
  }
}

function handleFilterUpdate(filters) {
  currentFilters.value = filters
  fetchRoles(1)
}

function fetchEnums() {
  // Sử dụng static enum thay vì gọi API
  statusEnums.value = getEnumSync('role_status')
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
  await fetchRoles()
  closeCreateModal()
}

async function handleRoleUpdated() {
  await fetchRoles()
  closeEditModal()
}

async function deleteRole() {
  try {
    await axios.delete(endpoints.roles.delete(selectedRole.value.id))
    await fetchRoles()
    closeDeleteModal()
  } catch (error) {
    console.error('Error deleting role:', error)
  }
}

function changePage(url) {
  if (!url) return
  
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  fetchRoles(page)
}

// Helper functions
function getStatusName(status) {
  if (!Array.isArray(statusEnums.value)) {
    return status;
  }
  const statusObj = statusEnums.value.find(s => s.id === status)
  return statusObj ? statusObj.name : status
}

function getStatusClass(status) {
  switch (status) {
    case 'active': return 'bg-green-100 text-green-800'
    case 'inactive': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}

// Map id -> vai trò để tra nhanh tên vai trò cha
const roleMap = computed(() => {
  const map = {}
  roles.value.forEach(r => { map[r.id] = r })
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
