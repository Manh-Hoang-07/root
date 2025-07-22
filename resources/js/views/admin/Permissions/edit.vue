<template>
  <div>
    <PermissionForm 
      v-if="showModal"
      :show="showModal"
      :permission="permission"
      :api-errors="apiErrors"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import PermissionForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, reactive, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  permission: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])

const showModal = ref(false)
const apiErrors = reactive({})

watch(() => props.show, (newValue) => {
  showModal.value = newValue
}, { immediate: true })

async function handleSubmit(formData) {
  try {
    if (!props.permission) return;
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    const response = await axios.post(endpoints.permissions.update(props.permission.id), formData)
    emit('updated')
    props.onClose()
  } catch (error) {
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors
      for (const field in errors) {
        if (Array.isArray(errors[field])) {
          apiErrors[field] = errors[field][0]
        } else {
          apiErrors[field] = errors[field]
        }
      }
    }
  }
}

function onClose() {
  if (props.onClose) {
    props.onClose()
  }
}
</script> 