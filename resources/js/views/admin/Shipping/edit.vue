<template>
  <Modal v-if="show" v-model="modalVisible" title="Chỉnh sửa khu vực vận chuyển">
    <Form :shipping-zone="shippingZone" :mode="'edit'" @submit="handleSubmit" @cancel="onClose" />
  </Modal>
</template>
<script setup>
import Form from './form.vue'
import api from '@/api/apiClient'
import endpoints from '@/api/endpoints'
import Modal from '@/components/Core/Modal.vue'
import { computed } from 'vue'
const props = defineProps({
  show: Boolean,
  shippingZone: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])
const modalVisible = computed({
  get: () => props.show,
  set: (val) => { if (!val) props.onClose() }
})
async function handleSubmit(formData) {
  try {
    await api.put(endpoints.shipping.update(props.shippingZone.id), formData)
    emit('updated')
    props.onClose()
  } catch (e) {
    // handle error
  }
}
</script> 
