<template>
  <CustomSection title="Service Management" description="Quản lý các dịch vụ vận chuyển">
    <!-- Service Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Card dịch vụ mẫu -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Standard Delivery</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between"><span>Base Price:</span><span class="font-medium">15,000đ</span></div>
          <div class="flex justify-between"><span>Weight Multiplier:</span><span class="font-medium">2,000đ/kg</span></div>
          <div class="flex justify-between"><span>Estimated Days:</span><span class="font-medium">2-3 ngày</span></div>
          <div class="flex justify-between"><span>Available Zones:</span><span class="font-medium">63 tỉnh</span></div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openEditService('Standard Delivery')">Edit</button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openToggleService('Standard Delivery', true)">Disable</button>
        </div>
      </div>
      <!-- Card dịch vụ khác ... -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Express Delivery</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between"><span>Base Price:</span><span class="font-medium">25,000đ</span></div>
          <div class="flex justify-between"><span>Weight Multiplier:</span><span class="font-medium">3,000đ/kg</span></div>
          <div class="flex justify-between"><span>Estimated Days:</span><span class="font-medium">1-2 ngày</span></div>
          <div class="flex justify-between"><span>Available Zones:</span><span class="font-medium">45 tỉnh</span></div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openEditService('Express Delivery')">Edit</button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openToggleService('Express Delivery', true)">Disable</button>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Same Day Delivery</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Limited</span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between"><span>Base Price:</span><span class="font-medium">50,000đ</span></div>
          <div class="flex justify-between"><span>Weight Multiplier:</span><span class="font-medium">5,000đ/kg</span></div>
          <div class="flex justify-between"><span>Estimated Days:</span><span class="font-medium">Trong ngày</span></div>
          <div class="flex justify-between"><span>Available Zones:</span><span class="font-medium">5 tỉnh</span></div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openEditService('Same Day Delivery')">Edit</button>
          <button class="px-3 py-1.5 bg-green-500 text-white rounded-lg text-sm hover:bg-green-600 transition" @click="openToggleService('Same Day Delivery', false)">Enable</button>
        </div>
      </div>
    </div>
    <!-- Add New Service -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Thêm dịch vụ mới</h3>
        <button class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition" @click="showAddService = true">
          + Thêm Service
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- ... các card dịch vụ mới ... -->
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 hover:bg-blue-50 transition cursor-pointer">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-900">Next Day</p>
          <p class="text-xs text-gray-500">Giao hàng ngày hôm sau</p>
        </div>
        <!-- ... các card khác ... -->
      </div>
    </div>
    <!-- Modal thêm/sửa dịch vụ -->
    <Modal v-if="showAddService || showEditService" v-model="showAddService" :title="editServiceName ? 'Sửa dịch vụ' : 'Thêm dịch vụ mới'">
      <form @submit.prevent="submitService">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Tên dịch vụ</label>
          <input v-model="serviceName" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Base Price</label>
          <input v-model="serviceBasePrice" type="number" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Weight Multiplier</label>
          <input v-model="serviceWeightFee" type="number" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Estimated Days</label>
          <input v-model="serviceDays" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="closeServiceModal">Hủy</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Lưu</button>
        </div>
      </form>
    </Modal>
    <!-- Modal xác nhận bật/tắt dịch vụ -->
    <Modal v-if="showToggle" v-model="showToggle" :title="toggleEnable ? 'Bật dịch vụ' : 'Tắt dịch vụ'">
      <div class="py-4 text-center">
        <p>Bạn muốn {{ toggleEnable ? 'bật' : 'tắt' }} dịch vụ <b>{{ toggleServiceName }}</b>?</p>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="showToggle = false">Hủy</button>
          <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg" @click="submitToggleService">Xác nhận</button>
        </div>
      </div>
    </Modal>
    <CreateServiceModal v-if="showCreate" @close="showCreate = false" @save="showCreate = false" />
    <EditServiceModal v-if="showEdit" @close="showEdit = false" @save="showEdit = false" />
  </CustomSection>
</template>
<script setup>
import { ref } from 'vue'
import CustomSection from '@/components/CustomSection.vue'
import Modal from '@/components/Modal.vue'
import CreateServiceModal from './create.vue'
import EditServiceModal from './edit.vue'

const showAddService = ref(false)
const showEditService = ref(false)
const editServiceName = ref('')
const serviceName = ref('')
const serviceBasePrice = ref('')
const serviceWeightFee = ref('')
const serviceDays = ref('')
const showToggle = ref(false)
const toggleServiceName = ref('')
const toggleEnable = ref(false)
const showCreate = ref(false)
const showEdit = ref(false)

function openEditService(name) {
  editServiceName.value = name
  serviceName.value = name
  serviceBasePrice.value = ''
  serviceWeightFee.value = ''
  serviceDays.value = ''
  showAddService.value = true
}
function openToggleService(name, disable) {
  toggleServiceName.value = name
  toggleEnable.value = !disable
  showToggle.value = true
}
function closeServiceModal() {
  showAddService.value = false
  showEditService.value = false
  editServiceName.value = ''
}
function submitService() {
  // Xử lý thêm/sửa dịch vụ
  closeServiceModal()
}
function submitToggleService() {
  // Xử lý bật/tắt dịch vụ
  showToggle.value = false
}
function openCreate() { showCreate.value = true }
function openEdit() { showEdit.value = true }
</script> 