<template>
  <FormLayout @submit.prevent="handleSubmit" @cancel="$emit('cancel')">
    <slot :form="form" :errors="errors" :setServerErrors="setServerErrors" />
  </FormLayout>
</template>

<script setup>
import { watch, ref } from 'vue'
import FormLayout from '@/components/Core/FormLayout.vue'
import useFormData from '@/composables/useFormData'
import useFormErrors from '@/composables/useFormErrors'

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
  },
  rules: {
    type: Object,
    default: () => ({})
  },
  validateOnSubmit: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['submit', 'cancel', 'error'])

// Sử dụng composable cho form data
const { form, prepareData, resetForm, setFormData } = useFormData()

// Sử dụng composable cho form errors
const { errors, setErrors, clearError, clearAll, setServerErrors } = useFormErrors()

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

// Xử lý submit form
const handleSubmit = () => {
  // Nếu cần validate trước khi submit
  if (props.validateOnSubmit && props.rules && Object.keys(props.rules).length > 0) {
    // Import validateForm chỉ khi cần
    import('@/utils/validateForm').then(({ default: validateForm }) => {
      clearAll() // Xóa lỗi cũ
      const validationErrors = validateForm(form.value, props.rules)
      
      if (Object.keys(validationErrors).length > 0) {
        // Có lỗi validation
        setErrors(validationErrors)
        emit('error', validationErrors)
        return
      }
      
      // Không có lỗi, tiếp tục submit
      const formData = props.useFormData ? prepareData(form.value) : form.value
      emit('submit', formData)
    })
  } else {
    // Không cần validate, submit luôn
    const formData = props.useFormData ? prepareData(form.value) : form.value
    emit('submit', formData)
  }
}

// Expose các phương thức cần thiết
defineExpose({
  form,
  errors,
  resetForm,
  setFormData,
  clearError,
  clearAll,
  setServerErrors
})
</script> 