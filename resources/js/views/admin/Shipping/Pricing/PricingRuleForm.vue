<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Dịch vụ <span class="text-red-500">*</span></label>
        <select v-model="formData.service_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.service_id || apiErrors.service_id }">
          <option value="">Chọn dịch vụ</option>
          <option v-for="service in services" :key="service.id" :value="service.id">{{ service.name }}</option>
        </select>
        <p v-if="validationErrors.service_id" class="mt-1 text-sm text-red-600">{{ validationErrors.service_id }}</p>
        <p v-else-if="apiErrors.service_id" class="mt-1 text-sm text-red-600">{{ apiErrors.service_id }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Zone <span class="text-red-500">*</span></label>
        <select v-model="formData.zone_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.zone_id || apiErrors.zone_id }">
          <option value="">Chọn zone</option>
          <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
        </select>
        <p v-if="validationErrors.zone_id" class="mt-1 text-sm text-red-600">{{ validationErrors.zone_id }}</p>
        <p v-else-if="apiErrors.zone_id" class="mt-1 text-sm text-red-600">{{ apiErrors.zone_id }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Khối lượng tối thiểu (kg)</label>
        <input v-model="formData.min_weight" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.min_weight || apiErrors.min_weight }" />
        <p v-if="validationErrors.min_weight" class="mt-1 text-sm text-red-600">{{ validationErrors.min_weight }}</p>
        <p v-else-if="apiErrors.min_weight" class="mt-1 text-sm text-red-600">{{ apiErrors.min_weight }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Khối lượng tối đa (kg)</label>
        <input v-model="formData.max_weight" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.max_weight || apiErrors.max_weight }" />
        <p v-if="validationErrors.max_weight" class="mt-1 text-sm text-red-600">{{ validationErrors.max_weight }}</p>
        <p v-else-if="apiErrors.max_weight" class="mt-1 text-sm text-red-600">{{ apiErrors.max_weight }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tăng giá theo %</label>
        <input v-model="formData.markup_percent" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.markup_percent || apiErrors.markup_percent }" />
        <p v-if="validationErrors.markup_percent" class="mt-1 text-sm text-red-600">{{ validationErrors.markup_percent }}</p>
        <p v-else-if="apiErrors.markup_percent" class="mt-1 text-sm text-red-600">{{ apiErrors.markup_percent }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tăng giá cố định (VNĐ)</label>
        <input v-model="formData.markup_fixed" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.markup_fixed || apiErrors.markup_fixed }" />
        <p v-if="validationErrors.markup_fixed" class="mt-1 text-sm text-red-600">{{ validationErrors.markup_fixed }}</p>
        <p v-else-if="apiErrors.markup_fixed" class="mt-1 text-sm text-red-600">{{ apiErrors.markup_fixed }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Phí tối thiểu (VNĐ)</label>
        <input v-model="formData.min_fee" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.min_fee || apiErrors.min_fee }" />
        <p v-if="validationErrors.min_fee" class="mt-1 text-sm text-red-600">{{ validationErrors.min_fee }}</p>
        <p v-else-if="apiErrors.min_fee" class="mt-1 text-sm text-red-600">{{ apiErrors.min_fee }}</p>
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
import Modal from '@/components/Modal.vue'
const props = defineProps({
  show: Boolean,
  pricingRule: Object,
  services: { type: Array, default: () => [] },
  zones: { type: Array, default: () => [] },
  apiErrors: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const modalVisible = computed({
  get: () => props.show,
  set: () => onCancel()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Sửa quy tắc giá' : 'Thêm quy tắc giá mới')
const formData = reactive({
  service_id: '',
  zone_id: '',
  min_weight: '',
  max_weight: '',
  markup_percent: '',
  markup_fixed: '',
  min_fee: '',
  status: 'active'
})
const validationErrors = reactive({})
watch(() => props.pricingRule, (val) => {
  if (val) {
    formData.service_id = val.service_id || ''
    formData.zone_id = val.zone_id || ''
    formData.min_weight = val.min_weight || ''
    formData.max_weight = val.max_weight || ''
    formData.markup_percent = val.markup_percent || ''
    formData.markup_fixed = val.markup_fixed || ''
    formData.min_fee = val.min_fee || ''
    formData.status = val.status || 'active'
  } else {
    resetForm()
  }
}, { immediate: true })
function resetForm() {
  formData.service_id = ''
  formData.zone_id = ''
  formData.min_weight = ''
  formData.max_weight = ''
  formData.markup_percent = ''
  formData.markup_fixed = ''
  formData.min_fee = ''
  formData.status = 'active'
  clearErrors()
}
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}
const validationRules = computed(() => ({
  service_id: [{ required: 'Dịch vụ là bắt buộc.' }],
  zone_id: [{ required: 'Zone là bắt buộc.' }],
  min_weight: [{ number: 'Khối lượng tối thiểu phải là số.' }],
  max_weight: [{ number: 'Khối lượng tối đa phải là số.' }],
  markup_percent: [{ number: 'Tăng giá % phải là số.' }],
  markup_fixed: [{ number: 'Tăng giá cố định phải là số.' }],
  min_fee: [{ number: 'Phí tối thiểu phải là số.' }]
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