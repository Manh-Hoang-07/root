<template>
  <DataTable
    :is-all-selected="isAllSelected"
    @toggle-select-all="toggleSelectAll"
    :pagination="pagination"
    :loading="loading"
  >
    <!-- Action bar -->
    <template #actions>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
      >
        <span>Thêm vai trò</span>
      </button>
      <button
        @click="deleteSelected"
        :disabled="!selected.length"
        class="ml-2 px-4 py-2 rounded-lg bg-red-500 text-white font-medium disabled:opacity-50"
      >
        Xóa đã chọn
      </button>
    </template>
    <!-- Filter bar -->
    <template #filter>
      <Filter :filters="filters" @update:filters="onUpdateFilters" @clear="clearFilters" />
    </template>
    <!-- Table head -->
    <template #thead>
      <th class="px-4 py-3 whitespace-nowrap">Tên vai trò</th>
      <th class="px-4 py-3 whitespace-nowrap">Tên hiển thị</th>
      <th class="px-4 py-3 whitespace-nowrap">Guard</th>
      <th class="px-4 py-3 whitespace-nowrap">Vai trò cha</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày tạo</th>
    </template>
    <!-- Table body -->
    <template #tbody>
      <tr v-if="roles.length === 0">
        <td colspan="7" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
      </tr>
      <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50">
        <!-- Checkbox -->
        <td class="text-center">
          <input type="checkbox" :checked="selected.includes(role.id)" @change="toggleSelect(role.id)" />
        </td>
        <td class="px-4 py-3 whitespace-nowrap">{{ role.name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ role.display_name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ role.guard_name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ getParentName(role.parent_id) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ role.created_at }}</td>
        <!-- Thao tác -->
        <td class="text-center">
          <button @click="editRole(role)" class="p-2 rounded-full hover:bg-indigo-100 transition" title="Sửa">
            <PencilIcon class="w-5 h-5 text-indigo-600" />
          </button>
          <button @click="deleteRole(role)" class="p-2 rounded-full hover:bg-red-100 transition" title="Xóa">
            <TrashIcon class="w-5 h-5 text-red-500" />
          </button>
        </td>
      </tr>
    </template>
    <!-- Pagination -->
    <template #pagination>
      <div class="flex items-center gap-2">
        <button @click="prevPage" :disabled="pagination.current_page === 1" class="px-3 py-1 rounded bg-gray-200">&lt;</button>
        <span>Trang {{ pagination.current_page }} / {{ totalPages }}</span>
        <button @click="nextPage" :disabled="pagination.current_page === totalPages" class="px-3 py-1 rounded bg-gray-200">&gt;</button>
      </div>
    </template>
  </DataTable>

  <!-- Modal thêm/sửa vai trò -->
  <RoleCreate v-if="showAddModal" @close="closeModal" @saved="fetchRoles" />
  <RoleEdit v-if="showEditModal" :role="editingRole" @close="closeModal" @saved="fetchRoles" />
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import useTableSelection from '@/composables/useTableSelection'
import Filter from '../Permissions/filter.vue'
import RoleCreate from './create.vue'
import RoleEdit from './edit.vue'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'

const roles = ref([])
const filters = ref({ search: '' })
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  total: 0,
  per_page: 10
})
const totalPages = computed(() => Math.ceil(pagination.value.total / pagination.value.per_page) || 1)
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingRole = ref(null)

const { selected, isAllSelected, toggleSelectAll, toggleSelect } = useTableSelection(roles)

const fetchRoles = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.current_page, per_page: pagination.value.per_page }
    const res = await axios.get('/api/admin/roles', { params })
    console.log('API roles:', res.data)
    roles.value = res.data.data || []
    pagination.value.total = res.data.meta?.total || 0
    pagination.value.per_page = res.data.meta?.per_page || 10
    pagination.value.current_page = res.data.meta?.current_page || 1
    console.log('pagination:', pagination.value, 'totalPages:', totalPages.value)
    // Nếu không còn dữ liệu ở trang hiện tại và không phải trang 1, lùi về trang trước
    if (roles.value.length === 0 && pagination.value.current_page > 1) {
      pagination.value.current_page--
      fetchRoles()
    }
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
    } catch (e) {}
  }
}
const deleteSelected = async () => {
  if (!selected.length) return
  if (confirm('Bạn có chắc chắn muốn xóa các vai trò đã chọn?')) {
    try {
      await Promise.all(selected.map(id => axios.delete(`/api/admin/roles/${id}`)))
      fetchRoles()
    } catch (e) {}
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

const goToPage = (page) => {
  if (page < 1 || page > totalPages.value) return
  pagination.value.current_page = page
  fetchRoles()
}
const prevPage = () => goToPage(pagination.value.current_page - 1)
const nextPage = () => goToPage(pagination.value.current_page + 1)

onMounted(fetchRoles)
</script>

<style>
/* Cho phép cuộn ngang table khi màn hình nhỏ */
.table-responsive {
  overflow-x: auto;
}
</style> 