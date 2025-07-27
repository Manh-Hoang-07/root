<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="validateAndSubmit" class="space-y-4">
      <!-- Tên kho hàng -->
      <div>
        <label class="block text-sm font-medium mb-1">Tên kho hàng <span class="text-red-500">*</span></label>
        <input v-model="formData.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.name || apiErrors.name }" maxlength="100" />
        <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600">{{ validationErrors.name }}</p>
        <p v-else-if="apiErrors.name" class="mt-1 text-sm text-red-600">{{ apiErrors.name }}</p>
      </div>
      <!-- Địa chỉ -->
      <div>
        <label class="block text-sm font-medium mb-1">Địa chỉ <span class="text-red-500">*</span></label>
        <input v-model="formData.address" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.address || apiErrors.address }" maxlength="255" />
        <p v-if="validationErrors.address" class="mt-1 text-sm text-red-600">{{ validationErrors.address }}</p>
        <p v-else-if="apiErrors.address" class="mt-1 text-sm text-red-600">{{ apiErrors.address }}</p>
      </div>
      <!-- Thành phố -->
      <div>
        <label class="block text-sm font-medium mb-1">Thành phố <span class="text-red-500">*</span></label>
        <input v-model="formData.city" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.city || apiErrors.city }" maxlength="100" />
        <p v-if="validationErrors.city" class="mt-1 text-sm text-red-600">{{ validationErrors.city }}</p>
        <p v-else-if="apiErrors.city" class="mt-1 text-sm text-red-600">{{ apiErrors.city }}</p>
      </div>
      <!-- Tỉnh/Thành -->
      <div>
        <label class="block text-sm font-medium mb-1">Tỉnh/Thành <span class="text-red-500">*</span></label>
        <input v-model="formData.province" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.province || apiErrors.province }" maxlength="100" />
        <p v-if="validationErrors.province" class="mt-1 text-sm text-red-600">{{ validationErrors.province }}</p>
        <p v-else-if="apiErrors.province" class="mt-1 text-sm text-red-600">{{ apiErrors.province }}</p>
      </div>
      <!-- Số điện thoại -->
      <div>
        <label class="block text-sm font-medium mb-1">Số điện thoại</label>
        <input v-model="formData.phone" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.phone || apiErrors.phone }" maxlength="20" />
        <p v-if="validationErrors.phone" class="mt-1 text-sm text-red-600">{{ validationErrors.phone }}</p>
        <p v-else-if="apiErrors.phone" class="mt-1 text-sm text-red-600">{{ apiErrors.phone }}</p>
      </div>
      <!-- Email -->
      <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input v-model="formData.email" type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.email || apiErrors.email }" maxlength="100" />
        <p v-if="validationErrors.email" class="mt-1 text-sm text-red-600">{{ validationErrors.email }}</p>
        <p v-else-if="apiErrors.email" class="mt-1 text-sm text-red-600">{{ apiErrors.email }}</p>
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
      <!-- Buttons -->
      <div class="flex justify-end space-x-3 pt-4">
        <button type="button" @click="onClose" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">Huỷ</button>
        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none" :disabled="isSubmitting">{{ isSubmitting ? 'Đang xử lý...' : (mode === 'edit' ? 'Cập nhật' : 'Thêm mới') }}</button>
      </div>
    </form>
  </Modal>
</template>
<script setup>
import { ref, computed, reactive, watch } from 'vue'
import Modal from '@/components/Core/Modal.vue'

const props = defineProps({
  show: Boolean,
  warehouse: Object,
  apiErrors: { type: Object, default: () => ({}) },
  statusOptions: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])

const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Chỉnh sửa kho hàng' : 'Thêm kho hàng mới')

const formData = reactive({
  name: '',
  address: '',
  city: '',
  province: '',
  phone: '',
  email: '',
  status: ''
})
const validationErrors = reactive({})
const isSubmitting = ref(false)

const validationRules = computed(() => ({
  name: [
    { required: 'Tên kho hàng là bắt buộc' },
    { max: [100, 'Tên kho hàng không được vượt quá 100 ký tự'] }
  ],
  address: [
    { required: 'Địa chỉ là bắt buộc' },
    { max: [255, 'Địa chỉ không được vượt quá 255 ký tự'] }
  ],
  city: [
    { required: 'Thành phố là bắt buộc' },
    { max: [100, 'Thành phố không được vượt quá 100 ký tự'] }
  ],
  province: [
    { required: 'Tỉnh/Thành là bắt buộc' },
    { max: [100, 'Tỉnh/Thành không được vượt quá 100 ký tự'] }
  ],
  phone: [
    { max: [20, 'Số điện thoại không được vượt quá 20 ký tự'] }
  ],
  email: [
    { max: [100, 'Email không được vượt quá 100 ký tự'] },
    { pattern: [/^\S+@\S+\.\S+$/, 'Email không hợp lệ'] }
  ]
}))

watch(() => props.warehouse, (val) => {
  if (val) {
    formData.name = val.name || ''
    formData.address = val.address || ''
    formData.city = val.city || ''
    formData.province = val.province || ''
    formData.phone = val.phone || ''
    formData.email = val.email || ''
    formData.status = val.status || Object.keys(props.statusOptions)[0] || ''
  } else {
    resetForm()
  }
}, { immediate: true })

function resetForm() {
  formData.name = ''
  formData.address = ''
  formData.city = ''
  formData.province = ''
  formData.phone = ''
  formData.email = ''
  formData.status = Object.keys(props.statusOptions)[0] || ''
  clearErrors()
}
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}
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
      if (rule.pattern && formData[field] && !rule.pattern[0].test(formData[field])) {
        validationErrors[field] = rule.pattern[1]
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
