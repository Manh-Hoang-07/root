<template>
  <div>
    <ProductForm 
      v-if="showModal"
      :show="showModal"
      :product="productData"
      :api-errors="apiErrors"
      :status-options="statusOptions"
      mode="edit"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import ProductForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, reactive, watch, onMounted } from 'vue'
import axios from 'axios'
import { getEnumSync } from '@/constants/enums'

const props = defineProps({
  show: Boolean,
  product: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])

const showModal = ref(false)
const apiErrors = reactive({})
const statusOptions = ref({})
const productData = ref(null)
const loading = ref(false)

watch(() => props.show, (newValue) => {
  showModal.value = newValue
  if (newValue) {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    fetchStatusOptions()
    
    // Luôn fetch dữ liệu chi tiết từ API khi mở modal
    if (props.product?.id) {
      fetchProductDetails()
    }
  } else {
    productData.value = null // Reset data khi đóng modal
  }
}, { immediate: true })

async function fetchProductDetails() {
  if (!props.product?.id) return
  
  loading.value = true
  try {
    console.log('Fetching product details for ID:', props.product.id)
    const response = await axios.get(`/api/admin/products/${props.product.id}`)
    console.log('Product details response:', response.data)
    
    productData.value = response.data.data || response.data
    console.log('Product data set:', productData.value)
  } catch (error) {
    console.error('Error fetching product details:', error)
    // Fallback về dữ liệu từ list view nếu API lỗi
    productData.value = props.product
  } finally {
    loading.value = false
  }
}

function fetchStatusOptions() {
  const enumData = getEnumSync('product_status')
  statusOptions.value = enumData.map(item => ({
    value: item.value,
    label: item.label
  }))
}

async function handleSubmit(formData) {
  try {
    if (!props.product) return;
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    const dataWithMethod = {
      ...formData,
      _method: 'PUT'
    }
    await axios.post(endpoints.products.update(props.product.id), dataWithMethod)
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
