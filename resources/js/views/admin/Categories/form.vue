<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <FormWrapper
      :default-values="defaultValues"
      :rules="validationRules"
      :api-errors="apiErrors"
      :submit-text="category ? 'Cập nhật' : 'Thêm mới'"
      @submit="handleSubmit"
      @cancel="onClose"
    >
      <template #default="{ form, errors, clearError, isSubmitting }">
        <!-- Tên danh mục -->
        <FormField
          v-model="form.name"
          label="Tên danh mục"
          name="name"
          :error="errors.name"
          required
          @update:model-value="clearError('name')"
        />
        <!-- Danh mục cha -->
        <FormField
          v-model="form.parent_id"
          label="Danh mục cha"
          name="parent_id"
          type="select"
          :options="parentCategoryOptions"
          :error="errors.parent_id"
          @update:model-value="clearError('parent_id')"
        >
          <template #help>Không có (Danh mục gốc)</template>
        </FormField>
        <!-- Slug -->
        <FormField
          v-model="form.slug"
          label="Slug"
          name="slug"
          :error="errors.slug"
          @update:model-value="clearError('slug')"
        >
          <template #help>Để trống để tự động tạo từ tên</template>
        </FormField>
        <!-- Mô tả -->
        <FormField
          v-model="form.description"
          label="Mô tả"
          name="description"
          type="textarea"
          :error="errors.description"
          @update:model-value="clearError('description')"
        />
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
                :class="{ 'border-red-500': errors.image }"
              />
              <p v-if="errors.image" class="mt-1 text-sm text-red-600">{{ errors.image }}</p>
              <p class="mt-1 text-sm text-gray-500">PNG, JPG hoặc GIF (tối đa 2MB)</p>
            </div>
          </div>
        </div>
        <!-- Trạng thái -->
        <FormField
          v-model="form.status"
          label="Trạng thái"
          name="status"
          type="select"
          :options="statusOptionsArr"
          :error="errors.status"
          @update:model-value="clearError('status')"
        />
        <!-- Buttons sẽ nằm trong FormWrapper -->
      </template>
    </FormWrapper>
  </Modal>
</template>
<script setup>
import { ref, computed, reactive, watch, onMounted } from 'vue'
import Modal from '@/components/Modal.vue'
import FormWrapper from '@/components/FormWrapper.vue'
import FormField from '@/components/FormField.vue'
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
const defaultValues = computed(() => ({
  name: props.category?.name || '',
  parent_id: props.category?.parent_id || '',
  slug: props.category?.slug || '',
  description: props.category?.description || '',
  status: props.category?.status === true || props.category?.status === 1 ? 1 : 0,
  image: null,
  remove_image: false
}))
const imagePreview = ref(null)
watch(() => props.category, (newCategory) => {
  if (newCategory) {
    imagePreview.value = null
  } else {
    resetForm()
  }
}, { immediate: true })
function resetForm() {
  imagePreview.value = null
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
function handleSubmit(form) {
  const submitData = new FormData()
  submitData.append('name', form.name)
  submitData.append('slug', form.slug)
  submitData.append('description', form.description)
  submitData.append('status', form.status)
  if (form.parent_id !== '') {
    submitData.append('parent_id', form.parent_id)
  }
  if (form.image) {
    submitData.append('image', form.image)
  }
  if (form.remove_image) {
    submitData.append('remove_image', 1)
  }
  emit('submit', submitData)
}
const parentCategoryOptions = computed(() => {
  const options = [{ value: '', label: 'Không có (Danh mục gốc)' }]
  if (Array.isArray(props.parentCategories)) {
    options.push(...props.parentCategories.map(cat => ({ value: cat.id, label: cat.name })))
  }
  return options
})
const statusOptionsArr = computed(() => {
  if (!statusOptions.value || typeof statusOptions.value !== 'object') return []
  return Object.entries(statusOptions.value).map(([value, label]) => ({ value: Number(value), label }))
})
function handleImageChange(event) {
  const file = event.target.files[0]
  if (!file) return
  const validTypes = ['image/jpeg', 'image/png', 'image/gif']
  if (!validTypes.includes(file.type)) {
    // Đẩy lỗi vào FormWrapper qua localErrors nếu muốn
    return
  }
  if (file.size > 2 * 1024 * 1024) {
    // Đẩy lỗi vào FormWrapper qua localErrors nếu muốn
    return
  }
  imagePreview.value = null
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
function onClose() {
  emit('cancel')
}
</script> 