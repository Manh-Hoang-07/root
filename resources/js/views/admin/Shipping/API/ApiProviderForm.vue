<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <FormLayout @submit="validateAndSubmit" @cancel="onClose">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Tên Provider <span class="text-red-500">*</span></label>
          <input v-model="formData.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.name || apiErrors.name }" maxlength="100" />
          <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600">{{ validationErrors.name }}</p>
          <p v-else-if="apiErrors.name" class="mt-1 text-sm text-red-600">{{ apiErrors.name }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">API Key <span class="text-red-500">*</span></label>
          <input v-model="formData.api_key" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.api_key || apiErrors.api_key }" maxlength="100" />
          <p v-if="validationErrors.api_key" class="mt-1 text-sm text-red-600">{{ validationErrors.api_key }}</p>
          <p v-else-if="apiErrors.api_key" class="mt-1 text-sm text-red-600">{{ apiErrors.api_key }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Secret Key <span class="text-red-500">*</span></label>
          <input v-model="formData.secret_key" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.secret_key || apiErrors.secret_key }" maxlength="100" />
          <p v-if="validationErrors.secret_key" class="mt-1 text-sm text-red-600">{{ validationErrors.secret_key }}</p>
          <p v-else-if="apiErrors.secret_key" class="mt-1 text-sm text-red-600">{{ apiErrors.secret_key }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Môi trường</label>
          <select v-model="formData.env" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <option value="production">Production</option>
            <option value="test">Test</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Trạng thái</label>
          <select v-model="formData.status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <option value="active">Hoạt động</option>
            <option value="inactive">Không hoạt động</option>
          </select>
        </div>
      </div>
      <template #actions>
        <button type="button" @click="onClose" class="px-4 py-2 bg-gray-100 rounded-xl hover:bg-gray-200">Huỷ</button>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700" :disabled="isSubmitting">{{ isSubmitting ? 'Đang xử lý...' : (mode === 'edit' ? 'Cập nhật' : 'Thêm mới') }}</button>
      </template>
    </FormLayout>
  </Modal>
</template>
<script setup>
import { ref, computed, reactive, watch } from 'vue'
import Modal from '@/components/Modal.vue'
import FormLayout from '@/components/FormLayout.vue'

const props = defineProps({
  show: Boolean,
  provider: Object,
  apiErrors: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])

const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Chỉnh sửa Provider' : 'Thêm Provider mới')

const formData = reactive({
  name: '',
  api_key: '',
  secret_key: '',
  env: 'production',
  status: 'active'
})
const validationErrors = reactive({})
const isSubmitting = ref(false)

watch(() => props.provider, (val) => {
  if (val) {
    formData.name = val.name || ''
    formData.api_key = val.api_key || ''
    formData.secret_key = val.secret_key || ''
    formData.env = val.env || 'production'
    formData.status = val.status || 'active'
  } else {
    resetForm()
  }
}, { immediate: true })

function resetForm() {
  formData.name = ''
  formData.api_key = ''
  formData.secret_key = ''
  formData.env = 'production'
  formData.status = 'active'
  clearErrors()
}
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}
function validateForm() {
  clearErrors()
  if (!formData.name.trim()) {
    validationErrors.name = 'Tên Provider là bắt buộc'
  } else if (formData.name.length > 100) {
    validationErrors.name = 'Tên Provider không được vượt quá 100 ký tự'
  }
  if (!formData.api_key.trim()) {
    validationErrors.api_key = 'API Key là bắt buộc'
  } else if (formData.api_key.length > 100) {
    validationErrors.api_key = 'API Key không được vượt quá 100 ký tự'
  }
  if (!formData.secret_key.trim()) {
    validationErrors.secret_key = 'Secret Key là bắt buộc'
  } else if (formData.secret_key.length > 100) {
    validationErrors.secret_key = 'Secret Key không được vượt quá 100 ký tự'
  }
  return Object.keys(validationErrors).length === 0
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