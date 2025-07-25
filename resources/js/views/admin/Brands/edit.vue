<template>
  <div>
    <BrandForm 
      v-if="showModal"
      :show="showModal"
      :brand="brand"
      :api-errors="apiErrors"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import BrandForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, watch } from 'vue'
import { useApiFormSubmit } from '@/utils/useApiFormSubmit'

const props = defineProps({
  show: Boolean,
  brand: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])

const showModal = ref(false)

const { apiErrors, submit } = useApiFormSubmit({
  endpoint: endpoints.brands.update(props.brand?.id),
  emit,
  onClose: props.onClose,
  eventName: 'updated',
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