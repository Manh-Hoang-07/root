<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tên khuyến mãi <span class="text-red-500">*</span></label>
        <input v-model="formData.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.name || apiErrors.name }" maxlength="100" />
        <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600">{{ validationErrors.name }}</p>
        <p v-else-if="apiErrors.name" class="mt-1 text-sm text-red-600">{{ apiErrors.name }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Discount <span class="text-red-500">*</span></label>
        <input v-model="formData.discount" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.discount || apiErrors.discount }" />
        <p v-if="validationErrors.discount" class="mt-1 text-sm text-red-600">{{ validationErrors.discount }}</p>
        <p v-else-if="apiErrors.discount" class="mt-1 text-sm text-red-600">{{ apiErrors.discount }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Valid Until <span class="text-red-500">*</span></label>
        <input v-model="formData.valid_until" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.valid_until || apiErrors.valid_until }" />
        <p v-if="validationErrors.valid_until" class="mt-1 text-sm text-red-600">{{ validationErrors.valid_until }}</p>
        <p v-else-if="apiErrors.valid_until" class="mt-1 text-sm text-red-600">{{ apiErrors.valid_until }}</p>
      </div>
      <div class="flex justify-end space-x-3 pt-4">
        <button type="button" @click="onCancel" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">Huỷ</button>
        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">{{ mode === 'edit' ? 'Cập nhật' : 'Thêm mới' }}</button>
      </div>
    </form>
  </Modal>
</template>
<script setup>
import { ref, computed, reactive, watch } from 'vue'
import Modal from '@/components/Core/Modal.vue'
const props = defineProps({
  show: Boolean,
  promotion: Object,
  apiErrors: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const modalVisible = computed({
  get: () => props.show,
  set: () => onCancel()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Sửa khuyến mãi' : 'Thêm khuyến mãi mới')
const formData = reactive({
  name: '',
  discount: '',
  valid_until: '',
  status: 'active'
})
const validationErrors = reactive({})
watch(() => props.promotion, (val) => {
  if (val) {
    formData.name = val.name || ''
    formData.discount = val.discount || ''
    formData.valid_until = val.valid_until || ''
    formData.status = val.status || 'active'
  } else {
    resetForm()
  }
}, { immediate: true })
function resetForm() {
  formData.name = ''
  formData.discount = ''
  formData.valid_until = ''
  formData.status = 'active'
  clearErrors()
}
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}
const validationRules = computed(() => ({
  name: [{ required: 'Tên khuyến mãi là bắt buộc.' }],
  discount: [{ required: 'Discount là bắt buộc.' }],
  valid_until: [{ required: 'Ngày hết hạn là bắt buộc.' }]
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
    }
  }
  return valid
}
function handleSubmit() {
  if (!validateForm()) return
  emit('submit', { ...formData })
}
function onCancel() { emit('cancel') }
</script> 
