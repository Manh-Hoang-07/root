<template>
  <CustomSection title="API Integration" description="Cấu hình tích hợp với các đơn vị vận chuyển">
    <button class="mb-4 px-4 py-2 bg-gray-600 text-white rounded-lg" @click="openEdit">Sửa cấu hình API</button>
    <!-- Provider Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <!-- GHTK Card -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">GHTK</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
            Đang hoạt động
          </span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between">
            <span>API Status:</span>
            <span class="text-green-600">✅ Connected</span>
          </div>
          <div class="flex justify-between">
            <span>Last Test:</span>
            <span>2 phút trước</span>
          </div>
          <div class="flex justify-between">
            <span>Services:</span>
            <span>3 active</span>
          </div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openTestModal('GHTK')">
            Test
          </button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openConfigModal('GHTK')">
            Cấu hình
          </button>
        </div>
      </div>
      <!-- GHN Card -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Giao Hàng Nhanh</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
            Cần cấu hình
          </span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between">
            <span>API Status:</span>
            <span class="text-red-600">❌ Not Connected</span>
          </div>
          <div class="flex justify-between">
            <span>Last Test:</span>
            <span>Chưa test</span>
          </div>
          <div class="flex justify-between">
            <span>Services:</span>
            <span>0 active</span>
          </div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openTestModal('GHN')">
            Test
          </button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openConfigModal('GHN')">
            Cấu hình
          </button>
        </div>
      </div>
      <!-- ViettelPost Card -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">ViettelPost</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
            Chưa kích hoạt
          </span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between">
            <span>API Status:</span>
            <span class="text-gray-600">⚪ Disabled</span>
          </div>
          <div class="flex justify-between">
            <span>Last Test:</span>
            <span>Chưa test</span>
          </div>
          <div class="flex justify-between">
            <span>Services:</span>
            <span>0 active</span>
          </div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-green-500 text-white rounded-lg text-sm hover:bg-green-600 transition" @click="openConfigModal('ViettelPost')">
            Kích hoạt
          </button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openConfigModal('ViettelPost')">
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

    <!-- API Logs -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">API Logs</h3>
        <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition">
          Xem tất cả
        </button>
      </div>
      <div class="space-y-3">
        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
          <div class="flex items-center space-x-3">
            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
            <span class="text-sm text-gray-900">GHTK API test successful</span>
          </div>
          <span class="text-xs text-gray-500">2 phút trước</span>
        </div>
        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
          <div class="flex items-center space-x-3">
            <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
            <span class="text-sm text-gray-900">GHN API key expired</span>
          </div>
          <span class="text-xs text-gray-500">1 giờ trước</span>
        </div>
        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
          <div class="flex items-center space-x-3">
            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
            <span class="text-sm text-gray-900">ViettelPost connection failed</span>
          </div>
          <span class="text-xs text-gray-500">3 giờ trước</span>
        </div>
      </div>
    </div>

    <!-- Modal cấu hình API -->
    <Modal v-if="showConfig" v-model="showConfig" :title="'Cấu hình API ' + configProvider">
      <form @submit.prevent="submitConfig">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Tên Provider</label>
          <input v-model="providerName" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">API Key</label>
          <input v-model="apiKey" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Secret Key</label>
          <input v-model="secretKey" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Môi trường</label>
          <select v-model="env" class="w-full px-4 py-2 border rounded-xl">
            <option value="production">Production</option>
            <option value="test">Test</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Trạng thái</label>
          <select v-model="status" class="w-full px-4 py-2 border rounded-xl">
            <option value="active">Hoạt động</option>
            <option value="inactive">Không hoạt động</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="showConfig = false">Hủy</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Lưu</button>
        </div>
      </form>
    </Modal>

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
    <Modal v-if="showAddProvider" v-model="showAddProvider" title="Thêm đơn vị vận chuyển mới">
      <form @submit.prevent="submitAddProvider">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Tên Provider</label>
          <input v-model="newProviderName" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">API Key</label>
          <input v-model="newProviderApiKey" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Secret Key</label>
          <input v-model="newProviderSecretKey" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Môi trường</label>
          <select v-model="newProviderEnv" class="w-full px-4 py-2 border rounded-xl">
            <option value="production">Production</option>
            <option value="test">Test</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Trạng thái</label>
          <select v-model="newProviderStatus" class="w-full px-4 py-2 border rounded-xl">
            <option value="active">Hoạt động</option>
            <option value="inactive">Không hoạt động</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="showAddProvider = false">Hủy</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Thêm</button>
        </div>
      </form>
    </Modal>
    <EditApiModal v-if="showEdit" @close="showEdit = false" @save="showEdit = false" />
  </CustomSection>
</template>

<script setup>
import { ref } from 'vue'
import CustomSection from '@/components/CustomSection.vue'
import Modal from '@/components/Modal.vue'
import EditApiModal from './edit.vue'

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
const providerName = ref('')
const status = ref('active')

function openConfigModal(provider) {
  configProvider.value = provider
  showConfig.value = true
}
function openTestModal(provider) {
  testProvider.value = provider
  showTest.value = true
}
function submitConfig() {
  // Xử lý lưu cấu hình API
  showConfig.value = false
}
function submitTest() {
  // Xử lý test API
  showTest.value = false
}
function submitAddProvider() {
  // Xử lý thêm provider mới
  showAddProvider.value = false
}
function openEdit() { showEdit.value = true }
</script> 