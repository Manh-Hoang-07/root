<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý giá trị thuộc tính</h1>
      <button 
        @click="openCreateModal" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm giá trị
      </button>
    </div>

    <!-- Bộ lọc -->
    <AttributeValueFilter
      :initial-filters="filters"
      @update:filters="handleFilterChange"
    />

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thuộc tính</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá trị</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hiển thị</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thứ tự</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="val in attributeValues" :key="val.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ val.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ val.attribute_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ val.value }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ val.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ val.sort_order }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="val.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              >
                {{ val.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button 
                @click="openEditModal(val)" 
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Sửa
              </button>
              <button 
                @click="confirmDelete(val)" 
                class="text-red-600 hover:text-red-900"
              >
                Xóa
              </button>
            </td>
          </tr>
          <tr v-if="attributeValues.length === 0">
            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
              {{ loading ? 'Đang tải dữ liệu...' : 'Không có dữ liệu' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Phân trang -->
    <div v-if="attributeValues.length > 0" class="mt-4 flex justify-between items-center">
      <div class="text-sm text-gray-700">
        Hiển thị {{ pagination.from || 0 }} đến {{ pagination.to || 0 }} trên tổng số {{ pagination.total || 0 }} bản ghi
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
    <CreateAttributeValue
      v-if="showCreateModal"
      :show="showCreateModal"
      :on-close="closeCreateModal"
      @created="handleAttributeValueCreated"
    />

    <!-- Modal chỉnh sửa -->
    <EditAttributeValue
      v-if="showEditModal"
      :show="showEditModal"
      :attribute-value="selectedAttributeValue"
      :on-close="closeEditModal"
      @updated="handleAttributeValueUpdated"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa giá trị ${selectedAttributeValue?.value || ''}?`"
      :on-close="closeDeleteModal"
      @confirm="deleteAttributeValue"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import CreateAttributeValue from './create.vue'
import EditAttributeValue from './edit.vue'
import AttributeValueFilter from './filter.vue'
import ConfirmModal from '@/components/Core/ConfirmModal.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'

// State
const attributeValues = ref([])
const selectedAttributeValue = ref(null)
const pagination = reactive({
  current_page: 1,
  from: 0,
  to: 0,
  total: 0,
  per_page: 10,
  links: []
})
const filters = reactive({
  search: '',
  status: '',
  sort_by: 'created_at_desc'
})
const loading = ref(false)

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

// Fetch data
onMounted(async () => {
  await fetchAttributeValues()
})

async function fetchAttributeValues(page = 1) {
  loading.value = true
  try {
    const response = await axios.get(endpoints.attributeValues.list, {
      params: { 
        page,
        search: filters.search,
        status: filters.status,
        sort_by: filters.sort_by
      }
    })
    attributeValues.value = response.data.data
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
    console.error('Error fetching attribute values:', error)
  } finally {
    loading.value = false
  }
}

// Filter handlers
function handleFilterChange(newFilters) {
  Object.assign(filters, newFilters)
  fetchAttributeValues(1)
}

// Modal handlers
function openCreateModal() {
  showCreateModal.value = true
}

function closeCreateModal() {
  showCreateModal.value = false
}

function openEditModal(attributeValue) {
  selectedAttributeValue.value = attributeValue
  showEditModal.value = true
}

function closeEditModal() {
  showEditModal.value = false
  selectedAttributeValue.value = null
}

function confirmDelete(attributeValue) {
  selectedAttributeValue.value = attributeValue
  showDeleteModal.value = true
}

function closeDeleteModal() {
  showDeleteModal.value = false
  selectedAttributeValue.value = null
}

// Action handlers
async function handleAttributeValueCreated() {
  await fetchAttributeValues()
  closeCreateModal()
}

async function handleAttributeValueUpdated() {
  await fetchAttributeValues()
  closeEditModal()
}

async function deleteAttributeValue() {
  try {
    await axios.delete(endpoints.attributeValues.delete(selectedAttributeValue.value.id))
    await fetchAttributeValues()
    closeDeleteModal()
  } catch (error) {
    console.error('Error deleting attribute value:', error)
  }
}

function changePage(url) {
  if (!url) return
  const urlObj = new URL(url)
  const page = urlObj.searchParams.get('page')
  fetchAttributeValues(page)
}
</script>

<style scoped>
</style> 
