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
      :initial-filters="currentFilters"
      @update:filters="handleFilterUpdate" 
    />

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên đăng nhập</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.username }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              >
                {{ user.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button 
                @click="openEditModal(user)" 
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Sửa
              </button>
              <button 
                @click="openChangePasswordModal(user)" 
                class="text-blue-600 hover:text-blue-900 mr-3"
              >
                Đổi mật khẩu
              </button>
              <button 
                @click="confirmDelete(user)" 
                class="text-red-600 hover:text-red-900"
              >
                Xóa
              </button>
            </td>
          </tr>
          <tr v-if="users.length === 0">
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
import { ref, onMounted, reactive } from 'vue'
import CreateUser from './create.vue'
import EditUser from './edit.vue'
import ChangePassword from './change-password.vue'
import UserFilter from './filter.vue'
import ConfirmModal from '@/components/ConfirmModal.vue'
import endpoints from '@/api/endpoints'
import apiClient from '@/api/apiClient'

// State
const users = ref([])
const selectedUser = ref(null)
const currentFilters = ref({
  search: '',
  status: '',
  sort_by: 'created_at_desc'
})
const statusEnums = ref([
  { value: 1, name: 'Hoạt động' },
  { value: 2, name: 'Chờ xác nhận' },
  { value: 3, name: 'Đã khóa' }
])
const genderEnums = ref([
  { value: 1, name: 'Nam' },
  { value: 2, name: 'Nữ' },
  { value: 3, name: 'Khác' }
])
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
const showChangePasswordModal = ref(false)
const showDeleteModal = ref(false)

// Fetch data
onMounted(async () => {
  await Promise.all([
    fetchUsers(),
    fetchEnums()
  ])
})

async function fetchUsers(page = 1) {
  try {
    const response = await apiClient.get(endpoints.users.list, {
      params: { 
        page,
        ...currentFilters.value
      }
    })
    users.value = response.data.data
    
    // Update pagination
    const meta = response.data.meta
    pagination.current_page = meta.current_page
    pagination.from = meta.from
    pagination.to = meta.to
    pagination.total = meta.total
    pagination.per_page = meta.per_page
    pagination.links = meta.links
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

function handleFilterUpdate(filters) {
  currentFilters.value = filters
  fetchUsers(1)
}

async function fetchEnums() {
  try {
    const [statusResponse, genderResponse] = await Promise.all([
      apiClient.get(endpoints.enums('UserStatus')),
      apiClient.get(endpoints.enums('Gender'))
    ])
    
    statusEnums.value = Array.isArray(statusResponse.data) ? statusResponse.data : []
    genderEnums.value = Array.isArray(genderResponse.data) ? genderResponse.data : []
    
    console.log('Status enums:', statusEnums.value)
    console.log('Gender enums:', genderEnums.value)
  } catch (error) {
    console.error('Error fetching enums:', error)
    statusEnums.value = []
    genderEnums.value = []
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
  await fetchUsers()
  closeCreateModal()
}

async function handleUserUpdated() {
  await fetchUsers()
  closeEditModal()
}

async function handlePasswordChanged() {
  closeChangePasswordModal()
}

async function deleteUser() {
  try {
    await apiClient.delete(endpoints.users.delete(selectedUser.value.id))
    await fetchUsers()
    closeDeleteModal()
  } catch (error) {
    console.error('Error deleting user:', error)
  }
}

function changePage(url) {
  if (!url) return
  
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  fetchUsers(page)
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
    case 'pending': return 'bg-yellow-100 text-yellow-800'
    case 'inactive': return 'bg-red-100 text-red-800'
    case 'banned': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}
</script>