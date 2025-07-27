<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="validateAndSubmit" class="space-y-4">
      <!-- Tên sản phẩm -->
      <div>
        <label class="block text-sm font-medium mb-1">Tên sản phẩm <span class="text-red-500">*</span></label>
        <input v-model="formData.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.name || apiErrors.name }" maxlength="100" />
        <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600">{{ validationErrors.name }}</p>
        <p v-else-if="apiErrors.name" class="mt-1 text-sm text-red-600">{{ apiErrors.name }}</p>
      </div>
      <!-- SKU -->
      <div>
        <label class="block text-sm font-medium mb-1">SKU</label>
        <input v-model="formData.sku" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.sku || apiErrors.sku }" maxlength="50" />
        <p v-if="validationErrors.sku" class="mt-1 text-sm text-red-600">{{ validationErrors.sku }}</p>
        <p v-else-if="apiErrors.sku" class="mt-1 text-sm text-red-600">{{ apiErrors.sku }}</p>
      </div>
      <!-- Danh mục -->
      <div>
        <label class="block text-sm font-medium mb-1">Danh mục</label>
        <input v-model="formData.category_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.category_name || apiErrors.category_name }" />
        <p v-if="validationErrors.category_name" class="mt-1 text-sm text-red-600">{{ validationErrors.category_name }}</p>
        <p v-else-if="apiErrors.category_name" class="mt-1 text-sm text-red-600">{{ apiErrors.category_name }}</p>
      </div>
      <!-- Thương hiệu -->
      <div>
        <label class="block text-sm font-medium mb-1">Thương hiệu</label>
        <input v-model="formData.brand_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.brand_name || apiErrors.brand_name }" />
        <p v-if="validationErrors.brand_name" class="mt-1 text-sm text-red-600">{{ validationErrors.brand_name }}</p>
        <p v-else-if="apiErrors.brand_name" class="mt-1 text-sm text-red-600">{{ apiErrors.brand_name }}</p>
      </div>
      <!-- Giá -->
      <div>
        <label class="block text-sm font-medium mb-1">Giá</label>
        <input v-model="formData.price" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.price || apiErrors.price }" />
        <p v-if="validationErrors.price" class="mt-1 text-sm text-red-600">{{ validationErrors.price }}</p>
        <p v-else-if="apiErrors.price" class="mt-1 text-sm text-red-600">{{ apiErrors.price }}</p>
      </div>
      <!-- Tồn kho -->
      <div>
        <label class="block text-sm font-medium mb-1">Tồn kho</label>
        <input v-model="formData.stock" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.stock || apiErrors.stock }" />
        <p v-if="validationErrors.stock" class="mt-1 text-sm text-red-600">{{ validationErrors.stock }}</p>
        <p v-else-if="apiErrors.stock" class="mt-1 text-sm text-red-600">{{ apiErrors.stock }}</p>
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
      <!-- Ảnh sản phẩm (URL) -->
      <div>
        <label class="block text-sm font-medium mb-1">Ảnh sản phẩm (URL)</label>
        <input v-model="formData.image" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.image || apiErrors.image }" maxlength="255" />
        <p v-if="validationErrors.image" class="mt-1 text-sm text-red-600">{{ validationErrors.image }}</p>
        <p v-else-if="apiErrors.image" class="mt-1 text-sm text-red-600">{{ apiErrors.image }}</p>
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
  product: Object,
  apiErrors: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])

const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới')

const formData = reactive({
  name: '',
  sku: '',
  category_name: '',
  brand_name: '',
  price: '',
  stock: '',
  status: '',
  description: '',
  image: ''
})
const validationErrors = reactive({})
const isSubmitting = ref(false)
const statusOptions = ref({})

onMounted(fetchStatusOptions)
async function fetchStatusOptions() {
  try {
    const response = await axios.get(endpoints.enums('ProductStatus'))
    statusOptions.value = response.data
  } catch (error) {
    statusOptions.value = {
      active: 'Đang bán',
      inactive: 'Ngừng bán',
      draft: 'Bản nháp'
    }
  }
}

watch(() => props.product, (val) => {
  if (val) {
    formData.name = val.name || ''
    formData.sku = val.sku || ''
    formData.category_name = val.category_name || ''
    formData.brand_name = val.brand_name || ''
    formData.price = val.price || ''
    formData.stock = val.stock || ''
    formData.status = val.status || Object.keys(statusOptions.value)[0] || ''
    formData.description = val.description || ''
    formData.image = val.image || ''
  } else {
    resetForm()
  }
}, { immediate: true })

function resetForm() {
  formData.name = ''
  formData.sku = ''
  formData.category_name = ''
  formData.brand_name = ''
  formData.price = ''
  formData.stock = ''
  formData.status = Object.keys(statusOptions.value)[0] || ''
  formData.description = ''
  formData.image = ''
  clearErrors()
}
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}
function validateForm() {
  clearErrors()
  if (!formData.name.trim()) {
    validationErrors.name = 'Tên sản phẩm là bắt buộc'
  } else if (formData.name.length > 100) {
    validationErrors.name = 'Tên sản phẩm không được vượt quá 100 ký tự'
  }
  if (!formData.sku.trim()) {
    validationErrors.sku = 'SKU là bắt buộc'
  } else if (formData.sku.length > 50) {
    validationErrors.sku = 'SKU không được vượt quá 50 ký tự'
  }
  if (!formData.category_name.trim()) {
    validationErrors.category_name = 'Danh mục là bắt buộc'
  }
  if (!formData.brand_name.trim()) {
    validationErrors.brand_name = 'Thương hiệu là bắt buộc'
  }
  if (!formData.price || isNaN(formData.price)) {
    validationErrors.price = 'Giá là bắt buộc và phải là số'
  } else if (Number(formData.price) < 0) {
    validationErrors.price = 'Giá không được âm'
  }
  if (!formData.stock || isNaN(formData.stock)) {
    validationErrors.stock = 'Tồn kho là bắt buộc và phải là số'
  } else if (Number(formData.stock) < 0) {
    validationErrors.stock = 'Tồn kho không được âm'
  }
  if (formData.description && formData.description.length > 500) {
    validationErrors.description = 'Mô tả không được vượt quá 500 ký tự'
  }
  if (formData.image && formData.image.length > 255) {
    validationErrors.image = 'Đường dẫn ảnh không được vượt quá 255 ký tự'
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
