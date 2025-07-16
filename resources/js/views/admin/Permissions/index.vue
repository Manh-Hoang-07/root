<template>
  <TableLayout :loading="loading" :pagination="pagination" :has-data="permissions.length > 0" @page-change="fetchPermissions">
    <template #header>
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Quản lý quyền</h1>
          <p class="text-gray-600">Quản lý các quyền truy cập hệ thống</p>
        </div>
        <button
          @click="openAddModal"
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
        >
          <span>Thêm quyền</span>
        </button>
      </div>
    </template>
    <template #filter>
      <Filter :filters="filters" @update:filters="onUpdateFilters" @clear="clearFilters" />
    </template>
    <template #table>
      <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8 overflow-auto">
        <table class="w-full table-auto text-sm">
          <thead class="bg-gray-50">
            <tr class="text-left">
              <th class="px-4 py-3 whitespace-nowrap">Tên quyền</th>
              <th class="px-4 py-3 whitespace-nowrap">Tên quyền hiển thị</th>
              <th class="px-4 py-3 whitespace-nowrap">Guard</th>
              <th class="px-4 py-3 whitespace-nowrap">Ngày tạo</th>
              <th class="px-4 py-3 whitespace-nowrap">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="permissions.length === 0">
              <td colspan="4" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
            </tr>
            <tr v-for="permission in permissions" :key="permission.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 whitespace-nowrap">{{ permission.name }}</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ permission.display_name }}</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ permission.guard_name }}</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ permission.created_at }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-right">
                <button @click="editPermission(permission)" class="text-blue-600 hover:text-blue-800 mr-2" title="Sửa">
                  <PencilIcon class="w-5 h-5 inline" />
                </button>
                <button @click="deletePermission(permission)" class="text-red-600 hover:text-red-800" title="Xóa">
                  <TrashIcon class="w-5 h-5 inline" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </TableLayout>

  <!-- Modal thêm/sửa quyền -->
  <PermissionCreate v-if="showAddModal" @close="closeModal" @saved="fetchPermissions" />
  <PermissionEdit v-if="showEditModal" :permission="editingPermission" @close="closeModal" @saved="fetchPermissions" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import TableLayout from '@/components/TableLayout.vue'
import PermissionCreate from './create.vue'
import PermissionEdit from './edit.vue'
import Filter from './filter.vue'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'

const permissions = ref([])
const filters = ref({ search: '' })
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  total: 0,
  per_page: 10
})
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingPermission = ref(null)

const fetchPermissions = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.current_page, per_page: pagination.value.per_page }
    const res = await axios.get('/api/admin/permissions', { params })
    permissions.value = res.data.data || []
    pagination.value.total = res.data.total
    pagination.value.per_page = res.data.per_page
    pagination.value.current_page = res.data.current_page
  } catch (e) {
    permissions.value = []
    pagination.value.total = 0
    pagination.value.current_page = 1
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  filters.value = { search: '' }
  fetchPermissions()
}

const openAddModal = () => {
  showAddModal.value = true
}
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingPermission.value = null
}
const editPermission = (permission) => {
  editingPermission.value = permission
  showEditModal.value = true
}
const deletePermission = async (permission) => {
  if (confirm(`Bạn có chắc chắn muốn xóa quyền "${permission.name}"?`)) {
    try {
      await axios.delete(`/api/admin/permissions/${permission.id}`)
      fetchPermissions()
    } catch (e) {
      alert('Xóa thất bại!')
    }
  }
}

const onUpdateFilters = (newFilters) => {
  filters.value = newFilters
  pagination.value.current_page = 1
  fetchPermissions()
}

onMounted(fetchPermissions)
</script>