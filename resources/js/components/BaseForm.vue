<template>
  <FormLayout @submit="handleSubmit" @cancel="$emit('cancel')">
    <slot :form="form" />
  </FormLayout>
</template>

<script setup>
import { watch } from 'vue'
import FormLayout from '@/components/FormLayout.vue'
import useFormData from '@/composables/useFormData'

const props = defineProps({
  initialData: {
    type: Object,
    default: () => ({})
  },
  defaultValues: {
    type: Object,
    default: () => ({})
  },
  useFormData: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit', 'cancel'])

// Sử dụng composable
const { form, prepareData, resetForm, setFormData } = useFormData()

// Khởi tạo form
const initForm = () => {
  if (props.initialData && Object.keys(props.initialData).length > 0) {
    setFormData(props.initialData)
  } else {
    resetForm(props.defaultValues)
  }
}

// Watch để cập nhật form khi initialData thay đổi
watch(() => props.initialData, () => {
  initForm()
}, { immediate: true, deep: true })

const handleSubmit = () => {
  emit('submit', form.value)
}
</script> 