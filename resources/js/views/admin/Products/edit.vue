<template>
  <div>
    <ProductForm 
      v-if="showModal"
      :show="showModal"
      :product="product"
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
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  product: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])

const showModal = ref(false)
const apiErrors = reactive({})
const statusOptions = ref({})

watch(() => props.show, (newValue) => {
  showModal.value = newValue
  if (newValue) {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    fetchStatusOptions()
  }
}, { immediate: true })

async function fetchStatusOptions() {
  try {
    const response = await axios.get(endpoints.enums('ProductStatus'))
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
    if (!props.product) return;
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    await axios.post(endpoints.products.update(props.product.id), formData)
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