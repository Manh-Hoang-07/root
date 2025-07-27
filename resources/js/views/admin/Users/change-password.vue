<template>
  <div>
    <ChangePasswordForm 
      v-if="showModal"
      :show="showModal"
      :user="user"
      :api-errors="apiErrors"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import ChangePasswordForm from './change-password-form.vue'
import endpoints from '@/api/endpoints'
import { ref, watch } from 'vue'
import { useApiFormSubmit } from '@/utils/useApiFormSubmit'

const props = defineProps({
  show: Boolean,
  user: Object,
  onClose: Function
})
const emit = defineEmits(['password-changed'])

const showModal = ref(false)

const { apiErrors, submit } = useApiFormSubmit({
  endpoint: endpoints.users.changePassword(props.user?.id),
  emit,
  onClose: props.onClose,
  eventName: 'password-changed',
  method: 'post'
})

// Watch show prop để cập nhật showModal
watch(() => props.show, (newValue) => {
  showModal.value = newValue
}, { immediate: true })

async function handleSubmit(formData) {
  await submit(formData)
}

function onClose() {
  if (props.onClose) {
    props.onClose()
  }
}
</script> 
