<template>
  <div>
    <div v-if="loading" class="flex justify-center items-center p-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-2 text-gray-600">Đang tải dữ liệu...</span>
    </div>
    <PermissionForm 
      v-else-if="showModal"
      :show="showModal"
      :permission="permissionData"
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
import { ref, watch, reactive } from 'vue'
import { getEnumSync } from '@/constants/enums'
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
const permissionData = ref(null)
const loading = ref(false)
const apiErrors = reactive({})

watch(() => props.show, (newValue) => {
  showModal.value = newValue
  if (newValue) {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    fetchStatusEnums()
    fetchParentOptions()
    
    // Luôn fetch dữ liệu chi tiết từ API khi mở modal
    if (props.permission?.id) {
      fetchPermissionDetails()
    }
  } else {
    permissionData.value = null // Reset data khi đóng modal
    loading.value = false
  }
}, { immediate: true })

async function fetchPermissionDetails() {
  if (!props.permission?.id) return
  
  loading.value = true
  try {
    const response = await axios.get(`/api/admin/permissions/${props.permission.id}`)
    
    permissionData.value = response.data.data || response.data
  } catch (error) {
    
    // Fallback về dữ liệu từ list view nếu API lỗi
    permissionData.value = props.permission
  } finally {
    loading.value = false
  }
}

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
    // Loại bỏ permission hiện tại khỏi danh sách parent options
    const allPermissions = response.data.data || []
    parentOptions.value = allPermissions.filter(permission => permission.id !== props.permission?.id)
  } catch (error) {
    parentOptions.value = []
  }
}

async function handleSubmit(formData) {
  try {
    if (!props.permission) return;
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    const dataWithMethod = {
      ...formData,
      _method: 'PUT'
    }
    await axios.post(endpoints.permissions.update(props.permission.id), dataWithMethod)
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
