<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Sản phẩm <span class="text-red-500">*</span></label>
        <select v-model="formData.product_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.product_id || apiErrors.product_id }">
          <option value="">Chọn sản phẩm</option>
          <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
        </select>
        <p v-if="validationErrors.product_id" class="mt-1 text-sm text-red-600">{{ validationErrors.product_id }}</p>
        <p v-else-if="apiErrors.product_id" class="mt-1 text-sm text-red-600">{{ apiErrors.product_id }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Loại quy tắc <span class="text-red-500">*</span></label>
        <select v-model="formData.rule_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.rule_type || apiErrors.rule_type }">
          <option value="">Chọn loại</option>
          <option value="min_order">Đơn tối thiểu</option>
          <option value="max_order">Đơn tối đa</option>
          <option value="special">Đặc biệt</option>
        </select>
        <p v-if="validationErrors.rule_type" class="mt-1 text-sm text-red-600">{{ validationErrors.rule_type }}</p>
        <p v-else-if="apiErrors.rule_type" class="mt-1 text-sm text-red-600">{{ apiErrors.rule_type }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Giá trị <span class="text-red-500">*</span></label>
        <input v-model="formData.value" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.value || apiErrors.value }" />
        <p v-if="validationErrors.value" class="mt-1 text-sm text-red-600">{{ validationErrors.value }}</p>
        <p v-else-if="apiErrors.value" class="mt-1 text-sm text-red-600">{{ apiErrors.value }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
        <select v-model="formData.status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.status || apiErrors.status }">
          <option value="active">Hoạt động</option>
          <option value="inactive">Không hoạt động</option>
        </select>
        <p v-if="validationErrors.status" class="mt-1 text-sm text-red-600">{{ validationErrors.status }}</p>
        <p v-else-if="apiErrors.status" class="mt-1 text-sm text-red-600">{{ apiErrors.status }}</p>
      </div>
      <div class="flex justify-end space-x-3 pt-4">
        <button type="button" @click="onCancel" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">Huỷ</button>
        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">{{ mode === 'edit' ? 'Cập nhật' : 'Thêm mới' }}</button>
      </div>
    </form>
  </Modal>
</template>
<script setup>
import { ref, computed, reactive, watch } from 'vue'
import Modal from '@/components/Core/Modal.vue'
const props = defineProps({
  show: Boolean,
  productRule: Object,
  products: { type: Array, default: () => [] },
  apiErrors: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const modalVisible = computed({
  get: () => props.show,
  set: () => onCancel()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Sửa quy tắc sản phẩm' : 'Thêm quy tắc sản phẩm mới')
const formData = reactive({
  product_id: '',
  rule_type: '',
  value: '',
  status: 'active'
})
const validationErrors = reactive({})
watch(() => props.productRule, (val) => {
  if (val) {
    formData.product_id = val.product_id || ''
    formData.rule_type = val.rule_type || ''
    formData.value = val.value || ''
    formData.status = val.status || 'active'
  } else {
    resetForm()
  }
}, { immediate: true })
function resetForm() {
  formData.product_id = ''
  formData.rule_type = ''
  formData.value = ''
  formData.status = 'active'
  clearErrors()
}
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}
const validationRules = computed(() => ({
  product_id: [{ required: 'Sản phẩm là bắt buộc.' }],
  rule_type: [{ required: 'Loại quy tắc là bắt buộc.' }],
  value: [
    { required: 'Giá trị là bắt buộc.' },
    { number: 'Giá trị phải là số.' }
  ]
}))
function validateForm() {
  clearErrors()
  let valid = true
  const rules = validationRules.value
  for (const field in rules) {
    for (const rule of rules[field]) {
      if (rule.required && !formData[field]) {
        validationErrors[field] = rule.required
        valid = false
        break
      }
      if (rule.number && formData[field] && isNaN(Number(formData[field]))) {
        validationErrors[field] = rule.number
        valid = false
        break
      }
    }
  }
  return valid
}
function handleSubmit() {
  if (!validateForm()) return
  emit('submit', { ...formData })
}
function onCancel() { emit('cancel') }
</script> 
