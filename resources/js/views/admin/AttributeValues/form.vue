<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <FormWrapper
      :default-values="defaultValues"
      :rules="validationRules"
      :api-errors="apiErrors"
      :submit-text="attributeValue ? 'Cập nhật' : 'Thêm mới'"
      @submit="handleSubmit"
      @cancel="onClose"
    >
      <template #default="{ form, errors, clearError, isSubmitting }">
        <!-- Thuộc tính cha -->
        <FormField
          v-model="form.attribute_id"
          label="Thuộc tính"
          name="attribute_id"
          type="select"
          :options="attributeOptionsFormatted"
          :error="errors.attribute_id"
          required
          @update:model-value="clearError('attribute_id')"
        />
        
        <!-- Giá trị -->
        <FormField
          v-model="form.value"
          label="Giá trị"
          name="value"
          :error="errors.value"
          required
          autocomplete="off"
          @update:model-value="clearError('value')"
        />
        
        <!-- Hiển thị -->
        <FormField
          v-model="form.name"
          label="Hiển thị"
          name="name"
          :error="errors.name"
          autocomplete="off"
          @update:model-value="clearError('name')"
        />
        
        <!-- Thứ tự -->
        <FormField
          v-model="form.sort_order"
          label="Thứ tự"
          name="sort_order"
          type="number"
          :error="errors.sort_order"
          required
          @update:model-value="clearError('sort_order')"
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
import Modal from '@/components/Modal.vue'
import FormWrapper from '@/components/FormWrapper.vue'
import FormField from '@/components/FormField.vue'
import { useFormDefaults } from '@/utils/useFormDefaults'

const props = defineProps({
  show: Boolean,
  attributeValue: Object,
  attributeOptions: {
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

const formTitle = computed(() => props.attributeValue ? 'Chỉnh sửa giá trị thuộc tính' : 'Thêm giá trị thuộc tính mới')
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})

const defaultValues = computed(() => {
  const obj = props.attributeValue || {}
  console.log('AttributeValue prop:', obj)
  const result = {
    attribute_id: obj.attrbute_id ? String(obj.attrbute_id) : '', // Sửa từ attribute_id thành attrbute_id
    value: obj.value || '',
    name: obj.name || '',
    sort_order: obj.sort_order || 0,
    description: obj.description || '',
    status: obj.status || ''
  }
  console.log('Default values:', result)
  return result
})

const validationRules = computed(() => ({
  attribute_id: [
    { required: 'Vui lòng chọn thuộc tính.' }
  ],
  value: [
    { required: 'Giá trị không được để trống.' },
    { max: [100, 'Giá trị không được vượt quá 100 ký tự.'] }
  ],
  name: [
    { max: [255, 'Tên không được vượt quá 255 ký tự.'] }
  ],
  sort_order: [
    { required: 'Thứ tự sắp xếp không được để trống.' },
    { min: [0, 'Thứ tự sắp xếp phải lớn hơn hoặc bằng 0.'] }
  ],
  description: [
    { max: [255, 'Mô tả không được vượt quá 255 ký tự.'] }
  ]
}))

const statusOptions = computed(() =>
  (props.statusEnums || []).map(opt => ({
    value: opt.id,
    label: opt.name
  }))
)

const attributeOptionsFormatted = computed(() =>
  (props.attributeOptions || []).map(attr => ({
    value: attr.id,
    label: attr.name
  }))
)

function handleSubmit(form) {
  emit('submit', form)
}

function onClose() {
  emit('cancel')
}
</script> 