<template>
  <div>
    <WarehouseForm 
      v-if="showModal"
      :show="showModal"
      :warehouse="warehouse"
      :api-errors="apiErrors"
      :status-options="statusOptions"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import WarehouseForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, reactive, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  warehouse: Object,
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
    const response = await axios.get(endpoints.enums('BasicStatus'))
    statusOptions.value = response.data
  } catch (error) {
    statusOptions.value = {
      active: 'Hoạt động',
      inactive: 'Không hoạt động'
    }
  }
}

async function handleSubmit(formData) {
  try {
    if (!props.warehouse) return;
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    await axios.post(endpoints.warehouses.update(props.warehouse.id), formData)
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