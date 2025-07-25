<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <FormWrapper
      :default-values="defaultValues"
      :rules="validationRules"
      :api-errors="apiErrors"
      :submit-text="brand ? 'Cập nhật' : 'Thêm mới'"
      @submit="handleSubmit"
      @cancel="onClose"
    >
      <template #default="{ form, errors, clearError, isSubmitting }">
        <!-- Tên thương hiệu -->
        <FormField
          v-model="form.name"
          label="Tên thương hiệu"
          name="name"
          :error="errors.name"
          required
          autocomplete="organization"
          @update:model-value="clearError('name')"
        />
        <!-- Slug -->
        <FormField
          v-model="form.slug"
          label="Slug"
          name="slug"
          :error="errors.slug"
          autocomplete="off"
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
          autocomplete="off"
          @update:model-value="clearError('description')"
        />
        <!-- Logo -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1" for="brand-logo">Logo</label>
          <div class="flex items-start space-x-4">
            <div v-if="logoPreview || logoUrl" class="w-24 h-24 border rounded-md overflow-hidden">
              <img :src="logoPreview || logoUrl" alt="Logo preview" class="w-full h-full object-contain" />
            </div>
            <div class="flex-1">
              <input
                id="brand-logo"
                type="file"
                @change="handleLogoChange"
                accept="image/*"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                :class="{ 'border-red-500': errors.logo }"
              />
              <p v-if="errors.logo" class="mt-1 text-sm text-red-600">{{ errors.logo }}</p>
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
          :options="statusOptions"
          :error="errors.status"
          @update:model-value="clearError('status')"
        />
      </template>
    </FormWrapper>
  </Modal>
</template>
<script setup>
import { ref, computed, watch } from 'vue'
import Modal from '@/components/Modal.vue'
import FormWrapper from '@/components/FormWrapper.vue'
import FormField from '@/components/FormField.vue'
import { useFormDefaults } from '@/utils/useFormDefaults'
import { useUrl } from '@/utils/useUrl'
import formToFormData from '@/utils/formToFormData'

const props = defineProps({
  show: Boolean,
  brand: Object,
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
const formTitle = computed(() => props.brand ? 'Chỉnh sửa thương hiệu' : 'Thêm thương hiệu mới')
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const defaultValues = useFormDefaults(props, 'brand', {
  name: '',
  slug: '',
  description: '',
  status: '',
  logo: null,
  remove_logo: false
})
const logoPreview = ref(null)
watch(() => props.brand, (newBrand) => {
  if (newBrand) {
    logoPreview.value = null
  } else {
    resetForm()
  }
}, { immediate: true })
function resetForm() {
  logoPreview.value = null
}
const validationRules = computed(() => ({
  name: [
    { required: 'Tên thương hiệu là bắt buộc.' },
    { max: [100, 'Tên thương hiệu không được vượt quá 100 ký tự.'] }
  ],
  slug: [
    { pattern: [/^[a-z0-9-]*$/, 'Slug chỉ được chứa chữ thường, số và dấu gạch ngang.'] }
  ],
  description: [
    { max: [500, 'Mô tả không được vượt quá 500 ký tự.'] }
  ]
}))
function handleSubmit(form) {
  emit('submit', formToFormData(form))
}
const statusOptions = computed(() =>
  (props.statusEnums || []).map(opt => ({
    value: opt.id,
    label: opt.name
  }))
)
const logoUrl = useUrl(props, 'brand', 'logo')
function handleLogoChange(event) {
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
  logoPreview.value = null
  const reader = new FileReader()
  reader.onload = (e) => {
    logoPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}
function onClose() {
  emit('cancel')
}
</script> 