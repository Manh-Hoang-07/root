<template>
  <div>
    <PermissionForm 
      v-if="showModal"
      :show="showModal"
      :permission="permission"
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
import { useApiFormSubmit } from '@/utils/useApiFormSubmit'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  permission: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])

const showModal = ref(false)
const statusEnums = ref([])
const parentOptions = ref([])

const { apiErrors, submit } = useApiFormSubmit({
  endpoint: endpoints.permissions.update(props.permission?.id),
  emit,
  onClose: props.onClose,
  eventName: 'updated',
  method: 'put'
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
    const response = await axios.get(endpoints.enums('BasicStatus'))
    statusEnums.value = Array.isArray(response.data) ? response.data : []
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
    // Loại bỏ permission hiện tại khỏi danh sách parent options
    const allPermissions = response.data.data || []
    parentOptions.value = allPermissions.filter(permission => permission.id !== props.permission?.id)
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