<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý người dùng</h1>
      <button 
        @click="openCreateModal" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm người dùng mới
      </button>
    </div>

    <!-- Bộ lọc -->
    <UserFilter 
      :initial-filters="filters"
      @update:filters="handleFilterUpdate" 
    />

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <SkeletonLoader v-if="loading" type="table" :rows="5" :columns="5" />
      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên đăng nhập</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số điện thoại</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in items" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.username || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.phone || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="getStatusClass(user.status)"
              >
                {{ getStatusLabel(user.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <Actions 
                :item="user"
                @edit="openEditModal"
                @delete="confirmDelete"
              >
                <template #custom="{ item }">
                  <button 
                    @click="openChangePasswordModal(item)" 
                    class="p-2 rounded-full hover:bg-blue-100 transition-colors duration-200"
                    title="Đổi mật khẩu"
                  >
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                  </button>
                  <button 
                    @click="openAssignRoleModal(item)" 
                    class="p-2 rounded-full hover:bg-green-100 transition-colors duration-200"
                    title="Phân quyền"
                  >
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </button>
                </template>
              </Actions>
            </td>
          </tr>
          <tr v-if="items.length === 0">
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
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
    <CreateUser
      v-if="showCreateModal"
      :show="showCreateModal"
      :status-enums="statusEnums"
      :gender-enums="genderEnums"
      :role-enums="roleEnums"
      :on-close="closeCreateModal"
      @created="handleUserCreated"
    />

    <!-- Modal chỉnh sửa -->
    <EditUser
      v-if="showEditModal"
      :show="showEditModal"
      :user="selectedUser"
      :status-enums="statusEnums"
      :gender-enums="genderEnums"
      :role-enums="roleEnums"
      :on-close="closeEditModal"
      @updated="handleUserUpdated"
    />

    <!-- Modal đổi mật khẩu -->
    <ChangePassword
      v-if="showChangePasswordModal"
      :show="showChangePasswordModal"
      :user="selectedUser"
      :on-close="closeChangePasswordModal"
      @password-changed="handlePasswordChanged"
    />

    <!-- Modal phân quyền -->
    <AssignRole
      v-if="showAssignRoleModal"
      :show="showAssignRoleModal"
      :user="selectedUser"
      :on-close="closeAssignRoleModal"
      @role-assigned="handleRoleAssigned"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa người dùng ${selectedUser?.username || ''}?`"
      :on-close="closeDeleteModal"
      @confirm="deleteUser"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, defineAsyncComponent } from 'vue'
import { getEnumSync, getEnumLabel } from '@/constants/enums'
import { useDataTable } from '@/composables/useDataTable'
import { useToast } from '@/composables/useToast'
import SkeletonLoader from '@/components/Core/SkeletonLoader.vue'
import ConfirmModal from '@/components/Core/ConfirmModal.vue'
import Actions from '@/components/Core/Actions.vue'
import endpoints from '@/api/endpoints'

// Lazy load components
const CreateUser = defineAsyncComponent(() => import('./create.vue'))
const EditUser = defineAsyncComponent(() => import('./edit.vue'))
const ChangePassword = defineAsyncComponent(() => import('./change-password.vue'))
const AssignRole = defineAsyncComponent(() => import('./assign-role.vue'))
const UserFilter = defineAsyncComponent(() => import('./filter.vue'))

// Use composables
const { 
  items, 
  loading, 
  pagination, 
  filters, 
  fetchData, 
  updateFilters, 
  deleteItem 
} = useDataTable(endpoints.users.list, {
  defaultFilters: {
    search: '',
    status: '',
    sort_by: 'created_at_desc'
  }
})

const { showSuccess, showError } = useToast()

// State
const selectedUser = ref(null)
const statusEnums = ref([])
const genderEnums = ref([])
const roleEnums = ref([])

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showChangePasswordModal = ref(false)
const showAssignRoleModal = ref(false)
const showDeleteModal = ref(false)

// Fetch data
onMounted(async () => {
  // Load enums immediately (static)
  fetchEnums()
  
  // Load roles from API
  await loadRoles()
  
  // Fetch users
  await fetchData()
})

function handleFilterUpdate(newFilters) {
  updateFilters(newFilters)
  fetchData({ page: 1 })
}

function fetchEnums() {
  // Sử dụng static enum thay vì gọi API
  statusEnums.value = getEnumSync('user_status')
  genderEnums.value = getEnumSync('gender')
}

async function loadRoles() {
  try {
    const response = await fetch(endpoints.roles.list)
    const data = await response.json()
    if (data.success) {
      roleEnums.value = data.data
    }
  } catch (error) {
    console.error('Error loading roles:', error)
    showError('Không thể tải danh sách vai trò')
  }
}

// Modal handlers
function openCreateModal() {
  showCreateModal.value = true
}

function closeCreateModal() {
  showCreateModal.value = false
}

function openEditModal(user) {
  selectedUser.value = user
  showEditModal.value = true
}

function closeEditModal() {
  showEditModal.value = false
  selectedUser.value = null
}

function openChangePasswordModal(user) {
  selectedUser.value = user
  showChangePasswordModal.value = true
}

function closeChangePasswordModal() {
  showChangePasswordModal.value = false
  selectedUser.value = null
}

async function openAssignRoleModal(user) {
  selectedUser.value = user
  
  // Đảm bảo roles đã được load
  if (roleEnums.value.length === 0) {
    await loadRoles()
  }
  
  showAssignRoleModal.value = true
}

function closeAssignRoleModal() {
  showAssignRoleModal.value = false
  selectedUser.value = null
}

function confirmDelete(user) {
  selectedUser.value = user
  showDeleteModal.value = true
}

function closeDeleteModal() {
  showDeleteModal.value = false
  selectedUser.value = null
}

// Action handlers
async function handleUserCreated() {
  await fetchData()
  closeCreateModal()
  showSuccess('Người dùng đã được tạo thành công')
}

async function handleUserUpdated() {
  await fetchData()
  closeEditModal()
  showSuccess('Người dùng đã được cập nhật thành công')
}

async function handlePasswordChanged() {
  closeChangePasswordModal()
  showSuccess('Mật khẩu đã được thay đổi thành công')
}

async function handleRoleAssigned() {
  await fetchData()
  closeAssignRoleModal()
  showSuccess('Vai trò đã được phân công thành công')
}

async function deleteUser() {
  try {
    await deleteItem(selectedUser.value.id)
    closeDeleteModal()
    showSuccess('Người dùng đã được xóa thành công')
  } catch (error) {
    showError('Không thể xóa người dùng')
  }
}

function changePage(url) {
  if (!url) return
  
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  fetchData({ page })
}

// Helper functions
function getStatusLabel(status) {
  return getEnumLabel('user_status', status) || status || 'Không xác định'
}

function getStatusName(status) {
  return getEnumLabel('user_status', status) || status
}

function getStatusClass(status) {
  switch (status) {
    case 'active': return 'bg-green-100 text-green-800'
    case 'pending': return 'bg-yellow-100 text-yellow-800'
    case 'inactive': return 'bg-gray-100 text-gray-800'
    case 'banned': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}
</script>
