<template>
  <div>
    <AttributeValueForm 
      v-if="showModal"
      :show="showModal"
      :attribute-options="attributeOptions"
      :status-enums="statusEnums"
      :api-errors="apiErrors"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import AttributeValueForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, watch } from 'vue'
import { useApiFormSubmit } from '@/utils/useApiFormSubmit'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  onClose: Function
})
const emit = defineEmits(['created'])

const showModal = ref(false)
const statusEnums = ref([])
const attributeOptions = ref([])

const { apiErrors, submit } = useApiFormSubmit({
  endpoint: endpoints.attributeValues.create,
  emit,
  onClose: props.onClose,
  eventName: 'created',
  method: 'post'
})

// Watch show prop để cập nhật showModal
watch(() => props.show, (newValue) => {
  showModal.value = newValue
  if (newValue) {
    fetchStatusEnums()
    fetchAttributeOptions()
  }
}, { immediate: true })

async function fetchStatusEnums() {
  try {
    const response = await axios.get(endpoints.enums('BasicStatus'))
    statusEnums.value = Array.isArray(response.data) ? response.data : []
  } catch (error) {
    statusEnums.value = []
  }
}

async function fetchAttributeOptions() {
  try {
    const response = await axios.get(endpoints.attributes.list, { params: { per_page: 100 } })
    console.log('Attributes response:', response.data)
    attributeOptions.value = response.data.data || []
    console.log('Attribute options:', attributeOptions.value)
  } catch (error) {
    console.error('Error fetching attributes:', error)
    attributeOptions.value = []
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
