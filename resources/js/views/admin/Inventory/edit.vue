<template>
  <div>
    <div v-if="loading" class="flex justify-center items-center p-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-2 text-gray-600">Đang tải dữ liệu...</span>
    </div>
    <InventoryForm 
      v-else-if="showModal"
      :show="showModal"
      :api-errors="apiErrors"
      :products="products"
      :warehouses="warehouses"
      :inventory="inventoryData"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>

<script setup>
import InventoryForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, reactive, watch } from 'vue'
import apiClient from '@/api/apiClient'

const props = defineProps({
  show: Boolean,
  inventory: Object,
  products: {
    type: Array,
    default: () => []
  },
  warehouses: {
    type: Array,
    default: () => []
  },
  onClose: Function
})

const emit = defineEmits(['updated'])

const showModal = ref(false)
const inventoryData = ref(null)
const loading = ref(false)
const apiErrors = reactive({})

watch(() => props.show, (newValue) => {
  showModal.value = newValue
  if (newValue) {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    
    // Luôn fetch dữ liệu chi tiết từ API khi mở modal
    if (props.inventory?.id) {
      fetchInventoryDetails()
    }
  } else {
    inventoryData.value = null // Reset data khi đóng modal
    loading.value = false
  }
}, { immediate: true })

async function fetchInventoryDetails() {
  if (!props.inventory?.id) return
  
  loading.value = true
  try {
    const response = await apiClient.get(`/api/admin/inventory/${props.inventory.id}`)
    
    inventoryData.value = response.data.data || response.data
  } catch (error) {
    console.error('Fetch inventory details error:', error)
    // Fallback về dữ liệu từ list view nếu API lỗi
    inventoryData.value = props.inventory
  } finally {
    loading.value = false
  }
}

async function handleSubmit(formData) {
  try {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    await apiClient.put(endpoints.inventory.update(props.inventory.id), formData)
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
