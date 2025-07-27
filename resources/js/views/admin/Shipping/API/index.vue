<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý API Provider</h1>
      <button 
        @click="showAddProvider = true" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm Provider mới
      </button>
    </div>
    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Provider</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">API Key</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Môi trường</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="provider in providers" :key="provider.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ provider.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ provider.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ provider.api_key }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ provider.env }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="getStatusClass(provider.status)"
              >
                {{ getStatusName(provider.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button 
                @click="openConfigModal(provider.name)" 
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Sửa
              </button>
              <button 
                @click="openTestModal(provider.name)" 
                class="text-blue-600 hover:text-blue-900"
              >
                Test
              </button>
            </td>
          </tr>
          <tr v-if="providers.length === 0">
            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
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
    <ApiProviderForm
      v-if="showAddProvider"
      :show="showAddProvider"
      :api-errors="apiErrors"
      :mode="'create'"
      @submit="handleAddProvider"
      @cancel="() => showAddProvider = false"
    />
    <!-- Modal chỉnh sửa -->
    <ApiProviderForm
      v-if="showConfig"
      :show="showConfig"
      :provider="editingProvider"
      :api-errors="apiErrors"
      :mode="'edit'"
      @submit="handleEditProvider"
      @cancel="() => showConfig = false"
    />
    <!-- Modal test API -->
    <Modal v-if="showTest" v-model="showTest" :title="'Test API ' + testProvider">
      <div class="py-4 text-center">
        <p>Bạn muốn test kết nối API với <b>{{ testProvider }}</b>?</p>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="showTest = false">Hủy</button>
          <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg" @click="submitTest">Test</button>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import api from '@/api/apiClient'
import endpoints from '@/api/endpoints'
import CustomSection from '@/components/Layout/CustomSection.vue'
import Modal from '@/components/Core/Modal.vue'
import EditApiModal from './edit.vue'
import ApiProviderForm from './ApiProviderForm.vue'

const showConfig = ref(false)
const showTest = ref(false)
const showAddProvider = ref(false)
const showEdit = ref(false)
const configProvider = ref('')
const testProvider = ref('')
const apiKey = ref('')
const secretKey = ref('')
const env = ref('production')
const newProviderName = ref('')
const newProviderApiKey = ref('')
const newProviderSecretKey = ref('')
const newProviderEnv = ref('production')
const newProviderStatus = ref('active')
const providerName = ref('')
const status = ref('active')
const providers = ref([])
const loading = ref(false)
const error = ref('')
const validationErrors = reactive({})
const apiErrors = reactive({})
const editingProvider = ref(null)

const pagination = reactive({
  current_page: 1,
  from: 0,
  to: 0,
  total: 0,
  per_page: 10,
  links: []
})

function getStatusName(status) {
  if (status === 'active' || status === 1) return 'Đang hoạt động'
  if (status === 'inactive' || status === 0) return 'Không hoạt động'
  return status
}
function getStatusClass(status) {
  if (status === 'active' || status === 1) return 'bg-green-100 text-green-800'
  if (status === 'inactive' || status === 0) return 'bg-gray-100 text-gray-800'
  return 'bg-gray-100 text-gray-800'
}

async function fetchProviders(page = 1) {
  loading.value = true
  error.value = ''
  try {
    const response = await api.get(endpoints.shippingApi.list, { params: { page } })
    providers.value = response.data.data
    // Update pagination
    const meta = response.data.meta
    if (meta) {
      pagination.current_page = meta.current_page
      pagination.from = meta.from
      pagination.to = meta.to
      pagination.total = meta.total
      pagination.per_page = meta.per_page
      pagination.links = meta.links
    } else {
      pagination.current_page = 1
      pagination.from = 0
      pagination.to = 0
      pagination.total = 0
      pagination.per_page = 10
      pagination.links = []
    }
  } catch (e) {
    error.value = 'Không lấy được danh sách provider!'
  } finally {
    loading.value = false
  }
}

onMounted(fetchProviders)

function changePage(url) {
  if (!url) return
  const urlObj = new URL(url, window.location.origin)
  const page = urlObj.searchParams.get('page')
  fetchProviders(page)
}

function openConfigModal(providerName) {
  const found = providers.value.find(p => p.name === providerName)
  editingProvider.value = found ? { ...found } : null
  showConfig.value = true
  Object.keys(apiErrors).forEach(key => delete apiErrors[key])
}
function openTestModal(provider) {
  testProvider.value = provider
  showTest.value = true
}
function clearValidationErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}

function validateProviderForm(name, apiKey, secretKey) {
  clearValidationErrors()
  if (!name.trim()) validationErrors.providerName = 'Tên Provider là bắt buộc'
  else if (name.length > 100) validationErrors.providerName = 'Tên Provider không được vượt quá 100 ký tự'
  if (!apiKey.trim()) validationErrors.apiKey = 'API Key là bắt buộc'
  else if (apiKey.length > 100) validationErrors.apiKey = 'API Key không được vượt quá 100 ký tự'
  if (!secretKey.trim()) validationErrors.secretKey = 'Secret Key là bắt buộc'
  else if (secretKey.length > 100) validationErrors.secretKey = 'Secret Key không được vượt quá 100 ký tự'
  return Object.keys(validationErrors).length === 0
}
function validateNewProviderForm(name, apiKey, secretKey) {
  clearValidationErrors()
  if (!name.trim()) validationErrors.newProviderName = 'Tên Provider là bắt buộc'
  else if (name.length > 100) validationErrors.newProviderName = 'Tên Provider không được vượt quá 100 ký tự'
  if (!apiKey.trim()) validationErrors.newProviderApiKey = 'API Key là bắt buộc'
  else if (apiKey.length > 100) validationErrors.newProviderApiKey = 'API Key không được vượt quá 100 ký tự'
  if (!secretKey.trim()) validationErrors.newProviderSecretKey = 'Secret Key là bắt buộc'
  else if (secretKey.length > 100) validationErrors.newProviderSecretKey = 'Secret Key không được vượt quá 100 ký tự'
  return Object.keys(validationErrors).length === 0
}
async function submitConfig() {
  if (!validateProviderForm(providerName.value, apiKey.value, secretKey.value)) return
  // Nếu provider đã tồn tại thì update, chưa có thì tạo mới
  const found = providers.value.find(p => p.name === providerName.value)
  const data = {
    name: providerName.value,
    api_key: apiKey.value,
    secret_key: secretKey.value,
    env: env.value,
    status: status.value
  }
  try {
    if (found) {
      await api.put(endpoints.shippingApi.update(found.id), data)
    } else {
      await api.post(endpoints.shippingApi.create, data)
    }
    await fetchProviders()
    showConfig.value = false
  } catch (e) {
    error.value = 'Lưu cấu hình thất bại!'
  }
}
async function submitAddProvider() {
  if (!validateNewProviderForm(newProviderName.value, newProviderApiKey.value, newProviderSecretKey.value)) return
  try {
    await api.post(endpoints.shippingApi.create, {
      name: newProviderName.value,
      api_key: newProviderApiKey.value,
      secret_key: newProviderSecretKey.value,
      env: newProviderEnv.value,
      status: newProviderStatus.value
    })
    await fetchProviders()
    showAddProvider.value = false
  } catch (e) {
    error.value = 'Thêm provider thất bại!'
  }
}
async function submitTest() {
  // Nếu backend có API test thì gọi ở đây, tạm thời chỉ đóng modal
  showTest.value = false
}
function handleEditProvider(formData) {
  // Gọi API update provider, xử lý lỗi, đóng modal nếu thành công
}
function handleAddProvider(formData) {
  // Gọi API tạo provider, xử lý lỗi, đóng modal nếu thành công
}
function openEdit() { showEdit.value = true }
</script> 
