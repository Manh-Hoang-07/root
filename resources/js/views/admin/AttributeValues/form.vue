<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="validateAndSubmit" class="space-y-4">
      <!-- Thuộc tính cha -->
      <div>
        <label for="attribute_id" class="block text-sm font-medium text-gray-700 mb-1">Thuộc tính <span class="text-red-500">*</span></label>
        <select
          id="attribute_id"
          v-model="formData.attribute_id"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.attribute_id || apiErrors.attribute_id }"
        >
          <option value="">Chọn thuộc tính</option>
          <option v-for="attr in attributeOptions" :key="attr.id" :value="attr.id">
            {{ attr.name }}
          </option>
        </select>
        <p v-if="validationErrors.attribute_id" class="mt-1 text-sm text-red-600">{{ validationErrors.attribute_id }}</p>
        <p v-else-if="apiErrors.attribute_id" class="mt-1 text-sm text-red-600">{{ apiErrors.attribute_id }}</p>
      </div>

      <!-- Giá trị -->
      <div>
        <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Giá trị <span class="text-red-500">*</span></label>
        <input
          id="value"
          v-model="formData.value"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.value || apiErrors.value }"
        />
        <p v-if="validationErrors.value" class="mt-1 text-sm text-red-600">{{ validationErrors.value }}</p>
        <p v-else-if="apiErrors.value" class="mt-1 text-sm text-red-600">{{ apiErrors.value }}</p>
      </div>

      <!-- Hiển thị -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Hiển thị</label>
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

      <!-- Thứ tự -->
      <div>
        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
        <input
          id="sort_order"
          v-model="formData.sort_order"
          type="number"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.sort_order || apiErrors.sort_order }"
        />
        <p v-if="validationErrors.sort_order" class="mt-1 text-sm text-red-600">{{ validationErrors.sort_order }}</p>
        <p v-else-if="apiErrors.sort_order" class="mt-1 text-sm text-red-600">{{ apiErrors.sort_order }}</p>
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
          {{ isSubmitting ? 'Đang xử lý...' : (attributeValue ? 'Cập nhật' : 'Thêm mới') }}
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
  attributeValue: Object,
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

// Lấy danh sách thuộc tính từ API
const attributeOptions = ref([])
const fetchAttributeOptions = async () => {
  try {
    const response = await axios.get(endpoints.attributes.list, { params: { per_page: 100 } })
    attributeOptions.value = response.data.data || []
  } catch (error) {
    attributeOptions.value = []
  }
}

onMounted(() => {
  fetchStatusOptions()
  fetchAttributeOptions()
})

// Form title
const formTitle = computed(() => props.attributeValue ? 'Chỉnh sửa giá trị thuộc tính' : 'Thêm giá trị thuộc tính mới')

// Modal visibility
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})

// Form data
const formData = reactive({
  attribute_id: '',
  value: '',
  name: '',
  sort_order: 0,
  status: 'active'
})

// Form state
const validationErrors = reactive({})
const isSubmitting = ref(false)

// Watch attributeValue prop to update form data
watch(() => props.attributeValue, (newVal) => {
  if (newVal) {
    formData.attribute_id = newVal.attribute_id || ''
    formData.value = newVal.value || ''
    formData.name = newVal.name || ''
    formData.sort_order = newVal.sort_order || 0
    formData.status = newVal.status || 'active'
  } else {
    resetForm()
  }
}, { immediate: true })

// Reset form
function resetForm() {
  formData.attribute_id = ''
  formData.value = ''
  formData.name = ''
  formData.sort_order = 0
  formData.status = 'active'
  clearErrors()
}

// Clear errors
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}

// Validate form
function validateForm() {
  clearErrors()
  
  // Validate attribute_id
  if (!formData.attribute_id) {
    validationErrors.attribute_id = 'Vui lòng chọn thuộc tính'
  }
  // Validate value
  if (!formData.value.trim()) {
    validationErrors.value = 'Giá trị là bắt buộc'
  }
  // Validate sort_order
  if (formData.sort_order && isNaN(Number(formData.sort_order))) {
    validationErrors.sort_order = 'Thứ tự phải là số'
  }
  return Object.keys(validationErrors).length === 0
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
    submitData.append('attribute_id', formData.attribute_id)
    submitData.append('value', formData.value)
    submitData.append('name', formData.name)
    submitData.append('sort_order', formData.sort_order)
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