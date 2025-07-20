<template>
  <Modal v-if="show" v-model="modalVisible" title="Chỉnh sửa giá trị thuộc tính">
    <Form :attributeValue="attributeValue" :mode="'edit'" @submit="handleSubmit" @cancel="onClose" />
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
  attributeValue: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])
const modalVisible = computed({
  get: () => props.show,
  set: (val) => { if (!val) props.onClose() }
})
async function handleSubmit(data) {
  console.log('Frontend: handleSubmit called', {
    id: props.attributeValue.id,
    endpoint: endpoints.attributeValues.update(props.attributeValue.id),
    data: data
  })
  
  try {
    const response = await api.put(endpoints.attributeValues.update(props.attributeValue.id), data)
    console.log('Frontend: Update successful', response)
    console.log('Frontend: Response data', response.data)
    console.log('Frontend: Response data.data', response.data.data)
    emit('updated')
    props.onClose()
  } catch (e) {
    console.error('Frontend: Update failed', e)
    // handle error
  }
}
</script> 