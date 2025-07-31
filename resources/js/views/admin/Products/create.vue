<template>
  <div>
    <ProductForm 
      v-if="showModal"
      :show="showModal"
      :api-errors="apiErrors"
      :status-options="statusOptions"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import ProductForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, reactive, watch, onMounted } from 'vue'
import apiClient from '@/api/apiClient'

const props = defineProps({
  show: Boolean,
  onClose: Function
})
const emit = defineEmits(['created'])

const showModal = ref(false)
const apiErrors = reactive({})
const statusOptions = ref({})

watch(() => props.show, (newValue) => {
  console.log('Create modal show changed:', newValue)
  showModal.value = newValue
  if (newValue) {
    console.log('Modal opened, fetching status options...')
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    fetchStatusOptions()
  }
}, { immediate: true })

async function fetchStatusOptions() {
  try {
    const response = await apiClient.get(endpoints.enums('ProductStatus'))
    statusOptions.value = response.data
  } catch (error) {
    statusOptions.value = {
      active: 'Đang bán',
      inactive: 'Ngừng bán',
      draft: 'Bản nháp'
    }
  }
}

async function handleSubmit(formData) {
  try {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    await apiClient.post(endpoints.products.create, formData)
    emit('created')
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
