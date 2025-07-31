<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <FormWrapper
      :default-values="defaultValues"
      :rules="validationRules"
      :api-errors="apiErrors"
      :submit-text="attribute ? 'Cập nhật' : 'Thêm mới'"
      @submit="handleSubmit"
      @cancel="onClose"
    >
      <template #default="{ form, errors, clearError, isSubmitting }">
        <!-- Tên thuộc tính -->
        <FormField
          v-model="form.name"
          label="Tên thuộc tính"
          name="name"
          :error="errors.name"
          required
          autocomplete="off"
          @update:model-value="clearError('name')"
        />
        
        <!-- Kiểu thuộc tính -->
        <FormField
          v-model="form.type"
          label="Kiểu thuộc tính"
          name="type"
          type="select"
          :options="typeOptions"
          :error="errors.type"
          required
          @update:model-value="clearError('type')"
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
        
        <!-- Các tùy chọn nâng cao -->
        <div class="grid grid-cols-2 gap-4">
          <FormField
            v-model="form.is_required"
            label="Bắt buộc"
            name="is_required"
            type="checkbox"
            :error="errors.is_required"
            @update:model-value="clearError('is_required')"
          />
          
          <FormField
            v-model="form.is_unique"
            label="Duy nhất"
            name="is_unique"
            type="checkbox"
            :error="errors.is_unique"
            @update:model-value="clearError('is_unique')"
          />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <FormField
            v-model="form.is_filterable"
            label="Có thể lọc"
            name="is_filterable"
            type="checkbox"
            :error="errors.is_filterable"
            @update:model-value="clearError('is_filterable')"
          />
          
          <FormField
            v-model="form.is_searchable"
            label="Có thể tìm kiếm"
            name="is_searchable"
            type="checkbox"
            :error="errors.is_searchable"
            @update:model-value="clearError('is_searchable')"
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
import { computed } from 'vue'
import Modal from '@/components/Core/Modal.vue'
import FormWrapper from '@/components/Core/FormWrapper.vue'
import FormField from '@/components/Core/FormField.vue'
import { useFormDefaults } from '@/utils/useFormDefaults'

const props = defineProps({
  show: Boolean,
  attribute: Object,
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
const formTitle = computed(() => props.attribute ? 'Chỉnh sửa thuộc tính' : 'Thêm thuộc tính mới')
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})

const defaultValues = useFormDefaults(props, 'attribute', {
  name: '',
  type: '',
  description: '',
  is_required: false,
  is_unique: false,
  is_filterable: true,
  is_searchable: true,
  status: ''
})

const validationRules = computed(() => ({
  name: [
    { required: 'Tên thuộc tính là bắt buộc.' },
    { max: [100, 'Tên thuộc tính không được vượt quá 100 ký tự.'] }
  ],
  type: [
    { required: 'Kiểu thuộc tính là bắt buộc.' }
  ],
  description: [
    { max: [255, 'Mô tả không được vượt quá 255 ký tự.'] }
  ]
}))

const typeOptions = [
  { value: 'text', label: 'Text' },
  { value: 'number', label: 'Number' },
  { value: 'select', label: 'Select' },
  { value: 'color', label: 'Color' }
]

const statusOptions = computed(() =>
  (props.statusEnums || []).map(opt => ({
    value: opt.id,
    label: opt.name
  }))
)

function handleSubmit(form) {
  emit('submit', form)
}

function onClose() {
  emit('cancel')
}
</script> 
