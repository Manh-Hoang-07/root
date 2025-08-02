<template>
  <div>
    <ImportInventoryForm 
      v-if="showModal"
      :show="showModal"
      :api-errors="apiErrors"
      :products="products"
      :warehouses="warehouses"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>

<script setup>
import ImportInventoryForm from './import-form.vue'
import endpoints from '@/api/endpoints'
import { ref, reactive, watch } from 'vue'
import apiClient from '@/api/apiClient'

const props = defineProps({
  show: Boolean,
  products: {
    type: Array,
    default: () => []
  },
  warehouses: {
    type: Array,
    default: () => []
  },
  onClose: Function
})

const emit = defineEmits(['imported'])

const showModal = ref(false)
const apiErrors = reactive({})

watch(() => props.show, (newValue) => {
  showModal.value = newValue
  if (newValue) {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
  }
}, { immediate: true })

async function handleSubmit(formData) {
  try {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    await apiClient.post(endpoints.inventory.import, formData)
    emit('imported')
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
