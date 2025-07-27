<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <FormWrapper
      :default-values="defaultValues"
      :rules="validationRules"
      :api-errors="apiErrors"
      submit-text="Đổi mật khẩu"
      @submit="handleSubmit"
      @cancel="onClose"
    >
      <template #default="{ form, errors, clearError, isSubmitting }">
        <!-- Mật khẩu mới -->
        <FormField
          v-model="form.password"
          label="Mật khẩu mới"
          name="password"
          type="password"
          :error="errors.password"
          required
          autocomplete="new-password"
          @update:model-value="clearError('password')"
        />
        
        <!-- Xác nhận mật khẩu mới -->
        <FormField
          v-model="form.password_confirmation"
          label="Xác nhận mật khẩu mới"
          name="password_confirmation"
          type="password"
          :error="errors.password_confirmation"
          required
          autocomplete="new-password"
          @update:model-value="clearError('password_confirmation')"
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

const props = defineProps({
  show: Boolean,
  user: Object,
  apiErrors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['submit', 'cancel'])

const formTitle = computed(() => `Đổi mật khẩu cho ${props.user?.username || 'người dùng'}`)
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})

const defaultValues = {
  password: '',
  password_confirmation: ''
}

const validationRules = {
  password: [
    { required: 'Mật khẩu mới là bắt buộc.' },
    { min: [8, 'Mật khẩu phải có ít nhất 8 ký tự.'] }
  ],
  password_confirmation: [
    { required: 'Vui lòng xác nhận mật khẩu mới.' },
    { match: ['password', 'Mật khẩu xác nhận không khớp.'] }
  ]
}

function handleSubmit(form) {
  emit('submit', form)
}

function onClose() {
  emit('cancel')
}
</script> 
