<template>
  <CustomSection title="API Integration" description="Cấu hình tích hợp với các đơn vị vận chuyển">
    <!-- Provider Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <div v-for="provider in providers" :key="provider.id" class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">{{ provider.name }}</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium"
            :class="provider.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'">
            {{ provider.status === 'active' ? 'Đang hoạt động' : 'Không hoạt động' }}
          </span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between">
            <span>API Key:</span>
            <span>{{ provider.api_key }}</span>
          </div>
          <div class="flex justify-between">
            <span>Môi trường:</span>
            <span>{{ provider.env }}</span>
          </div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openTestModal(provider.name)">
            Test
          </button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openConfigModal(provider.name)">
            Cấu hình
          </button>
        </div>
      </div>
    </div>

    <!-- Add New Provider -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Thêm đơn vị vận chuyển mới</h3>
        <button class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition" @click="showAddProvider = true">
          + Thêm Provider
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 hover:bg-blue-50 transition cursor-pointer">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-900">VNPost</p>
          <p class="text-xs text-gray-500">Bưu điện Việt Nam</p>
        </div>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 hover:bg-blue-50 transition cursor-pointer">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-900">DHL</p>
          <p class="text-xs text-gray-500">DHL Express</p>
        </div>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 hover:bg-blue-50 transition cursor-pointer">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-900">FedEx</p>
          <p class="text-xs text-gray-500">Federal Express</p>
        </div>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 hover:bg-blue-50 transition cursor-pointer">
          <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-900">Custom</p>
          <p class="text-xs text-gray-500">Tùy chỉnh</p>
        </div>
      </div>
    </div>

    <!-- Modal cấu hình API (edit) -->
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

    <!-- Modal thêm provider mới -->
    <ApiProviderForm
      v-if="showAddProvider"
      :show="showAddProvider"
      :api-errors="apiErrors"
      :mode="'create'"
      @submit="handleAddProvider"
      @cancel="() => showAddProvider = false"
    />
    <EditApiModal v-if="showEdit" @close="showEdit = false" @save="showEdit = false" />
  </CustomSection>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import api from '@/api/apiClient'
import endpoints from '@/api/endpoints'
import CustomSection from '@/components/CustomSection.vue'
import Modal from '@/components/Modal.vue'
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

async function fetchProviders() {
  loading.value = true
  error.value = ''
  try {
    const res = await api.get(endpoints.shippingApi.list)
    providers.value = res.data.data
  } catch (e) {
    error.value = 'Không lấy được danh sách provider!'
  } finally {
    loading.value = false
  }
}

onMounted(fetchProviders)

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