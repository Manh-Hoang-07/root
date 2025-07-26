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
          <label class="block text-sm font-medium text-gray-700 mb-1" for="brand-image">Logo</label>
          <ImageUploader
            v-model="form.image"
            :default-url="imageUrl"
            @remove="form.remove_image = true"
          />
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
import ImageUploader from '@/components/ImageUploader.vue'

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
  description: '',
  status: '',
  image: null,
  remove_image: false
})
const imageUrl = useUrl(props, 'brand', 'image')
const validationRules = computed(() => ({
  name: [
    { required: 'Tên thương hiệu là bắt buộc.' },
    { max: [100, 'Tên thương hiệu không được vượt quá 100 ký tự.'] }
  ],
  description: [
    { max: [500, 'Mô tả không được vượt quá 500 ký tự.'] }
  ]
}))
function handleSubmit(form) {
  emit('submit', form) // KHÔNG gọi formToFormData ở đây!
}
const statusOptions = computed(() =>
  (props.statusEnums || []).map(opt => ({
    value: opt.id,
    label: opt.name
  }))
)
function onClose() {
  emit('cancel')
}
</script> 