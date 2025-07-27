<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý Zone Mapping</h1>
      <button 
        @click="showAddZone = true" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm Zone mới
      </button>
    </div>
    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Zone</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tỉnh</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Fee</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Time</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="zone in zones" :key="zone.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ zone.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ zone.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Array.isArray(zone.provinces) ? zone.provinces.length : (zone.provinces || '').split(',').length }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ zone.base_fee | currency }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ zone.delivery_time }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="getStatusClass(zone.status)"
              >
                {{ getStatusName(zone.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button 
                @click="openEditZone(zone)" 
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Sửa
              </button>
              <button 
                @click="openDeleteZone(zone)" 
                class="text-red-600 hover:text-red-900"
              >
                Xóa
              </button>
            </td>
          </tr>
          <tr v-if="zones.length === 0">
            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
              Không có dữ liệu
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Phân trang (nếu có) -->
    <!-- Modal thêm/sửa zone -->
    <Modal v-if="showAddZone" v-model="showAddZone" :title="editZoneName ? 'Sửa zone' : 'Thêm zone mới'">
      <form @submit.prevent="submitZone" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tên zone <span class="text-red-500">*</span></label>
          <input v-model="zoneName" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.name }" maxlength="100" />
          <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600">{{ validationErrors.name }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Provinces (phân cách bằng dấu phẩy) <span class="text-red-500">*</span></label>
          <input v-model="zoneProvinces" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.provinces }" />
          <p v-if="validationErrors.provinces" class="mt-1 text-sm text-red-600">{{ validationErrors.provinces }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Base Fee <span class="text-red-500">*</span></label>
          <input v-model="zoneBaseFee" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.base_fee }" />
          <p v-if="validationErrors.base_fee" class="mt-1 text-sm text-red-600">{{ validationErrors.base_fee }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Time <span class="text-red-500">*</span></label>
          <input v-model="zoneDeliveryTime" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.delivery_time }" />
          <p v-if="validationErrors.delivery_time" class="mt-1 text-sm text-red-600">{{ validationErrors.delivery_time }}</p>
        </div>
        <div class="flex justify-end space-x-3 pt-4">
          <button type="button" @click="closeZoneModal" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">Huỷ</button>
          <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">{{ editZoneName ? 'Cập nhật' : 'Thêm mới' }}</button>
        </div>
      </form>
    </Modal>
    <!-- Modal xác nhận xóa zone -->
    <Modal v-if="showDeleteZone" v-model="showDeleteZone" title="Xóa zone">
      <div class="py-4 text-center">
        <p>Bạn muốn xóa zone <b>{{ deleteZoneName }}</b>?</p>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="showDeleteZone = false">Hủy</button>
          <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg" @click="submitDeleteZone">Xóa</button>
        </div>
      </div>
    </Modal>
  </div>
</template>
<script setup>
import { ref, onMounted, computed, reactive } from 'vue'
import api from '@/api/apiClient'
import endpoints from '@/api/endpoints'
import Modal from '@/components/Core/Modal.vue'

const showAddZone = ref(false)
const editZoneName = ref('')
const zoneName = ref('')
const zoneProvinces = ref('')
const zoneBaseFee = ref('')
const zoneDeliveryTime = ref('')
const showDeleteZone = ref(false)
const deleteZoneName = ref('')
const zones = ref([])
const loading = ref(false)
const error = ref('')
const validationErrors = reactive({})
const validationRules = computed(() => ({
  name: [
    { required: 'Tên zone là bắt buộc.' },
    { max: [100, 'Tên zone không được vượt quá 100 ký tự.'] }
  ],
  provinces: [
    { required: 'Danh sách tỉnh là bắt buộc.' }
  ],
  base_fee: [
    { required: 'Base fee là bắt buộc.' },
    { number: 'Base fee phải là số.' }
  ],
  delivery_time: [
    { required: 'Thời gian giao hàng là bắt buộc.' }
  ]
}))
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}
function validateForm() {
  clearErrors()
  let valid = true
  const rules = validationRules.value
  const formData = {
    name: zoneName.value,
    provinces: zoneProvinces.value,
    base_fee: zoneBaseFee.value,
    delivery_time: zoneDeliveryTime.value
  }
  for (const field in rules) {
    for (const rule of rules[field]) {
      if (rule.required && !formData[field]) {
        validationErrors[field] = rule.required
        valid = false
        break
      }
      if (rule.max && formData[field] && formData[field].length > rule.max[0]) {
        validationErrors[field] = rule.max[1]
        valid = false
        break
      }
      if (rule.number && formData[field] && isNaN(Number(formData[field]))) {
        validationErrors[field] = rule.number
        valid = false
        break
      }
    }
  }
  return valid
}

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

async function fetchZones() {
  loading.value = true
  error.value = ''
  try {
    const res = await api.get(endpoints.shippingZones.list)
    zones.value = res.data.data
  } catch (e) {
    error.value = 'Không lấy được danh sách zone!'
  } finally {
    loading.value = false
  }
}
onMounted(fetchZones)

function openEditZone(zone) {
  editZoneName.value = zone.name
  zoneName.value = zone.name
  zoneProvinces.value = Array.isArray(zone.provinces) ? zone.provinces.join(',') : zone.provinces
  zoneBaseFee.value = zone.base_fee
  zoneDeliveryTime.value = zone.delivery_time
  showAddZone.value = true
}
function openDeleteZone(zone) {
  deleteZoneName.value = zone.name
  showDeleteZone.value = true
}
function closeZoneModal() {
  showAddZone.value = false
  editZoneName.value = ''
  clearErrors()
}
async function submitZone() {
  if (!validateForm()) return
  const found = zones.value.find(z => z.name === zoneName.value)
  const data = {
    name: zoneName.value,
    provinces: zoneProvinces.value,
    base_fee: zoneBaseFee.value,
    delivery_time: zoneDeliveryTime.value,
    status: 'active'
  }
  try {
    if (found) {
      await api.put(endpoints.shippingZones.update(found.id), data)
    } else {
      await api.post(endpoints.shippingZones.create, data)
    }
    await fetchZones()
    closeZoneModal()
  } catch (e) {
    error.value = 'Lưu zone thất bại!'
  }
}
async function submitDeleteZone() {
  const found = zones.value.find(z => z.name === deleteZoneName.value)
  if (!found) return
  try {
    await api.delete(endpoints.shippingZones.delete(found.id))
    await fetchZones()
    showDeleteZone.value = false
  } catch (e) {
    error.value = 'Xóa zone thất bại!'
  }
}
</script> 
