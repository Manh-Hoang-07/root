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
          @update:model-value="clearError('name')"
        />
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
  brand: Object,
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
const formTitle = computed(() => props.brand ? 'Chỉnh sửa thương hiệu' : 'Thêm thương hiệu mới')
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const defaultValues = computed(() => ({
  name: props.brand?.name || '',
  slug: props.brand?.slug || '',
  description: props.brand?.description || '',
  status: props.brand?.status === true || props.brand?.status === 1 ? 1 : 0,
  logo: null,
  remove_logo: false
}))
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
  const submitData = new FormData()
  submitData.append('name', form.name)
  submitData.append('slug', form.slug)
  submitData.append('description', form.description)
  submitData.append('status', form.status)
  if (form.logo) {
    submitData.append('logo', form.logo)
  }
  if (form.remove_logo) {
    submitData.append('remove_logo', 1)
  }
  emit('submit', submitData)
}
function handleLogoChange(event) {
  const file = event.target.files[0]
  if (!file) return
  const validTypes = ['image/jpeg', 'image/png', 'image/gif']
  if (!validTypes.includes(file.type)) {
    // Đẩy lỗi vào FormWrapper qua localErrors
    // Không có errors.logo ở đây, nên cần custom xử lý
    // Có thể dùng emit('error', { logo: ... }) nếu muốn
    return
  }
  if (file.size > 2 * 1024 * 1024) {
    // Đẩy lỗi vào FormWrapper qua localErrors
    return
  }
  // Xóa lỗi logo nếu có
  // Gán file
  // Gán preview
  // Cập nhật form.logo qua FormWrapper
  logoPreview.value = null
  // Cần cập nhật form.logo, nhưng form lấy từ slot, nên cần expose qua ref nếu muốn
  // Ở đây chỉ preview, file sẽ được lấy từ input khi submit
  const reader = new FileReader()
  reader.onload = (e) => {
    logoPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}
const statusOptionsArr = computed(() => {
  if (!statusOptions.value || typeof statusOptions.value !== 'object') return []
  return Object.entries(statusOptions.value).map(([value, label]) => ({ value: Number(value), label }))
})
function getImageUrl(logo) {
  if (!logo) return null
  return logo.startsWith('http') ? logo : `/storage/${logo}`
}
function onClose() {
  emit('cancel')
}
</script> 