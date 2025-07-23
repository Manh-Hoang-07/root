<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý khuyến mãi vận chuyển</h1>
      <button 
        @click="showAddPromotion = true" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        Thêm khuyến mãi mới
      </button>
    </div>
    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên khuyến mãi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày hết hạn</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="promotion in promotions" :key="promotion.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ promotion.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ promotion.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ promotion.discount }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ promotion.valid_until }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="getStatusClass(promotion.status)"
              >
                {{ getStatusName(promotion.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button 
                @click="openEditPromotion(promotion)" 
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Sửa
              </button>
              <button 
                @click="openTogglePromotion(promotion)" 
                class="text-blue-600 hover:text-blue-900"
              >
                {{ promotion.status === 'active' ? 'Tắt' : 'Bật' }}
              </button>
            </td>
          </tr>
          <tr v-if="promotions.length === 0">
            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
              Không có dữ liệu
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Modal thêm/sửa khuyến mãi -->
    <PromotionForm
      v-if="showAddPromotion || showEditPromotion"
      :show="showAddPromotion"
      :promotion="editingPromotion"
      :api-errors="apiErrors"
      :mode="editPromotionId ? 'edit' : 'create'"
      @submit="handleSubmitPromotion"
      @cancel="closePromotionModal"
    />
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/apiClient'
import endpoints from '@/api/endpoints'
import PromotionForm from './PromotionForm.vue'
import CustomSection from '@/components/CustomSection.vue'
import Modal from '@/components/Modal.vue'
import CreatePromotionModal from './create.vue'
import EditPromotionModal from './edit.vue'

const showAddPromotion = ref(false)
const showEditPromotion = ref(false)
const editPromotionName = ref('')
const promotionName = ref('')
const promotionDiscount = ref('')
const promotionValidUntil = ref('')
const showToggle = ref(false)
const togglePromotionName = ref('')
const toggleEnable = ref(false)
const promotions = ref([])
const loading = ref(false)
const error = ref('')
const editingPromotion = ref(null)
const apiErrors = ref({})

async function fetchPromotions() {
  loading.value = true
  error.value = ''
  try {
    const res = await api.get(endpoints.shippingPromotions.list)
    promotions.value = res.data.data
  } catch (e) {
    error.value = 'Không lấy được danh sách khuyến mãi!'
  } finally {
    loading.value = false
  }
}
onMounted(fetchPromotions)

function openEditPromotion(promotion) {
  editingPromotion.value = { ...promotion }
  showAddPromotion.value = true
  Object.keys(apiErrors.value).forEach(key => delete apiErrors.value[key])
}
function openAddPromotion() {
  editingPromotion.value = null
  showAddPromotion.value = true
  Object.keys(apiErrors.value).forEach(key => delete apiErrors.value[key])
}
function openTogglePromotion(name, disable) {
  togglePromotionName.value = name
  toggleEnable.value = !disable
  showToggle.value = true
}
function closePromotionModal() {
  showAddPromotion.value = false
  editingPromotion.value = null
  Object.keys(apiErrors.value).forEach(key => delete apiErrors.value[key])
}
async function handleSubmitPromotion(formData) {
  try {
    if (editingPromotion.value && editingPromotion.value.id) {
      await api.put(endpoints.shippingPromotions.update(editingPromotion.value.id), formData)
    } else {
      await api.post(endpoints.shippingPromotions.create, formData)
    }
    await fetchPromotions()
    closePromotionModal()
  } catch (error) {
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors
      for (const field in errors) {
        apiErrors.value[field] = errors[field][0]
      }
    }
  }
}
async function submitTogglePromotion() {
  const found = promotions.value.find(p => p.name === togglePromotionName.value)
  if (!found) return
  try {
    await api.put(endpoints.shippingPromotions.update(found.id), { ...found, status: toggleEnable.value ? 'active' : 'inactive' })
    await fetchPromotions()
    showToggle.value = false
  } catch (e) {
    error.value = 'Cập nhật trạng thái thất bại!'
  }
}
const showCreate = ref(false)
const showEdit = ref(false)
function openCreate() { showCreate.value = true }
function openEdit() { showEdit.value = true }
</script> 