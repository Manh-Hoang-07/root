<template>
  <CustomSection title="Pricing Rules" description="Quản lý quy tắc tính phí vận chuyển">
    <!-- Markup Settings -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Markup Settings</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Percentage Markup</label>
            <div class="flex items-center space-x-2">
              <input type="number" value="10" class="flex-1 px-3 py-2 border rounded-lg" />
              <span class="text-gray-500">%</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Thêm % vào phí API</p>
          </div>
        </div>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fixed Amount</label>
            <div class="flex items-center space-x-2">
              <input type="number" value="5000" class="flex-1 px-3 py-2 border rounded-lg" />
              <span class="text-gray-500">đ</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Thêm số tiền cố định</p>
          </div>
        </div>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Fee</label>
            <div class="flex items-center space-x-2">
              <input type="number" value="15000" class="flex-1 px-3 py-2 border rounded-lg" />
              <span class="text-gray-500">đ</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Phí tối thiểu</p>
          </div>
        </div>
      </div>
      <div class="mt-6 flex justify-end">
        <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition" @click="showEditPricing = true">
          Sửa quy tắc
        </button>
      </div>
    </div>
    <!-- Modal thêm/sửa quy tắc -->
    <Modal v-if="showEditPricing" v-model="showEditPricing" title="Sửa quy tắc tính phí">
      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Dịch vụ</label>
          <select v-model="serviceId" class="w-full px-4 py-2 border rounded-xl" required>
            <option v-for="service in services" :key="service.id" :value="service.id">{{ service.name }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Zone</label>
          <select v-model="zoneId" class="w-full px-4 py-2 border rounded-xl" required>
            <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Khối lượng tối thiểu (kg)</label>
          <input v-model="minWeight" type="number" class="w-full px-4 py-2 border rounded-xl" />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Khối lượng tối đa (kg)</label>
          <input v-model="maxWeight" type="number" class="w-full px-4 py-2 border rounded-xl" />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Tăng giá theo %</label>
          <input v-model="markupPercent" type="number" class="w-full px-4 py-2 border rounded-xl" />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Tăng giá cố định (VNĐ)</label>
          <input v-model="markupFixed" type="number" class="w-full px-4 py-2 border rounded-xl" />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Phí tối thiểu (VNĐ)</label>
          <input v-model="minFee" type="number" class="w-full px-4 py-2 border rounded-xl" />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Trạng thái</label>
          <select v-model="status" class="w-full px-4 py-2 border rounded-xl" required>
            <option value="active">Hoạt động</option>
            <option value="inactive">Không hoạt động</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="closeModal">Hủy</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Lưu</button>
        </div>
      </form>
    </Modal>

    <!-- Weight Rules -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Weight Rules</h3>
        <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition">
          + Add Rule
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left font-medium text-gray-900">Weight Range</th>
              <th class="px-4 py-3 text-left font-medium text-gray-900">Additional Fee</th>
              <th class="px-4 py-3 text-left font-medium text-gray-900">Max Weight</th>
              <th class="px-4 py-3 text-left font-medium text-gray-900">Status</th>
              <th class="px-4 py-3 text-left font-medium text-gray-900">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">0 - 1 kg</td>
              <td class="px-4 py-3">0đ</td>
              <td class="px-4 py-3">1 kg</td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Active</span>
              </td>
              <td class="px-4 py-3">
                <button class="text-blue-600 hover:text-blue-800 text-sm mr-2">Edit</button>
                <button class="text-red-600 hover:text-red-800 text-sm">Delete</button>
              </td>
            </tr>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">1 - 3 kg</td>
              <td class="px-4 py-3">2,000đ/kg</td>
              <td class="px-4 py-3">3 kg</td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Active</span>
              </td>
              <td class="px-4 py-3">
                <button class="text-blue-600 hover:text-blue-800 text-sm mr-2">Edit</button>
                <button class="text-red-600 hover:text-red-800 text-sm">Delete</button>
              </td>
            </tr>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">3 - 10 kg</td>
              <td class="px-4 py-3">3,000đ/kg</td>
              <td class="px-4 py-3">10 kg</td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Active</span>
              </td>
              <td class="px-4 py-3">
                <button class="text-blue-600 hover:text-blue-800 text-sm mr-2">Edit</button>
                <button class="text-red-600 hover:text-red-800 text-sm">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Distance Rules -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Distance Rules</h3>
        <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition">
          + Add Rule
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <h4 class="font-medium text-gray-900">Distance Tiers</h4>
          <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span class="text-sm">Local (0-50km)</span>
              <span class="text-sm font-medium">+0đ</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span class="text-sm">Regional (50-200km)</span>
              <span class="text-sm font-medium">+5,000đ</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span class="text-sm">National (200km+)</span>
              <span class="text-sm font-medium">+10,000đ</span>
            </div>
          </div>
        </div>
        <div class="space-y-4">
          <h4 class="font-medium text-gray-900">Remote Area Fees</h4>
          <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
              <span class="text-sm">Islands</span>
              <span class="text-sm font-medium">+15,000đ</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
              <span class="text-sm">Mountainous</span>
              <span class="text-sm font-medium">+8,000đ</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
              <span class="text-sm">Rural Areas</span>
              <span class="text-sm font-medium">+3,000đ</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </CustomSection>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/apiClient'
import endpoints from '@/api/endpoints'
import CustomSection from '@/components/CustomSection.vue'
import Modal from '@/components/Modal.vue'

const showEditPricing = ref(false)
const serviceId = ref('')
const zoneId = ref('')
const minWeight = ref('')
const maxWeight = ref('')
const markupPercent = ref('')
const markupFixed = ref('')
const minFee = ref('')
const status = ref('active')
const pricingRules = ref([])
const loading = ref(false)
const error = ref('')
const services = ref([])
const zones = ref([])

async function fetchPricing() {
  loading.value = true
  error.value = ''
  try {
    const res = await api.get(endpoints.shippingPricing.list)
    pricingRules.value = res.data.data
  } catch (e) {
    error.value = 'Không lấy được danh sách quy tắc!'
  } finally {
    loading.value = false
  }
}
async function fetchServicesZones() {
  try {
    const res1 = await api.get(endpoints.shippingServices.list)
    services.value = res1.data.data
    const res2 = await api.get(endpoints.shippingZones.list)
    zones.value = res2.data.data
  } catch {}
}
onMounted(() => { fetchPricing(); fetchServicesZones(); })

function closeModal() { showEditPricing.value = false }
async function handleSubmit() {
  const data = {
    service_id: serviceId.value,
    zone_id: zoneId.value,
    min_weight: minWeight.value,
    max_weight: maxWeight.value,
    markup_percent: markupPercent.value,
    markup_fixed: markupFixed.value,
    min_fee: minFee.value,
    status: status.value
  }
  try {
    await api.post(endpoints.shippingPricing.create, data)
    await fetchPricing()
    closeModal()
  } catch (e) {
    error.value = 'Lưu quy tắc thất bại!'
  }
}
</script> 