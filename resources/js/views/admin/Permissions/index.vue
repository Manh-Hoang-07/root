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
      :initial-filters="currentFilters"
      @update:filters="handleFilterUpdate" 
    />

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
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
          <tr v-for="permission in permissions" :key="permission.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ permission.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ permission.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ permission.display_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ permission.guard_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ permission.parent_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="getStatusClass(permission.status)"
              >
                {{ getStatusName(permission.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button 
                v-if="!permission.has_children"
                @click="openEditModal(permission)" 
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Sửa
              </button>
              <button 
                v-if="!permission.has_children"
                @click="confirmDelete(permission)" 
                class="text-red-600 hover:text-red-900"
              >
                Xóa
              </button>
              <span v-if="permission.has_children" class="text-gray-500 text-sm">
                Không thể sửa/xóa (có {{ permission.children_count }} quyền con)
              </span>
            </td>
          </tr>
          <tr v-if="permissions.length === 0">
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
import { ref, onMounted, reactive } from 'vue'
import CreatePermission from './create.vue'
import EditPermission from './edit.vue'
import PermissionFilter from './filter.vue'
import ConfirmModal from '@/components/ConfirmModal.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'

// State
const permissions = ref([])
const selectedPermission = ref(null)
const loading = ref(false)

// Filter state
const currentFilters = ref({
  search: '',
  status: '',
  sort_by: 'created_at_desc'
})

// Pagination state
const pagination = reactive({
  current_page: 1,
  from: 0,
  to: 0,
  total: 0,
  per_page: 20,
  links: []
})

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

// Fetch data
onMounted(async () => {
  await fetchPermissions()
})

async function fetchPermissions(page = 1) {
  loading.value = true
  try {
    const response = await axios.get(endpoints.permissions.list, {
      params: { 
        page,
        ...currentFilters.value
      }
    })
    permissions.value = response.data.data
    // Update pagination
    const meta = response.data.meta
    if (meta) {
      pagination.current_page = meta.current_page
      pagination.from = meta.from
      pagination.to = meta.to
      pagination.total = meta.total
      pagination.per_page = meta.per_page
      pagination.links = meta.links
    }
  } catch (error) {
    console.error('Error fetching permissions:', error)
  } finally {
    loading.value = false
  }
}

// Filter handlers
function handleFilterUpdate(filters) {
  currentFilters.value = filters
  fetchPermissions(1)
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
  await fetchPermissions()
  closeCreateModal()
}

async function handlePermissionUpdated() {
  await fetchPermissions()
  closeEditModal()
}

async function deletePermission() {
  try {
    await axios.delete(endpoints.permissions.delete(selectedPermission.value.id))
    await fetchPermissions()
    closeDeleteModal()
  } catch (error) {
    console.error('Error deleting permission:', error)
  }
}

function changePage(url) {
  if (!url) return
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  fetchPermissions(page)
}

// Helper functions
function getStatusClass(status) {
  if (status === 'active') {
    return 'bg-green-100 text-green-800'
  } else {
    return 'bg-red-100 text-red-800'
  }
}

function getStatusName(status) {
  if (status === 'active') {
    return 'Hoạt động'
  } else {
    return 'Không hoạt động'
  }
}
</script>

<style>
/* Đảm bảo table luôn cuộn ngang được trên mobile */
.bg-white > .overflow-x-auto {
  width: 100%;
}
</style>