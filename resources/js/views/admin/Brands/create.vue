<template>
  <div>
    <BrandForm 
      v-if="showModal"
      :show="showModal"
      :api-errors="apiErrors"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import BrandForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, reactive, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  onClose: Function
})
const emit = defineEmits(['created'])

const showModal = ref(false)
const apiErrors = reactive({})

// Watch show prop để cập nhật showModal
watch(() => props.show, (newValue) => {
  showModal.value = newValue
}, { immediate: true })

async function handleSubmit(formData) {
  try {
    // Xóa lỗi cũ
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    
    console.log('Submitting form data:', formData)
    const response = await axios.post(endpoints.brands.create, formData)
    console.log('API response success:', response)
    emit('created')
    props.onClose()
  } catch (error) {
    console.log('API error:', error.response)
    
    // Xử lý lỗi validation
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors
      console.log('API validation errors:', errors)
      
      // Cập nhật apiErrors reactive object
      for (const field in errors) {
        if (Array.isArray(errors[field])) {
          apiErrors[field] = errors[field][0]
        } else {
          apiErrors[field] = errors[field]
        }
      }
      
      console.log('API errors set:', apiErrors)
    }
  }
}

function onClose() {
  if (props.onClose) {
    props.onClose()
  }
}
</script> 