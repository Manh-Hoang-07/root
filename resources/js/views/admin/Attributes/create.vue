<template>
  <div>
    <AttributeForm 
      v-if="showModal"
      :show="showModal"
      :status-enums="statusEnums"
      :api-errors="apiErrors"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import AttributeForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, watch } from 'vue'
import { getEnumSync } from '@/constants/enums'
import { useApiFormSubmit } from '@/utils/useApiFormSubmit'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  onClose: Function
})
const emit = defineEmits(['created'])

const showModal = ref(false)
const statusEnums = ref([])

const { apiErrors, submit } = useApiFormSubmit({
  endpoint: endpoints.attributes.create,
  emit,
  onClose: props.onClose,
  eventName: 'created',
  method: 'post'
})

// Watch show prop để cập nhật showModal
watch(() => props.show, (newValue) => {
  showModal.value = newValue
  if (newValue) fetchStatusEnums()
}, { immediate: true })

async function fetchStatusEnums() {
  try {
    const response = { data: getEnumSync('basic_status') }
    statusEnums.value = Array.isArray(response.data.data) ? response.data.data : []
  } catch (error) {
    statusEnums.value = []
  }
}

async function handleSubmit(formData) {
  await submit(formData)
}

function onClose() {
  if (props.onClose) {
    props.onClose()
  }
}
</script> 
