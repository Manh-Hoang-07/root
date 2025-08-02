<template>
  <div>
    <div v-if="loading" class="flex justify-center items-center p-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-2 text-gray-600">Đang tải dữ liệu...</span>
    </div>
    <RoleForm 
      v-else-if="showModal"
      :show="showModal"
      :role="roleData"
      :status-enums="statusEnums"
      :api-errors="apiErrors"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import RoleForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, reactive, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  role: Object,
  statusEnums: {
    type: Array,
    default: () => []
  },
  onClose: Function
})
const emit = defineEmits(['updated'])

const showModal = ref(false)
const roleData = ref(null)
const loading = ref(false)
const apiErrors = reactive({})

watch(() => props.show, (newValue) => {
  showModal.value = newValue
  if (newValue) {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    
    // Luôn fetch dữ liệu chi tiết từ API khi mở modal
    if (props.role?.id) {
      fetchRoleDetails()
    }
  } else {
    roleData.value = null // Reset data khi đóng modal
    loading.value = false
  }
}, { immediate: true })

async function fetchRoleDetails() {
  if (!props.role?.id) return
  
  loading.value = true
  try {
    const response = await axios.get(`/api/admin/roles/${props.role.id}`)
    
    roleData.value = response.data.data || response.data
  } catch (error) {
    
    // Fallback về dữ liệu từ list view nếu API lỗi
    roleData.value = props.role
  } finally {
    loading.value = false
  }
}

async function handleSubmit(formData) {
  try {
    if (!props.role) return;
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    
    // Thêm _method = PUT để Laravel hiểu đây là PUT request
    const dataWithMethod = {
      ...formData,
      _method: 'PUT'
    }
    
    const response = await axios.post(endpoints.roles.update(props.role.id), dataWithMethod)
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

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style>
.multiselect__tag, .multiselect__single, .multiselect__option {
  color: #222 !important;
  font-size: 14px !important;
  min-width: 40px;
}
/* Thêm scroll cho vùng tag khi tràn */
.multiselect__tags {
  max-height: 90px;
  overflow-y: auto;
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
}
</style> 
