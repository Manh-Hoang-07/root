<template>
  <DataTable
    :is-all-selected="isAllSelected"
    @toggle-select-all="toggleSelectAll"
  >
    <!-- Action bar -->
    <template #actions>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-3 py-1.5 rounded font-medium hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow flex items-center space-x-1.5 text-sm"
      >
        <span>Thêm quyền</span>
      </button>
      <button
        @click="deleteSelected"
        :disabled="!selected.length"
        class="ml-1 px-3 py-1.5 rounded bg-red-500 text-white font-medium disabled:opacity-50 text-sm"
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
      <Pagination
        :current-page="pagination.currentPage"
        :total-pages="pagination.totalPages"
        :page-size="pagination.itemsPerPage"
        :total-items="pagination.totalItems"
        :loading="loading"
        @page-change="onPageChange"
      />
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
import Pagination from '@/components/Pagination.vue'
import useSyncQueryPagination from '@/composables/useSyncQueryPagination'

const permissions = ref([])
const filters = ref({ search: '' })
const loading = ref(false)
const pagination = ref({
  currentPage: 1,
  totalPages: 1,
  totalItems: 0,
  itemsPerPage: 10
})
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingPermission = ref(null)

const { selected, isAllSelected, toggleSelectAll, toggleSelect } = useTableSelection(permissions)

const fetchPermissions = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.currentPage, per_page: pagination.value.itemsPerPage }
    const res = await axios.get('/api/admin/permissions', { params })
    permissions.value = res.data.data || []
    pagination.value.totalItems = res.data.meta?.total || 0
    pagination.value.itemsPerPage = res.data.meta?.per_page || 10
    pagination.value.currentPage = res.data.meta?.current_page || 1
    pagination.value.totalPages = Math.ceil(pagination.value.totalItems / pagination.value.itemsPerPage) || 1
  } catch (e) {
    permissions.value = []
    pagination.value.totalItems = 0
    pagination.value.itemsPerPage = 10
    pagination.value.currentPage = 1
    pagination.value.totalPages = 1
  } finally {
    loading.value = false
  }
}

const { onPageChange, onUpdateFilters } = useSyncQueryPagination(filters, pagination, fetchPermissions, ['search'])

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

watch(() => pagination.value.currentPage, () => {
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