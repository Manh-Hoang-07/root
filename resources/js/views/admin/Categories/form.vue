<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="validateAndSubmit" class="space-y-4">
      <!-- Tên danh mục -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục <span class="text-red-500">*</span></label>
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
      <!-- Danh mục cha -->
      <div>
        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Danh mục cha</label>
        <select
          id="parent_id"
          v-model="formData.parent_id"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.parent_id || apiErrors.parent_id }"
        >
          <option value="">Không có (Danh mục gốc)</option>
          <option v-for="category in parentCategories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <p v-if="validationErrors.parent_id" class="mt-1 text-sm text-red-600">{{ validationErrors.parent_id }}</p>
        <p v-else-if="apiErrors.parent_id" class="mt-1 text-sm text-red-600">{{ apiErrors.parent_id }}</p>
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
      <!-- Hình ảnh -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh</label>
        <div class="flex items-start space-x-4">
          <div v-if="imagePreview || (category && category.image)" class="w-24 h-24 border rounded-md overflow-hidden">
            <img :src="imagePreview || getImageUrl(category?.image)" alt="Image preview" class="w-full h-full object-contain" />
          </div>
          <div class="flex-1">
            <input
              type="file"
              @change="handleImageChange"
              accept="image/*"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              :class="{ 'border-red-500': validationErrors.image || apiErrors.image }"
            />
            <p v-if="validationErrors.image" class="mt-1 text-sm text-red-600">{{ validationErrors.image }}</p>
            <p v-else-if="apiErrors.image" class="mt-1 text-sm text-red-600">{{ apiErrors.image }}</p>
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
          {{ isSubmitting ? 'Đang xử lý...' : (category ? 'Cập nhật' : 'Thêm mới') }}
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
  category: Object,
  parentCategories: {
    type: Array,
    default: () => []
  },
  apiErrors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['submit', 'cancel'])

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

const formTitle = computed(() => props.category ? 'Chỉnh sửa danh mục' : 'Thêm danh mục mới')
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const formData = reactive({
  name: '',
  parent_id: '',
  slug: '',
  description: '',
  image: null,
  status: 1,
  remove_image: false
})
const validationErrors = reactive({})
const isSubmitting = ref(false)

watch(() => props.category, (newCategory) => {
  if (newCategory) {
    formData.name = newCategory.name || ''
    formData.parent_id = newCategory.parent_id || ''
    formData.slug = newCategory.slug || ''
    formData.description = newCategory.description || ''
    formData.status = newCategory.status === true || newCategory.status === 1 ? 1 : 0
    formData.remove_image = false
    imagePreview.value = null
  } else {
    resetForm()
  }
}, { immediate: true })

function resetForm() {
  formData.name = ''
  formData.parent_id = ''
  formData.slug = ''
  formData.description = ''
  formData.image = null
  formData.status = 1
  formData.remove_image = false
  imagePreview.value = null
  clearErrors()
}
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}
const validationRules = computed(() => ({
  name: [
    { required: 'Tên danh mục là bắt buộc.' },
    { max: [100, 'Tên danh mục không được vượt quá 100 ký tự.'] }
  ],
  slug: [
    { pattern: [/^[a-z0-9-]*$/, 'Slug chỉ được chứa chữ thường, số và dấu gạch ngang.'] }
  ],
  description: [
    { max: [500, 'Mô tả không được vượt quá 500 ký tự.'] }
  ],
  parent_id: [
    { notSelf: ['Danh mục không thể là danh mục cha của chính nó.'] }
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
      if (rule.pattern && formData[field] && !rule.pattern[0].test(formData[field])) {
        validationErrors[field] = rule.pattern[1]
        valid = false
        break
      }
      if (rule.notSelf && props.category && formData.parent_id === props.category.id) {
        validationErrors.parent_id = rule.notSelf[0]
        valid = false
        break
      }
    }
  }
  return valid
}
function validateAndSubmit() {
  if (!validateForm()) {
    return
  }
  isSubmitting.value = true
  try {
    const submitData = new FormData()
    submitData.append('name', formData.name)
    submitData.append('slug', formData.slug)
    submitData.append('description', formData.description)
    submitData.append('status', formData.status)
    if (formData.parent_id !== '') {
      submitData.append('parent_id', formData.parent_id)
    }
    if (formData.image) {
      submitData.append('image', formData.image)
    }
    if (formData.remove_image) {
      submitData.append('remove_image', 1)
    }
    emit('submit', submitData)
  } finally {
    isSubmitting.value = false
  }
}
function onClose() {
  emit('cancel')
}
const imagePreview = ref(null)
function handleImageChange(event) {
  const file = event.target.files[0]
  if (!file) return
  const validTypes = ['image/jpeg', 'image/png', 'image/gif']
  if (!validTypes.includes(file.type)) {
    validationErrors.image = 'File phải là định dạng JPG, PNG hoặc GIF'
    return
  }
  if (file.size > 2 * 1024 * 1024) {
    validationErrors.image = 'Kích thước file không được vượt quá 2MB'
    return
  }
  delete validationErrors.image
  formData.image = file
  formData.remove_image = false
  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}
function getImageUrl(image) {
  if (!image) return null
  return image.startsWith('http') ? image : `/storage/${image}`
}
</script> 