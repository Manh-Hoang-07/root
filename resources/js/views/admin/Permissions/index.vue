<template>
  <DataTable
    :is-all-selected="isAllSelected"
    @toggle-select-all="toggleSelectAll"
  >
    <!-- Action bar -->
    <template #actions>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
      >
        <span>Thêm quyền</span>
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
      <th class="px-4 py-3 whitespace-nowrap">Tên quyền</th>
      <th class="px-4 py-3 whitespace-nowrap">Tên quyền hiển thị</th>
      <th class="px-4 py-3 whitespace-nowrap">Guard</th>
      <th class="px-4 py-3 whitespace-nowrap">Quyền cha</th>
      <th class="px-4 py-3 whitespace-nowrap">Ngày tạo</th>
    </template>
    <!-- Table body -->
    <template #tbody>
      <tr v-if="permissions.length === 0">
        <td colspan="7" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
      </tr>
      <tr v-for="permission in permissions" :key="permission.id" class="hover:bg-gray-50">
        <!-- Checkbox -->
        <td class="text-center">
          <input type="checkbox" :checked="selected.includes(permission.id)" @change="toggleSelect(permission.id)" />
        </td>
        <td class="px-4 py-3 whitespace-nowrap">{{ permission.name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ permission.display_name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ permission.guard_name }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ getParentName(permission.parent_id) }}</td>
        <td class="px-4 py-3 whitespace-nowrap">{{ permission.created_at }}</td>
        <!-- Thao tác -->
        <td class="text-center">
          <button @click="editPermission(permission)" class="p-2 rounded-full hover:bg-indigo-100 transition" title="Sửa">
            <PencilIcon class="w-5 h-5 text-indigo-600" />
          </button>
          <button @click="deletePermission(permission)" class="p-2 rounded-full hover:bg-red-100 transition" title="Xóa">
            <TrashIcon class="w-5 h-5 text-red-500" />
          </button>
        </td>
      </tr>
    </template>
    <!-- Pagination -->
    <template #pagination>
      <div class="flex items-center gap-2">
        <button @click="prevPage" :disabled="pagination.current_page === 1" class="px-3 py-1 rounded bg-gray-200">&lt;</button>
        <span>Trang {{ pagination.current_page }}</span>
        <button @click="nextPage" :disabled="pagination.current_page * pagination.per_page >= pagination.total" class="px-3 py-1 rounded bg-gray-200">&gt;</button>
      </div>
    </template>
  </DataTable>

  <!-- Modal thêm/sửa quyền -->
  <PermissionCreate v-if="showAddModal" @close="closeModal" @saved="fetchPermissions" />
  <PermissionEdit v-if="showEditModal" :permission="editingPermission" @close="closeModal" @saved="fetchPermissions" />
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import DataTable from '@/components/DataTable.vue'
import useTableSelection from '@/composables/useTableSelection'
import Filter from './filter.vue'
import PermissionCreate from './create.vue'
import PermissionEdit from './edit.vue'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'

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

const { selected, isAllSelected, toggleSelectAll, toggleSelect } = useTableSelection(permissions)

const fetchPermissions = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.current_page, per_page: pagination.value.per_page }
    const res = await axios.get('/api/admin/permissions', { params })
    permissions.value = res.data.data || []
    pagination.value.total = res.data.meta?.total || 0
    pagination.value.per_page = res.data.meta?.per_page || 10
    pagination.value.current_page = res.data.meta?.current_page || 1
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
    } catch (e) {}
  }
}
const deleteSelected = async () => {
  if (!selected.length) return
  if (confirm('Bạn có chắc chắn muốn xóa các quyền đã chọn?')) {
    try {
      await Promise.all(selected.map(id => axios.delete(`/api/admin/permissions/${id}`)))
      fetchPermissions()
    } catch (e) {}
  }
}
const onUpdateFilters = (newFilters) => {
  filters.value = newFilters
  pagination.value.current_page = 1
  fetchPermissions()
}

// Map id -> quyền để tra nhanh tên quyền cha
const permissionMap = computed(() => {
  const map = {}
  permissions.value.forEach(p => { map[p.id] = p })
  return map
})
function getParentName(parent_id) {
  if (!parent_id) return '—'
  const p = permissionMap.value[parent_id]
  return p ? (p.display_name || p.name) : parent_id
}

const prevPage = () => {
  if (pagination.value.current_page > 1) {
    pagination.value.current_page--
  }
}
const nextPage = () => {
  if (pagination.value.current_page * pagination.value.per_page < pagination.value.total) {
    pagination.value.current_page++
  }
}

watch(() => pagination.value.current_page, () => {
  fetchPermissions()
})

onMounted(fetchPermissions)
</script>

<style>
/* Đảm bảo table luôn cuộn ngang được trên mobile */
.bg-white > .overflow-x-auto {
  width: 100%;
}
</style>