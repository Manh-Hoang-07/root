<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="validateAndSubmit" class="space-y-4">
      <!-- SKU -->
      <div>
        <label class="block text-sm font-medium mb-1">SKU <span class="text-red-500">*</span></label>
        <input v-model="formData.sku" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.sku || apiErrors.sku }" maxlength="100" />
        <p v-if="validationErrors.sku" class="mt-1 text-sm text-red-600">{{ validationErrors.sku }}</p>
        <p v-else-if="apiErrors.sku" class="mt-1 text-sm text-red-600">{{ apiErrors.sku }}</p>
      </div>

      <!-- Barcode -->
      <div>
        <label class="block text-sm font-medium mb-1">Barcode</label>
        <input v-model="formData.barcode" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.barcode || apiErrors.barcode }" maxlength="100" />
        <p v-if="validationErrors.barcode" class="mt-1 text-sm text-red-600">{{ validationErrors.barcode }}</p>
        <p v-else-if="apiErrors.barcode" class="mt-1 text-sm text-red-600">{{ apiErrors.barcode }}</p>
      </div>

      <!-- Giá và giá khuyến mãi -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Giá gốc <span class="text-red-500">*</span></label>
          <input v-model="formData.price" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.price || apiErrors.price }" />
          <p v-if="validationErrors.price" class="mt-1 text-sm text-red-600">{{ validationErrors.price }}</p>
          <p v-else-if="apiErrors.price" class="mt-1 text-sm text-red-600">{{ apiErrors.price }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Giá khuyến mãi</label>
          <input v-model="formData.sale_price" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.sale_price || apiErrors.sale_price }" />
          <p v-if="validationErrors.sale_price" class="mt-1 text-sm text-red-600">{{ validationErrors.sale_price }}</p>
          <p v-else-if="apiErrors.sale_price" class="mt-1 text-sm text-red-600">{{ apiErrors.sale_price }}</p>
        </div>
      </div>

      <!-- Số lượng -->
      <div>
        <label class="block text-sm font-medium mb-1">Số lượng <span class="text-red-500">*</span></label>
        <input v-model="formData.quantity" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.quantity || apiErrors.quantity }" />
        <p v-if="validationErrors.quantity" class="mt-1 text-sm text-red-600">{{ validationErrors.quantity }}</p>
        <p v-else-if="apiErrors.quantity" class="mt-1 text-sm text-red-600">{{ apiErrors.quantity }}</p>
      </div>

      <!-- Ảnh biến thể -->
      <div>
        <label class="block text-sm font-medium mb-1">Ảnh biến thể (URL)</label>
        <input v-model="formData.image" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.image || apiErrors.image }" maxlength="255" />
        <p v-if="validationErrors.image" class="mt-1 text-sm text-red-600">{{ validationErrors.image }}</p>
        <p v-else-if="apiErrors.image" class="mt-1 text-sm text-red-600">{{ apiErrors.image }}</p>
      </div>

      <!-- Thuộc tính -->
      <div>
        <label class="block text-sm font-medium mb-1">Thuộc tính</label>
        <div class="space-y-2">
          <div v-for="attribute in attributes" :key="attribute.id" class="flex items-center space-x-3">
            <span class="text-sm font-medium text-gray-700 w-24">{{ attribute.name }}:</span>
            <select 
              v-model="formData.attribute_values[attribute.id]" 
              class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">Chọn giá trị</option>
              <option v-for="value in attribute.values" :key="value.id" :value="value.id">
                {{ value.value }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Trạng thái -->
      <div>
        <label class="block text-sm font-medium mb-1">Trạng thái <span class="text-red-500">*</span></label>
        <select v-model="formData.status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.status || apiErrors.status }">
          <option v-for="(label, value) in statusOptions" :key="value" :value="value">{{ label }}</option>
        </select>
        <p v-if="validationErrors.status" class="mt-1 text-sm text-red-600">{{ validationErrors.status }}</p>
        <p v-else-if="apiErrors.status" class="mt-1 text-sm text-red-600">{{ apiErrors.status }}</p>
      </div>

      <!-- Buttons -->
      <div class="flex justify-end space-x-3 pt-4">
        <button type="button" @click="onClose" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">Huỷ</button>
        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none" :disabled="isSubmitting">{{ isSubmitting ? 'Đang xử lý...' : (mode === 'edit' ? 'Cập nhật' : 'Thêm mới') }}</button>
      </div>
    </form>
  </Modal>
</template>

<script setup>
import { ref, computed, reactive, watch, onMounted } from 'vue'
import { getEnumSync } from '@/constants/enums'
import Modal from '@/components/Core/Modal.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  variant: Object,
  productId: [String, Number],
  apiErrors: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel', 'created', 'updated'])

const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Chỉnh sửa biến thể' : 'Thêm biến thể mới')

const formData = reactive({
  sku: '',
  barcode: '',
  price: '',
  sale_price: '',
  quantity: '',
  image: '',
  status: '',
  attribute_values: {}
})

const validationErrors = reactive({})
const isSubmitting = ref(false)
const statusOptions = ref({})
const attributes = ref([])

onMounted(() => {
  fetchStatusOptions()
  fetchAttributes()
})

function fetchStatusOptions() {
  const enumData = getEnumSync('variant_status')
  statusOptions.value = enumData.map(item => ({
    value: item.value,
    label: item.label
  }))
}

async function fetchAttributes() {
  try {
    const response = await axios.get(endpoints.attributes.list)
    attributes.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching attributes:', error)
    attributes.value = []
  }
}

watch(() => props.variant, (val) => {
  if (val) {
    formData.sku = val.sku || ''
    formData.barcode = val.barcode || ''
    formData.price = val.price || ''
    formData.sale_price = val.sale_price || ''
    formData.quantity = val.quantity || ''
    formData.image = val.image || ''
    formData.status = val.status || Object.keys(statusOptions.value)[0] || ''
    
    // Set attribute values
    if (val.attribute_values) {
      val.attribute_values.forEach(attr => {
        formData.attribute_values[attr.attribute_id] = attr.id
      })
    }
  } else {
    resetForm()
  }
}, { immediate: true })

function resetForm() {
  formData.sku = ''
  formData.barcode = ''
  formData.price = ''
  formData.sale_price = ''
  formData.quantity = ''
  formData.image = ''
  formData.status = Object.keys(statusOptions.value)[0] || ''
  formData.attribute_values = {}
  clearErrors()
}

function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}

function validateForm() {
  clearErrors()
  
  if (!formData.sku.trim()) {
    validationErrors.sku = 'SKU là bắt buộc'
  } else if (formData.sku.length > 100) {
    validationErrors.sku = 'SKU không được vượt quá 100 ký tự'
  }

  if (!formData.price || isNaN(formData.price)) {
    validationErrors.price = 'Giá là bắt buộc và phải là số'
  } else if (Number(formData.price) < 0) {
    validationErrors.price = 'Giá không được âm'
  }

  if (formData.sale_price && (isNaN(formData.sale_price) || Number(formData.sale_price) < 0)) {
    validationErrors.sale_price = 'Giá khuyến mãi phải là số và không được âm'
  }

  if (!formData.quantity || isNaN(formData.quantity)) {
    validationErrors.quantity = 'Số lượng là bắt buộc và phải là số'
  } else if (Number(formData.quantity) < 0) {
    validationErrors.quantity = 'Số lượng không được âm'
  }

  if (formData.image && formData.image.length > 255) {
    validationErrors.image = 'Đường dẫn ảnh không được vượt quá 255 ký tự'
  }

  return Object.keys(validationErrors).length === 0
}

function validateAndSubmit() {
  if (!validateForm()) return
  isSubmitting.value = true
  try {
    const submitData = new FormData()
    
    // Add basic fields
    Object.entries(formData).forEach(([key, value]) => {
      if (key !== 'attribute_values' && value !== null && value !== undefined && value !== '') {
        submitData.append(key, value)
      }
    })
    
    // Add product_id
    submitData.append('product_id', props.productId)
    
    // Add attribute values
    Object.entries(formData.attribute_values).forEach(([attrId, valueId]) => {
      if (valueId) {
        submitData.append(`attribute_values[${attrId}]`, valueId)
      }
    })
    
    emit('submit', submitData)
  } finally {
    isSubmitting.value = false
  }
}

function onClose() {
  emit('cancel')
}
</script> 