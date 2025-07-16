<template>
  <TableLayout :loading="loading" :pagination="pagination" :has-data="roles.length > 0" @page-change="fetchRoles">
    <template #header>
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Quản lý vai trò</h1>
          <p class="text-gray-600">Quản lý các vai trò truy cập hệ thống</p>
        </div>
        <button
          @click="openAddModal"
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
        >
          <span>Thêm vai trò</span>
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
              <th class="px-4 py-3 whitespace-nowrap">Tên vai trò</th>
              <th class="px-4 py-3 whitespace-nowrap">Tên hiển thị</th>
              <th class="px-4 py-3 whitespace-nowrap">Guard</th>
              <th class="px-4 py-3 whitespace-nowrap">Vai trò cha</th>
              <th class="px-4 py-3 whitespace-nowrap">Ngày tạo</th>
              <th class="px-4 py-3 whitespace-nowrap">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="roles.length === 0">
              <td colspan="6" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
            </tr>
            <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 whitespace-nowrap">{{ role.name }}</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ role.display_name }}</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ role.guard_name }}</td>
              <td class="px-4 py-3 whitespace-nowrap">
                {{ getParentName(role.parent_id) }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap">{{ role.created_at }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-right">
                <button @click="editRole(role)" class="text-blue-600 hover:text-blue-800 mr-2" title="Sửa">
                  <PencilIcon class="w-5 h-5 inline" />
                </button>
                <button @click="deleteRole(role)" class="text-red-600 hover:text-red-800" title="Xóa">
                  <TrashIcon class="w-5 h-5 inline" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </TableLayout>

  <!-- Modal thêm/sửa vai trò -->
  <RoleCreate v-if="showAddModal" @close="closeModal" @saved="fetchRoles" />
  <RoleEdit v-if="showEditModal" :role="editingRole" @close="closeModal" @saved="fetchRoles" />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import TableLayout from '@/components/TableLayout.vue'
import RoleCreate from './create.vue'
import RoleEdit from './edit.vue'
import Filter from '../Permissions/filter.vue'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'

const roles = ref([])
const filters = ref({ search: '' })
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  total: 0,
  per_page: 10
})
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingRole = ref(null)

const fetchRoles = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.current_page, per_page: pagination.value.per_page }
    const res = await axios.get('/api/admin/roles', { params })
    roles.value = res.data.data || []
    pagination.value.total = res.data.total
    pagination.value.per_page = res.data.per_page
    pagination.value.current_page = res.data.current_page
  } catch (e) {
    roles.value = []
    pagination.value.total = 0
    pagination.value.current_page = 1
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  filters.value = { search: '' }
  fetchRoles()
}

const openAddModal = () => {
  showAddModal.value = true
}
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingRole.value = null
}
const editRole = (role) => {
  editingRole.value = role
  showEditModal.value = true
}
const deleteRole = async (role) => {
  if (confirm(`Bạn có chắc chắn muốn xóa vai trò "${role.name}"?`)) {
    try {
      await axios.delete(`/api/admin/roles/${role.id}`)
      fetchRoles()
    } catch (e) {
      alert('Xóa thất bại!')
    }
  }
}

const onUpdateFilters = (newFilters) => {
  filters.value = newFilters
  pagination.value.current_page = 1
  fetchRoles()
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

onMounted(fetchRoles)
</script> 