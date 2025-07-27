<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="validateAndSubmit" class="space-y-4">
      <!-- Khách hàng -->
      <div>
        <label class="block text-sm font-medium mb-1">Khách hàng <span class="text-red-500">*</span></label>
        <input v-model="formData.customer_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.customer_name || apiErrors.customer_name }" maxlength="100" />
        <p v-if="validationErrors.customer_name" class="mt-1 text-sm text-red-600">{{ validationErrors.customer_name }}</p>
        <p v-else-if="apiErrors.customer_name" class="mt-1 text-sm text-red-600">{{ apiErrors.customer_name }}</p>
      </div>
      <!-- Tổng tiền -->
      <div>
        <label class="block text-sm font-medium mb-1">Tổng tiền <span class="text-red-500">*</span></label>
        <input v-model="formData.total_amount" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.total_amount || apiErrors.total_amount }" />
        <p v-if="validationErrors.total_amount" class="mt-1 text-sm text-red-600">{{ validationErrors.total_amount }}</p>
        <p v-else-if="apiErrors.total_amount" class="mt-1 text-sm text-red-600">{{ apiErrors.total_amount }}</p>
      </div>
      <!-- Trạng thái -->
      <div>
        <label class="block text-sm font-medium mb-1">Trạng thái</label>
        <select v-model="formData.status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.status || apiErrors.status }">
          <option v-for="(label, value) in statusOptions" :key="value" :value="value">{{ label }}</option>
        </select>
        <p v-if="validationErrors.status" class="mt-1 text-sm text-red-600">{{ validationErrors.status }}</p>
        <p v-else-if="apiErrors.status" class="mt-1 text-sm text-red-600">{{ apiErrors.status }}</p>
      </div>
      <!-- Mô tả -->
      <div>
        <label class="block text-sm font-medium mb-1">Mô tả</label>
        <textarea v-model="formData.description" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.description || apiErrors.description }" maxlength="500"></textarea>
        <p v-if="validationErrors.description" class="mt-1 text-sm text-red-600">{{ validationErrors.description }}</p>
        <p v-else-if="apiErrors.description" class="mt-1 text-sm text-red-600">{{ apiErrors.description }}</p>
      </div>
      <!-- Ngày đặt -->
      <div>
        <label class="block text-sm font-medium mb-1">Ngày đặt</label>
        <input v-model="formData.created_at" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
      </div>
      <!-- Ngày cập nhật -->
      <div>
        <label class="block text-sm font-medium mb-1">Ngày cập nhật</label>
        <input v-model="formData.updated_at" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
      </div>
      <!-- Buttons -->
      <div class="flex justify-end space-x-3 pt-4">
        <button type="button" @click="onClose" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">Huỷ</button>
        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none" :disabled="isSubmitting">{{ isSubmitting ? 'Đang xử lý...' : (mode === 'edit' ? 'Cập nhật' : 'Thêm mới') }}</button>
      </div>
    </form>
  </Modal>
</template>
<script setup>
import { ref, computed, reactive, watch, onMounted } from 'vue'
import Modal from '@/components/Core/Modal.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  order: Object,
  apiErrors: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])

const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Chỉnh sửa đơn hàng' : 'Thêm đơn hàng mới')

const formData = reactive({
  customer_name: '',
  total_amount: '',
  status: '',
  description: '',
  created_at: '',
  updated_at: ''
})
const validationErrors = reactive({})
const isSubmitting = ref(false)
const statusOptions = ref({})

onMounted(fetchStatusOptions)
async function fetchStatusOptions() {
  try {
    const response = await axios.get(endpoints.enums('OrderStatus'))
    statusOptions.value = response.data
  } catch (error) {
    statusOptions.value = {
      pending: 'Chờ xử lý',
      completed: 'Hoàn thành',
      cancelled: 'Đã huỷ'
    }
  }
}

watch(() => props.order, (val) => {
  if (val) {
    formData.customer_name = val.customer_name || ''
    formData.total_amount = val.total_amount || ''
    formData.status = val.status || Object.keys(statusOptions.value)[0] || ''
    formData.description = val.description || ''
    formData.created_at = val.created_at || ''
    formData.updated_at = val.updated_at || ''
  } else {
    resetForm()
  }
}, { immediate: true })

function resetForm() {
  formData.customer_name = ''
  formData.total_amount = ''
  formData.status = Object.keys(statusOptions.value)[0] || ''
  formData.description = ''
  formData.created_at = ''
  formData.updated_at = ''
  clearErrors()
}
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}
const validationRules = computed(() => ({
  customer_name: [
    { required: 'Khách hàng là bắt buộc' },
    { max: [100, 'Tên khách hàng không được vượt quá 100 ký tự'] }
  ],
  total_amount: [
    { required: 'Tổng tiền là bắt buộc và phải là số' },
    { number: 'Tổng tiền phải là số' },
    { min: [0, 'Tổng tiền không được âm'] }
  ],
  description: [
    { max: [500, 'Mô tả không được vượt quá 500 ký tự'] }
  ]
}))

function validateForm() {
  clearErrors()
  let valid = true
  const rules = validationRules.value
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
      if (rule.min && formData[field] && Number(formData[field]) < rule.min[0]) {
        validationErrors[field] = rule.min[1]
        valid = false
        break
      }
    }
  }
  return valid
}
function validateAndSubmit() {
  if (!validateForm()) return
  isSubmitting.value = true
  try {
    const submitData = new FormData()
    Object.entries(formData).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        submitData.append(key, value)
      }
    })
    emit('submit', submitData)
  } finally {
    isSubmitting.value = false
  }
}
function onClose() {
  emit('cancel')
}
</script> 
