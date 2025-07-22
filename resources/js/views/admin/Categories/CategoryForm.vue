<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Tên danh mục -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục</label>
        <input
          id="name"
          v-model="formData.name"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': apiErrors.name }"
        />
        <p v-if="apiErrors.name" class="mt-1 text-sm text-red-600">{{ apiErrors.name }}</p>
      </div>

      <!-- Danh mục cha -->
      <div>
        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Danh mục cha</label>
        <select
          id="parent_id"
          v-model="formData.parent_id"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': apiErrors.parent_id }"
        >
          <option value="">Không có (Danh mục gốc)</option>
          <option v-for="category in parentCategories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <p v-if="apiErrors.parent_id" class="mt-1 text-sm text-red-600">{{ apiErrors.parent_id }}</p>
      </div>

      <!-- Slug -->
      <div>
        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
        <input
          id="slug"
          v-model="formData.slug"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': apiErrors.slug }"
        />
        <p v-if="apiErrors.slug" class="mt-1 text-sm text-red-600">{{ apiErrors.slug }}</p>
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
          :class="{ 'border-red-500': apiErrors.description }"
        ></textarea>
        <p v-if="apiErrors.description" class="mt-1 text-sm text-red-600">{{ apiErrors.description }}</p>
      </div>

      <!-- Hình ảnh -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh</label>
        <div class="flex items-start space-x-4">
          <div v-if="imagePreview || (category && category.image)" class="w-24 h-24 border rounded-md overflow-hidden">
            <img :src="imagePreview || (category && category.image)" alt="Image preview" class="w-full h-full object-contain" />
          </div>
          <div class="flex-1">
            <input
              type="file"
              @change="handleImageChange"
              accept="image/*"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              :class="{ 'border-red-500': apiErrors.image }"
            />
            <p v-if="apiErrors.image" class="mt-1 text-sm text-red-600">{{ apiErrors.image }}</p>
            <p class="mt-1 text-sm text-gray-500">PNG, JPG hoặc GIF (tối đa 2MB)</p>
          </div>
        </div>
      </div>

      <!-- Trạng thái -->
      <div>
        <div class="flex items-center">
          <input
            id="status"
            v-model="formData.status"
            type="checkbox"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
          />
          <label for="status" class="ml-2 block text-sm text-gray-900">Hoạt động</label>
        </div>
        <p v-if="apiErrors.status" class="mt-1 text-sm text-red-600">{{ apiErrors.status }}</p>
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
          :disabled="loading"
        >
          {{ loading ? 'Đang xử lý...' : 'Lưu' }}
        </button>
      </div>
    </form>
  </Modal>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue'
import Modal from '@/components/Modal.vue'

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
  },
  loading: Boolean
})

const emit = defineEmits(['submit', 'cancel'])

// Form title
const formTitle = computed(() => props.category ? 'Chỉnh sửa danh mục' : 'Thêm danh mục mới')

// Modal visibility
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})

// Form data
const formData = reactive({
  name: '',
  parent_id: '',
  slug: '',
  description: '',
  image: null,
  status: true,
  remove_image: false
})

// Image preview
const imagePreview = ref(null)

// Watch category prop to update form data
watch(() => props.category, (newCategory) => {
  if (newCategory) {
    formData.name = newCategory.name || ''
    formData.parent_id = newCategory.parent_id || ''
    formData.slug = newCategory.slug || ''
    formData.description = newCategory.description || ''
    formData.status = newCategory.status || false
    formData.remove_image = false
    imagePreview.value = null
  } else {
    resetForm()
  }
}, { immediate: true })

// Reset form
function resetForm() {
  formData.name = ''
  formData.parent_id = ''
  formData.slug = ''
  formData.description = ''
  formData.image = null
  formData.status = true
  formData.remove_image = false
  imagePreview.value = null
}

// Handle image change
function handleImageChange(event) {
  const file = event.target.files[0]
  if (!file) return

  formData.image = file
  formData.remove_image = false

  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

// Handle form submit
function handleSubmit() {
  // Create FormData object for file upload
  const submitData = new FormData()
  submitData.append('name', formData.name)
  submitData.append('slug', formData.slug)
  submitData.append('description', formData.description)
  submitData.append('status', formData.status ? 1 : 0)
  
  // Parent ID
  if (formData.parent_id !== '') {
    submitData.append('parent_id', formData.parent_id)
  }
  
  // Image
  if (formData.image) {
    submitData.append('image', formData.image)
  }
  
  if (formData.remove_image) {
    submitData.append('remove_image', 1)
  }

  emit('submit', submitData)
}

// Close modal
function onClose() {
  emit('cancel')
}
</script> 