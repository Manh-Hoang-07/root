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
          autocomplete="name"
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
        <!-- Mô tả -->
        <FormField
          v-model="form.description"
          label="Mô tả"
          name="description"
          type="textarea"
          :error="errors.description"
          autocomplete="off"
          @update:model-value="clearError('description')"
        />
        <!-- Hình ảnh -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh</label>
          <ImageUploader v-model="form.image" :default-url="imageUrl" />
          <div v-if="errors.image" class="text-red-500 text-sm mt-1">{{ errors.image }}</div>
        </div>
        <!-- Trạng thái -->
        <FormField
          v-model="form.status"
          label="Trạng thái"
          name="status"
          type="select"
          :options="statusOptions"
          :error="errors.status"
          @update:model-value="clearError('status')"
        />
      </template>
    </FormWrapper>
  </Modal>
</template>
<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import Modal from '@/components/Core/Modal.vue'
import FormWrapper from '@/components/Core/FormWrapper.vue'
import FormField from '@/components/Core/FormField.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'
import { useFormDefaults } from '@/utils/useFormDefaults'
import { useUrl } from '@/utils/useUrl'
import formToFormData from '@/utils/formToFormData'
import ImageUploader from '@/components/Core/ImageUploader.vue'

const props = defineProps({
  show: Boolean,
  category: Object,
  parentCategories: {
    type: Array,
    default: () => []
  },
  statusEnums: {
    type: Array,
    default: () => []
  },
  apiErrors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['submit', 'cancel'])

const formTitle = computed(() => props.category ? 'Chỉnh sửa danh mục' : 'Thêm danh mục mới')
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const defaultValues = useFormDefaults(props, 'category', {
  name: '',
  parent_id: '',
  slug: '',
  description: '',
  status: '',
  image: null,
  remove_image: false
})
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
  description: [
    { max: [500, 'Mô tả không được vượt quá 500 ký tự.'] }
  ],
  parent_id: [
    { notSelf: ['Danh mục không thể là danh mục cha của chính nó.'] }
  ]
}))
function handleSubmit(form) {
  emit('submit', form) // chỉ truyền object dữ liệu, không gọi formToFormData ở đây
}
const parentCategoryOptions = computed(() => {
  const options = [{ value: '', label: 'Không có (Danh mục gốc)' }]
  if (Array.isArray(props.parentCategories)) {
    options.push(...props.parentCategories.map(cat => ({ value: cat.id, label: cat.name })))
  }
  return options
})
const statusOptions = computed(() =>
  (props.statusEnums || []).map(opt => ({
    value: opt.id,
    label: opt.name
  }))
)
const imageUrl = useUrl(props, 'category', 'image')
</script> 
