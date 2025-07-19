<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Chỉnh sửa kho hàng</h3>
      </div>
      <div class="p-6 max-h-[80vh] overflow-y-auto">
        <Form :warehouse="warehouse" :mode="'edit'" @submit="handleSubmit" @cancel="onClose" />
      </div>
    </div>
  </div>
</template>
<script setup>
import Form from './form.vue'
import api from '@/api/apiClient'
import endpoints from '@/api/endpoints'
const props = defineProps({
  show: Boolean,
  warehouse: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])
async function handleSubmit(formData) {
  try {
    await api.post(endpoints.warehouses.update(props.warehouse.id), formData)
    emit('updated')
    props.onClose()
  } catch (e) {
    // handle error
  }
}
</script> 