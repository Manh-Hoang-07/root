<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="validateAndSubmit" class="space-y-4">
      <!-- Tên quyền -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên quyền <span class="text-red-500">*</span></label>
        <input
          id="name"
          v-model="formData.name"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.name || apiErrors.name }"
        />
        <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600">{{ validationErrors.name }}</p>
        <p v-else-if="apiErrors.name" class="mt-1 text-sm text-red-600">{{ apiErrors.name }}</p>
      </div>

      <!-- Tên hiển thị -->
      <div>
        <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Tên hiển thị</label>
        <input
          id="display_name"
          v-model="formData.display_name"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.display_name || apiErrors.display_name }"
        />
        <p v-if="validationErrors.display_name" class="mt-1 text-sm text-red-600">{{ validationErrors.display_name }}</p>
        <p v-else-if="apiErrors.display_name" class="mt-1 text-sm text-red-600">{{ apiErrors.display_name }}</p>
      </div>

      <!-- Guard -->
      <div>
        <label for="guard_name" class="block text-sm font-medium text-gray-700 mb-1">Guard</label>
        <input
          id="guard_name"
          v-model="formData.guard_name"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.guard_name || apiErrors.guard_name }"
        />
        <p v-if="validationErrors.guard_name" class="mt-1 text-sm text-red-600">{{ validationErrors.guard_name }}</p>
        <p v-else-if="apiErrors.guard_name" class="mt-1 text-sm text-red-600">{{ apiErrors.guard_name }}</p>
      </div>

      <!-- Quyền cha -->
      <div>
        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Quyền cha</label>
        <select
          id="parent_id"
          v-model="formData.parent_id"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.parent_id || apiErrors.parent_id }"
        >
          <option value="">-- Không có --</option>
          <option v-for="p in parentOptions" :key="p.id" :value="p.id">
            {{ p.display_name || p.name }}
          </option>
        </select>
        <p v-if="validationErrors.parent_id" class="mt-1 text-sm text-red-600">{{ validationErrors.parent_id }}</p>
        <p v-else-if="apiErrors.parent_id" class="mt-1 text-sm text-red-600">{{ apiErrors.parent_id }}</p>
      </div>

      <!-- Trạng thái -->
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
        <select
          id="status"
          v-model="formData.status"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.status || apiErrors.status }"
        >
          <option v-for="(label, value) in statusOptions" :key="value" :value="value">
            {{ label }}
          </option>
        </select>
        <p v-if="validationErrors.status" class="mt-1 text-sm text-red-600">{{ validationErrors.status }}</p>
        <p v-else-if="apiErrors.status" class="mt-1 text-sm text-red-600">{{ apiErrors.status }}</p>
      </div>

      <!-- Buttons -->
      <div class="flex justify-end space-x-3 pt-4">
        <button
          type="button"
          @click="onClose"
          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
        >
          Hủy
        </button>
        <button
          type="submit"
          class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none"
          :disabled="isSubmitting"
        >
          {{ isSubmitting ? 'Đang xử lý...' : (permission ? 'Cập nhật' : 'Thêm mới') }}
        </button>
      </div>
    </form>
  </Modal>
</template>

<script setup>
import { ref, computed, reactive, watch, onMounted } from 'vue'
import Modal from '@/components/Modal.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  permission: Object,
  apiErrors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['submit', 'cancel'])

// Lấy danh sách trạng thái từ API
const statusOptions = ref({})
const fetchStatusOptions = async () => {
  try {
    const response = await axios.get(endpoints.enums('BasicStatus'))
    statusOptions.value = response.data
  } catch (error) {
    console.error('Error fetching status options:', error)
  }
}

// Lấy danh sách quyền cha
const parentOptions = ref([])
const fetchParentOptions = async () => {
  try {
    const response = await axios.get(endpoints.permissions.list, { params: { per_page: 1000 } })
    parentOptions.value = response.data.data || []
  } catch (error) {
    parentOptions.value = []
  }
}

onMounted(() => {
  fetchStatusOptions()
  fetchParentOptions()
})

// Form title
const formTitle = computed(() => props.permission ? 'Chỉnh sửa quyền' : 'Thêm quyền mới')

// Modal visibility
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})

// Form data
const formData = reactive({
  name: '',
  display_name: '',
  guard_name: '',
  parent_id: '',
  status: 'active'
})

// Form state
const validationErrors = reactive({})
const isSubmitting = ref(false)

// Watch permission prop to update form data
watch(() => props.permission, (newVal) => {
  if (newVal) {
    formData.name = newVal.name || ''
    formData.display_name = newVal.display_name || ''
    formData.guard_name = newVal.guard_name || ''
    formData.parent_id = newVal.parent_id || ''
    formData.status = newVal.status || 'active'
  } else {
    resetForm()
  }
}, { immediate: true })

// Reset form
function resetForm() {
  formData.name = ''
  formData.display_name = ''
  formData.guard_name = ''
  formData.parent_id = ''
  formData.status = 'active'
  clearErrors()
}

// Clear errors
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}

const validationRules = computed(() => ({
  name: [
    { required: 'Tên quyền là bắt buộc' }
  ],
  display_name: [
    { required: 'Tên hiển thị là bắt buộc' }
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
    }
  }
  return valid
}

// Validate and submit form
function validateAndSubmit() {
  if (!validateForm()) {
    return
  }
  
  isSubmitting.value = true
  
  try {
    // Create FormData object for file upload
    const submitData = new FormData()
    submitData.append('name', formData.name)
    submitData.append('display_name', formData.display_name)
    submitData.append('guard_name', formData.guard_name)
    submitData.append('parent_id', formData.parent_id)
    submitData.append('status', formData.status)
    emit('submit', submitData)
  } finally {
    isSubmitting.value = false
  }
}

// Close modal
function onClose() {
  emit('cancel')
}
</script> 