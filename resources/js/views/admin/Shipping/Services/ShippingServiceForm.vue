<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Tên dịch vụ -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tên dịch vụ <span class="text-red-500">*</span></label>
        <input v-model="formData.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.name || apiErrors.name }" maxlength="100" />
        <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600">{{ validationErrors.name }}</p>
        <p v-else-if="apiErrors.name" class="mt-1 text-sm text-red-600">{{ apiErrors.name }}</p>
      </div>
      <!-- Mã dịch vụ -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Mã dịch vụ (code) <span class="text-red-500">*</span></label>
        <input v-model="formData.code" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.code || apiErrors.code }" maxlength="50" />
        <p v-if="validationErrors.code" class="mt-1 text-sm text-red-600">{{ validationErrors.code }}</p>
        <p v-else-if="apiErrors.code" class="mt-1 text-sm text-red-600">{{ apiErrors.code }}</p>
      </div>
      <!-- Provider -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Provider <span class="text-red-500">*</span></label>
        <select v-model="formData.provider_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.provider_id || apiErrors.provider_id }">
          <option value="">Chọn provider</option>
          <option v-for="provider in providers" :key="provider.id" :value="provider.id">{{ provider.name }}</option>
        </select>
        <p v-if="validationErrors.provider_id" class="mt-1 text-sm text-red-600">{{ validationErrors.provider_id }}</p>
        <p v-else-if="apiErrors.provider_id" class="mt-1 text-sm text-red-600">{{ apiErrors.provider_id }}</p>
      </div>
      <!-- Base Price -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Base Price <span class="text-red-500">*</span></label>
        <input v-model="formData.base_price" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.base_price || apiErrors.base_price }" />
        <p v-if="validationErrors.base_price" class="mt-1 text-sm text-red-600">{{ validationErrors.base_price }}</p>
        <p v-else-if="apiErrors.base_price" class="mt-1 text-sm text-red-600">{{ apiErrors.base_price }}</p>
      </div>
      <!-- Weight Fee -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Weight Fee <span class="text-red-500">*</span></label>
        <input v-model="formData.weight_fee" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.weight_fee || apiErrors.weight_fee }" />
        <p v-if="validationErrors.weight_fee" class="mt-1 text-sm text-red-600">{{ validationErrors.weight_fee }}</p>
        <p v-else-if="apiErrors.weight_fee" class="mt-1 text-sm text-red-600">{{ apiErrors.weight_fee }}</p>
      </div>
      <!-- Estimated Days -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Days <span class="text-red-500">*</span></label>
        <input v-model="formData.estimated_days" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.estimated_days || apiErrors.estimated_days }" />
        <p v-if="validationErrors.estimated_days" class="mt-1 text-sm text-red-600">{{ validationErrors.estimated_days }}</p>
        <p v-else-if="apiErrors.estimated_days" class="mt-1 text-sm text-red-600">{{ apiErrors.estimated_days }}</p>
      </div>
      <!-- Trạng thái -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
        <select v-model="formData.status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.status || apiErrors.status }">
          <option value="active">Hoạt động</option>
          <option value="inactive">Không hoạt động</option>
        </select>
        <p v-if="validationErrors.status" class="mt-1 text-sm text-red-600">{{ validationErrors.status }}</p>
        <p v-else-if="apiErrors.status" class="mt-1 text-sm text-red-600">{{ apiErrors.status }}</p>
      </div>
      <!-- Buttons -->
      <div class="flex justify-end space-x-3 pt-4">
        <button type="button" @click="onCancel" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">Huỷ</button>
        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">{{ mode === 'edit' ? 'Cập nhật' : 'Thêm mới' }}</button>
      </div>
    </form>
  </Modal>
</template>
<script setup>
import { ref, computed, reactive, watch } from 'vue'
import Modal from '@/components/Modal.vue'

const props = defineProps({
  show: Boolean,
  service: Object,
  providers: { type: Array, default: () => [] },
  apiErrors: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])

const modalVisible = computed({
  get: () => props.show,
  set: () => onCancel()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Chỉnh sửa dịch vụ' : 'Thêm dịch vụ mới')

const formData = reactive({
  name: '',
  code: '',
  provider_id: '',
  base_price: '',
  weight_fee: '',
  estimated_days: '',
  status: 'active'
})
const validationErrors = reactive({})

watch(() => props.service, (val) => {
  if (val) {
    formData.name = val.name || ''
    formData.code = val.code || ''
    formData.provider_id = val.provider_id || ''
    formData.base_price = val.base_price || ''
    formData.weight_fee = val.weight_fee || ''
    formData.estimated_days = val.estimated_days || ''
    formData.status = val.status || 'active'
  } else {
    resetForm()
  }
}, { immediate: true })

function resetForm() {
  formData.name = ''
  formData.code = ''
  formData.provider_id = ''
  formData.base_price = ''
  formData.weight_fee = ''
  formData.estimated_days = ''
  formData.status = 'active'
  clearErrors()
}
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}
const validationRules = computed(() => ({
  name: [{ required: 'Tên dịch vụ là bắt buộc.' }],
  code: [{ required: 'Mã dịch vụ là bắt buộc.' }],
  provider_id: [{ required: 'Provider là bắt buộc.' }],
  base_price: [
    { required: 'Base price là bắt buộc.' },
    { number: 'Base price phải là số.' }
  ],
  weight_fee: [
    { required: 'Weight fee là bắt buộc.' },
    { number: 'Weight fee phải là số.' }
  ],
  estimated_days: [
    { required: 'Estimated days là bắt buộc.' },
    { number: 'Estimated days phải là số.' }
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
      if (rule.number && formData[field] && isNaN(formData[field])) {
        validationErrors[field] = rule.number
        valid = false
        break
      }
    }
  }
  return valid
}
function handleSubmit() {
  if (!validateForm()) return
  const submitData = new FormData()
  submitData.append('name', formData.name)
  submitData.append('code', formData.code)
  submitData.append('provider_id', formData.provider_id)
  submitData.append('base_price', formData.base_price)
  submitData.append('weight_fee', formData.weight_fee)
  submitData.append('estimated_days', formData.estimated_days)
  submitData.append('status', formData.status)
  emit('submit', submitData)
}
function onCancel() {
  emit('cancel')
}
</script> 