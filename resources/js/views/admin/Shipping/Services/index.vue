<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý dịch vụ vận chuyển</h1>
      <button 
        @click="openCreateModal" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm dịch vụ mới
      </button>
    </div>

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên dịch vụ</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã dịch vụ</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Price</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weight Fee</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimated Days</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="service in services" :key="service.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ service.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ service.code }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getProviderName(service.provider_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(service.base_price) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(service.weight_fee) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ service.estimated_days }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="service.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              >
                {{ service.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button 
                @click="openEditModal(service)" 
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Sửa
              </button>
              <button 
                @click="confirmDelete(service)" 
                class="text-red-600 hover:text-red-900"
              >
                Xóa
              </button>
            </td>
          </tr>
          <tr v-if="services.length === 0">
            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
              {{ loading ? 'Đang tải dữ liệu...' : 'Không có dữ liệu' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal thêm mới -->
    <ShippingServiceForm
      v-if="showCreateModal"
      :show="showCreateModal"
      :providers="providers"
      :api-errors="apiErrors"
      :mode="'create'"
      @submit="handleCreateService"
      @cancel="closeModal"
    />

    <!-- Modal chỉnh sửa -->
    <ShippingServiceForm
      v-if="showEditModal"
      :show="showEditModal"
      :service="editingService"
      :providers="providers"
      :api-errors="apiErrors"
      :mode="'edit'"
      @submit="handleEditService"
      @cancel="closeModal"
    />
  </div>
</template>
<script setup>
import { ref, onMounted, reactive } from 'vue'
import ShippingServiceForm from './ShippingServiceForm.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'

const services = ref([])
const providers = ref([])
const loading = ref(false)
const showCreateModal = ref(false)
const showEditModal = ref(false)
const editingService = ref(null)
const apiErrors = reactive({})

onMounted(async () => {
  await fetchServices()
  await fetchProviders()
})

async function fetchServices() {
  loading.value = true
  try {
    const response = await axios.get(endpoints.shippingServices.list)
    services.value = response.data.data
  } finally {
    loading.value = false
  }
}
async function fetchProviders() {
  const response = await axios.get('/api/admin/shipping/api')
  providers.value = response.data.data
}
function getProviderName(id) {
  const found = providers.value.find(p => p.id == id)
  return found ? found.name : ''
}
function formatCurrency(val) {
  if (val == null) return ''
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
}
function openCreateModal() {
  showCreateModal.value = true
  Object.keys(apiErrors).forEach(key => delete apiErrors[key])
}
function openEditModal(service) {
  editingService.value = service
  showEditModal.value = true
  Object.keys(apiErrors).forEach(key => delete apiErrors[key])
}
function closeModal() {
  showCreateModal.value = false
  showEditModal.value = false
  editingService.value = null
}
async function handleCreateService(formData) {
  try {
    await axios.post(endpoints.shippingServices.create, formData)
    await fetchServices()
    closeModal()
  } catch (error) {
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors
      for (const field in errors) {
        apiErrors[field] = errors[field][0]
      }
    }
  }
}
async function handleEditService(formData) {
  try {
    await axios.post(endpoints.shippingServices.update(editingService.value.id), formData)
    await fetchServices()
    closeModal()
  } catch (error) {
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors
      for (const field in errors) {
        apiErrors[field] = errors[field][0]
      }
    }
  }
}
function confirmDelete(service) {
  // Xử lý xóa dịch vụ nếu cần
}
</script> 