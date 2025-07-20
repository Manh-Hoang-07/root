<template>
  <CustomSection title="Service Management" description="Quản lý các dịch vụ vận chuyển">
    <!-- Service Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <div v-for="service in services" :key="service.id" class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">{{ service.name }}</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium"
            :class="service.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'">
            {{ service.status === 'active' ? 'Active' : 'Inactive' }}
          </span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between"><span>Mã dịch vụ:</span><span class="font-medium">{{ service.code }}</span></div>
          <div class="flex justify-between"><span>Provider ID:</span><span class="font-medium">{{ service.provider_id }}</span></div>
          <div class="flex justify-between"><span>Base Price:</span><span class="font-medium">{{ Number(service.base_price).toLocaleString() }}đ</span></div>
          <div class="flex justify-between"><span>Weight Fee:</span><span class="font-medium">{{ Number(service.weight_fee).toLocaleString() }}đ/kg</span></div>
          <div class="flex justify-between"><span>Estimated Days:</span><span class="font-medium">{{ service.estimated_days }}</span></div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openEditService(service.name)">Edit</button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openToggleService(service.name, service.status !== 'active')">
            {{ service.status === 'active' ? 'Disable' : 'Enable' }}
          </button>
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
          <label class="block text-sm font-medium mb-1">Mã dịch vụ (code)</label>
          <input v-model="serviceCode" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Provider</label>
          <select v-model="serviceProviderId" class="w-full px-4 py-2 border rounded-xl" required>
            <option v-for="option in providerOptions" :key="option.value" :value="option.value">{{ option.name }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Base Price</label>
          <input v-model="serviceBasePrice" type="number" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Weight Fee</label>
          <input v-model="serviceWeightFee" type="number" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Estimated Days</label>
          <input v-model="serviceDays" type="number" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Ảnh dịch vụ</label>
          <ImageUploader v-model="serviceImage" />
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
import { ref, onMounted } from 'vue'
import api from '@/api/apiClient'
import endpoints from '@/api/endpoints'
import CustomSection from '@/components/CustomSection.vue'
import Modal from '@/components/Modal.vue'
import CreateServiceModal from './create.vue'
import EditServiceModal from './edit.vue'
import ImageUploader from '@/components/ImageUploader.vue'
import useApiOptions from '@/composables/useApiOptions.js'

const showAddService = ref(false)
const showEditService = ref(false)
const editServiceName = ref('')
const serviceName = ref('')
const serviceCode = ref('')
const serviceProviderId = ref('')
const serviceBasePrice = ref('')
const serviceWeightFee = ref('')
const serviceDays = ref('')
const serviceImage = ref('')
const showToggle = ref(false)
const toggleServiceName = ref('')
const toggleEnable = ref(false)
const showCreate = ref(false)
const showEdit = ref(false)
const services = ref([])
const loading = ref(false)
const error = ref('')
const { options: providerOptions, loading: loadingProviders } = useApiOptions('/api/admin/shipping/api')

async function fetchServices() {
  loading.value = true
  error.value = ''
  try {
    const res = await api.get(endpoints.shippingServices.list)
    services.value = res.data.data
  } catch (e) {
    error.value = 'Không lấy được danh sách dịch vụ!'
  } finally {
    loading.value = false
  }
}
onMounted(fetchServices)

function openEditService(name) {
  const found = services.value.find(s => s.name === name)
  editServiceName.value = name
  serviceName.value = found ? found.name : name
  serviceCode.value = found ? found.code : ''
  serviceProviderId.value = found ? found.provider_id : ''
  serviceBasePrice.value = found ? found.base_price : ''
  serviceWeightFee.value = found ? found.weight_fee : ''
  serviceDays.value = found ? found.estimated_days : ''
  serviceImage.value = found ? found.image : ''
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
async function submitService() {
  const found = services.value.find(s => s.name === serviceName.value)
  const data = {
    name: serviceName.value,
    code: serviceCode.value,
    provider_id: serviceProviderId.value,
    base_price: serviceBasePrice.value,
    weight_fee: serviceWeightFee.value,
    estimated_days: serviceDays.value,
    image: serviceImage.value,
    status: 'active'
  }
  try {
    if (found) {
      await api.put(endpoints.shippingServices.update(found.id), data)
    } else {
      await api.post(endpoints.shippingServices.create, data)
    }
    await fetchServices()
    closeServiceModal()
  } catch (e) {
    error.value = 'Lưu dịch vụ thất bại!'
  }
}
async function submitToggleService() {
  const found = services.value.find(s => s.name === toggleServiceName.value)
  if (!found) return
  try {
    await api.patch(endpoints.shippingServices.changeStatus(found.id), { status: toggleEnable.value ? 'active' : 'inactive' })
    await fetchServices()
    showToggle.value = false
  } catch (e) {
    error.value = 'Cập nhật trạng thái thất bại!'
  }
}
function openCreate() { showCreate.value = true }
function openEdit() { showEdit.value = true }
</script> 