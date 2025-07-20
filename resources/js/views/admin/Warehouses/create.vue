<template>
  <Modal v-if="show" v-model="modalVisible" title="Thêm kho hàng mới">
    <Form :warehouse="null" :mode="'create'" @submit="handleSubmit" @cancel="onClose" />
  </Modal>
</template>
<script setup>
import Form from './form.vue'
import api from '@/api/apiClient'
import endpoints from '@/api/endpoints'
import Modal from '@/components/Modal.vue'
import { computed } from 'vue'
const props = defineProps({
  show: Boolean,
  onClose: Function
})
const emit = defineEmits(['created'])
const modalVisible = computed({
  get: () => props.show,
  set: (val) => { if (!val) props.onClose() }
})
async function handleSubmit(data) {
  console.log('Warehouse create: handleSubmit called', {
    endpoint: endpoints.warehouses.create,
    data: data
  })
  
  try {
    const response = await api.post(endpoints.warehouses.create, data)
    console.log('Warehouse create: Success', response)
    emit('created')
    props.onClose()
  } catch (e) {
    console.error('Warehouse create: Error', e)
    // handle error
  }
}
</script> 