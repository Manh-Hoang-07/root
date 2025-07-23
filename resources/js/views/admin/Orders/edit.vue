<template>
  <div>
    <OrderForm 
      v-if="showModal"
      :show="showModal"
      :order="order"
      :api-errors="apiErrors"
      :status-options="statusOptions"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import OrderForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, reactive, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  order: Object,
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
    const response = await axios.get(endpoints.enums('OrderStatus'))
    statusOptions.value = response.data
  } catch (error) {
    statusOptions.value = {
      pending: 'Chờ xử lý',
      completed: 'Hoàn thành',
      cancelled: 'Đã huỷ'
    }
  }
}

async function handleSubmit(formData) {
  try {
    if (!props.order) return;
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    await axios.post(endpoints.orders.update(props.order.id), formData)
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