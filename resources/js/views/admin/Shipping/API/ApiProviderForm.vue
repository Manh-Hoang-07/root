<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <FormLayout @submit="validateAndSubmit" @cancel="onClose">
      <div class="space-y-4">
        <!-- Tên Provider -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên Provider <span class="text-red-500">*</span></label>
          <input
            id="name"
            v-model="formData.name"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            :class="{ 'border-red-500': validationErrors.name || apiErrors.name }"
            maxlength="100"
          />
          <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600">{{ validationErrors.name }}</p>
          <p v-else-if="apiErrors.name" class="mt-1 text-sm text-red-600">{{ apiErrors.name }}</p>
        </div>
        <!-- API Key -->
        <div>
          <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">API Key <span class="text-red-500">*</span></label>
          <input
            id="api_key"
            v-model="formData.api_key"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            :class="{ 'border-red-500': validationErrors.api_key || apiErrors.api_key }"
            maxlength="100"
          />
          <p v-if="validationErrors.api_key" class="mt-1 text-sm text-red-600">{{ validationErrors.api_key }}</p>
          <p v-else-if="apiErrors.api_key" class="mt-1 text-sm text-red-600">{{ apiErrors.api_key }}</p>
        </div>
        <!-- Secret Key -->
        <div>
          <label for="secret_key" class="block text-sm font-medium text-gray-700 mb-1">Secret Key <span class="text-red-500">*</span></label>
          <input
            id="secret_key"
            v-model="formData.secret_key"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            :class="{ 'border-red-500': validationErrors.secret_key || apiErrors.secret_key }"
            maxlength="100"
          />
          <p v-if="validationErrors.secret_key" class="mt-1 text-sm text-red-600">{{ validationErrors.secret_key }}</p>
          <p v-else-if="apiErrors.secret_key" class="mt-1 text-sm text-red-600">{{ apiErrors.secret_key }}</p>
        </div>
        <!-- Môi trường -->
        <div>
          <label for="env" class="block text-sm font-medium text-gray-700 mb-1">Môi trường</label>
          <select
            id="env"
            v-model="formData.env"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option v-for="env in envEnums" :key="env.value" :value="env.value">{{ env.name }}</option>
          </select>
        </div>
        <!-- Trạng thái -->
        <div>
          <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
          <select
            id="status"
            v-model="formData.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option v-for="status in statusEnums" :key="status.value" :value="status.value">{{ status.name }}</option>
          </select>
        </div>
      </div>
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
  mode: String,
  statusEnums: { type: Array, default: () => [
    { value: 'active', name: 'Hoạt động' },
    { value: 'inactive', name: 'Không hoạt động' }
  ] },
  envEnums: { type: Array, default: () => [
    { value: 'production', name: 'Production' },
    { value: 'test', name: 'Test' }
  ] }
})
const emit = defineEmits(['submit', 'cancel'])

const formTitle = computed(() => props.mode === 'edit' ? 'Chỉnh sửa Provider' : 'Thêm Provider mới')
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
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
const validationRules = computed(() => ({
  name: [
    { required: 'Tên Provider là bắt buộc' },
    { max: [100, 'Tên Provider không được vượt quá 100 ký tự'] }
  ],
  api_key: [
    { required: 'API Key là bắt buộc' },
    { max: [100, 'API Key không được vượt quá 100 ký tự'] }
  ],
  secret_key: [
    { required: 'Secret Key là bắt buộc' },
    { max: [100, 'Secret Key không được vượt quá 100 ký tự'] }
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