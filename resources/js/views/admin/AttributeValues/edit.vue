<template>
  <div>
    <div v-if="loading" class="flex justify-center items-center p-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-2 text-gray-600">Đang tải dữ liệu...</span>
    </div>
    <AttributeValueForm 
      v-else-if="showModal"
      :show="showModal"
      :attribute-value="attributeValueData"
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
import { ref, watch, reactive } from 'vue'
import { getEnumSync } from '@/constants/enums'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  attributeValue: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])

const showModal = ref(false)
const statusEnums = ref([])
const attributeOptions = ref([])
const attributeValueData = ref(null)
const loading = ref(false)
const apiErrors = reactive({})

watch(() => props.show, (newValue) => {
  showModal.value = newValue
  if (newValue) {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    fetchStatusEnums()
    fetchAttributeOptions()
    
    // Luôn fetch dữ liệu chi tiết từ API khi mở modal
    if (props.attributeValue?.id) {
      fetchAttributeValueDetails()
    }
  } else {
    attributeValueData.value = null // Reset data khi đóng modal
    loading.value = false
  }
}, { immediate: true })

async function fetchAttributeValueDetails() {
  if (!props.attributeValue?.id) return
  
  loading.value = true
  try {
    const response = await axios.get(`/api/admin/attribute-values/${props.attributeValue.id}`)
    
    attributeValueData.value = response.data.data || response.data
  } catch (error) {
    
    // Fallback về dữ liệu từ list view nếu API lỗi
    attributeValueData.value = props.attributeValue
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

async function fetchAttributeOptions() {
  try {
    const response = await axios.get(endpoints.attributes.list, { params: { per_page: 100 } })
    attributeOptions.value = response.data.data || []
  } catch (error) {
    attributeOptions.value = []
  }
}

async function handleSubmit(formData) {
  try {
    if (!props.attributeValue) return;
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    const dataWithMethod = {
      ...formData,
      _method: 'PUT'
    }
    await axios.post(endpoints.attributeValues.update(props.attributeValue.id), dataWithMethod)
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
