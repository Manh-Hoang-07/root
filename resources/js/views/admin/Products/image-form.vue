<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="validateAndSubmit" class="space-y-4">
      <!-- URL ảnh -->
      <div>
        <label class="block text-sm font-medium mb-1">URL ảnh <span class="text-red-500">*</span></label>
        <input v-model="formData.url" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.url || apiErrors.url }" maxlength="500" />
        <p v-if="validationErrors.url" class="mt-1 text-sm text-red-600">{{ validationErrors.url }}</p>
        <p v-else-if="apiErrors.url" class="mt-1 text-sm text-red-600">{{ apiErrors.url }}</p>
      </div>

      <!-- Preview ảnh -->
      <div v-if="formData.url" class="border rounded-lg p-4">
        <label class="block text-sm font-medium mb-2">Xem trước:</label>
        <img :src="formData.url" :alt="formData.alt_text || 'Preview'" class="w-full h-32 object-cover rounded" @error="handleImageError" />
      </div>

      <!-- Alt text -->
      <div>
        <label class="block text-sm font-medium mb-1">Mô tả ảnh (Alt text)</label>
        <input v-model="formData.alt_text" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.alt_text || apiErrors.alt_text }" maxlength="255" />
        <p v-if="validationErrors.alt_text" class="mt-1 text-sm text-red-600">{{ validationErrors.alt_text }}</p>
        <p v-else-if="apiErrors.alt_text" class="mt-1 text-sm text-red-600">{{ apiErrors.alt_text }}</p>
      </div>

      <!-- Caption -->
      <div>
        <label class="block text-sm font-medium mb-1">Chú thích</label>
        <textarea v-model="formData.caption" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.caption || apiErrors.caption }" rows="3" maxlength="500"></textarea>
        <p v-if="validationErrors.caption" class="mt-1 text-sm text-red-600">{{ validationErrors.caption }}</p>
        <p v-else-if="apiErrors.caption" class="mt-1 text-sm text-red-600">{{ apiErrors.caption }}</p>
      </div>

      <!-- Thứ tự -->
      <div>
        <label class="block text-sm font-medium mb-1">Thứ tự hiển thị</label>
        <input v-model="formData.order" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.order || apiErrors.order }" min="0" />
        <p v-if="validationErrors.order" class="mt-1 text-sm text-red-600">{{ validationErrors.order }}</p>
        <p v-else-if="apiErrors.order" class="mt-1 text-sm text-red-600">{{ apiErrors.order }}</p>
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
  image: Object,
  productId: [String, Number],
  apiErrors: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel', 'created', 'updated'])

const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Chỉnh sửa ảnh' : 'Thêm ảnh mới')

const formData = reactive({
  url: '',
  alt_text: '',
  caption: '',
  order: 0
})

const validationErrors = reactive({})
const isSubmitting = ref(false)

watch(() => props.image, (val) => {
  if (val) {
    formData.url = val.url || ''
    formData.alt_text = val.alt_text || ''
    formData.caption = val.caption || ''
    formData.order = val.order || 0
  } else {
    resetForm()
  }
}, { immediate: true })

function resetForm() {
  formData.url = ''
  formData.alt_text = ''
  formData.caption = ''
  formData.order = 0
  clearErrors()
}

function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}

function validateForm() {
  clearErrors()
  
  if (!formData.url.trim()) {
    validationErrors.url = 'URL ảnh là bắt buộc'
  } else if (formData.url.length > 500) {
    validationErrors.url = 'URL ảnh không được vượt quá 500 ký tự'
  }

  if (formData.alt_text && formData.alt_text.length > 255) {
    validationErrors.alt_text = 'Mô tả ảnh không được vượt quá 255 ký tự'
  }

  if (formData.caption && formData.caption.length > 500) {
    validationErrors.caption = 'Chú thích không được vượt quá 500 ký tự'
  }

  if (formData.order < 0) {
    validationErrors.order = 'Thứ tự không được âm'
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
    
    // Add product_id for polymorphic relationship
    submitData.append('imageable_type', 'App\\Models\\Product')
    submitData.append('imageable_id', props.productId)
    
    emit('submit', submitData)
  } finally {
    isSubmitting.value = false
  }
}

function handleImageError(event) {
  event.target.style.display = 'none'
  event.target.nextElementSibling?.style.display = 'block'
}

function onClose() {
  emit('cancel')
}
</script> 