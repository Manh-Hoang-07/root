<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="validateAndSubmit" class="space-y-4">
      <!-- Tên thương hiệu -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên thương hiệu <span class="text-red-500">*</span></label>
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

      <!-- Slug -->
      <div>
        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
        <input
          id="slug"
          v-model="formData.slug"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.slug || apiErrors.slug }"
        />
        <p v-if="validationErrors.slug" class="mt-1 text-sm text-red-600">{{ validationErrors.slug }}</p>
        <p v-else-if="apiErrors.slug" class="mt-1 text-sm text-red-600">{{ apiErrors.slug }}</p>
        <p class="mt-1 text-sm text-gray-500">Để trống để tự động tạo từ tên</p>
      </div>

      <!-- Mô tả -->
      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
        <textarea
          id="description"
          v-model="formData.description"
          rows="3"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.description || apiErrors.description }"
        ></textarea>
        <p v-if="validationErrors.description" class="mt-1 text-sm text-red-600">{{ validationErrors.description }}</p>
        <p v-else-if="apiErrors.description" class="mt-1 text-sm text-red-600">{{ apiErrors.description }}</p>
      </div>

      <!-- Logo -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
        <div class="flex items-start space-x-4">
          <div v-if="logoPreview || (brand && brand.logo)" class="w-24 h-24 border rounded-md overflow-hidden">
            <img :src="logoPreview || getImageUrl(brand?.logo)" alt="Logo preview" class="w-full h-full object-contain" />
          </div>
          <div class="flex-1">
            <input
              type="file"
              @change="handleLogoChange"
              accept="image/*"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              :class="{ 'border-red-500': validationErrors.logo || apiErrors.logo }"
            />
            <p v-if="validationErrors.logo" class="mt-1 text-sm text-red-600">{{ validationErrors.logo }}</p>
            <p v-else-if="apiErrors.logo" class="mt-1 text-sm text-red-600">{{ apiErrors.logo }}</p>
            <p class="mt-1 text-sm text-gray-500">PNG, JPG hoặc GIF (tối đa 2MB)</p>
          </div>
        </div>
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
          <option v-for="(label, value) in statusOptions" :key="value" :value="Number(value)">
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
          {{ isSubmitting ? 'Đang xử lý...' : (brand ? 'Cập nhật' : 'Thêm mới') }}
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
  brand: Object,
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

onMounted(fetchStatusOptions)

// Form title
const formTitle = computed(() => props.brand ? 'Chỉnh sửa thương hiệu' : 'Thêm thương hiệu mới')

// Modal visibility
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})

// Form data
const formData = reactive({
  name: '',
  slug: '',
  description: '',
  logo: null,
  status: 1,
  remove_logo: false
})

// Form state
const logoPreview = ref(null)
const validationErrors = reactive({})
const isSubmitting = ref(false)

// Watch brand prop to update form data
watch(() => props.brand, (newBrand) => {
  if (newBrand) {
    formData.name = newBrand.name || ''
    formData.slug = newBrand.slug || ''
    formData.description = newBrand.description || ''
    formData.status = newBrand.status === true || newBrand.status === 1 ? 1 : 0
    formData.remove_logo = false
    logoPreview.value = null
  } else {
    resetForm()
  }
}, { immediate: true })

// Reset form
function resetForm() {
  formData.name = ''
  formData.slug = ''
  formData.description = ''
  formData.logo = null
  formData.status = 1
  formData.remove_logo = false
  logoPreview.value = null
  clearErrors()
}

// Clear errors
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}

// Handle logo change
function handleLogoChange(event) {
  const file = event.target.files[0]
  if (!file) return

  // Validate file type
  const validTypes = ['image/jpeg', 'image/png', 'image/gif']
  if (!validTypes.includes(file.type)) {
    validationErrors.logo = 'File phải là định dạng JPG, PNG hoặc GIF'
    return
  }

  // Validate file size (max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    validationErrors.logo = 'Kích thước file không được vượt quá 2MB'
    return
  }

  // Clear error if valid
  delete validationErrors.logo
  
  formData.logo = file
  formData.remove_logo = false

  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    logoPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

// Get image URL
function getImageUrl(logo) {
  if (!logo) return null
  return logo.startsWith('http') ? logo : `/storage/${logo}`
}

// Validate form
function validateForm() {
  clearErrors()
  
  // Validate name
  if (!formData.name.trim()) {
    validationErrors.name = 'Tên thương hiệu là bắt buộc'
  } else if (formData.name.length > 100) {
    validationErrors.name = 'Tên thương hiệu không được vượt quá 100 ký tự'
  }
  
  // Validate slug if provided
  if (formData.slug && !/^[a-z0-9-]+$/.test(formData.slug)) {
    validationErrors.slug = 'Slug chỉ được chứa chữ thường, số và dấu gạch ngang'
  }
  
  // Validate description length
  if (formData.description && formData.description.length > 500) {
    validationErrors.description = 'Mô tả không được vượt quá 500 ký tự'
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
    submitData.append('name', formData.name)
    submitData.append('slug', formData.slug)
    submitData.append('description', formData.description)
    submitData.append('status', formData.status)
    
    if (formData.logo) {
      submitData.append('logo', formData.logo)
    }
    
    if (formData.remove_logo) {
      submitData.append('remove_logo', 1)
    }

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