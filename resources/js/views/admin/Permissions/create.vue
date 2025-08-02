<template>
  <div>
    <PermissionForm 
      v-if="showModal"
      :show="showModal"
      :parent-options="parentOptions"
      :status-enums="statusEnums"
      :api-errors="apiErrors"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import PermissionForm from './form.vue'
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
const parentOptions = ref([])

const { apiErrors, submit } = useApiFormSubmit({
  endpoint: endpoints.permissions.create,
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
    fetchParentOptions()
  }
}, { immediate: true })

async function fetchStatusEnums() {
  try {
    const enumData = getEnumSync('basic_status')
    statusEnums.value = Array.isArray(enumData) ? enumData : []
  } catch (error) {
    statusEnums.value = []
  }
}

async function fetchParentOptions() {
  try {
    const response = await axios.get(endpoints.permissions.list, { 
      params: { 
        per_page: 100,
        relations: 'parent'
      } 
    })
    parentOptions.value = response.data.data || []
  } catch (error) {
    parentOptions.value = []
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
