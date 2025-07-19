<template>
  <Modal v-if="show" v-model="modalVisible" title="Chỉnh sửa thuộc tính">
    <Form :attribute="attribute" :mode="'edit'" @submit="handleSubmit" @cancel="onClose" />
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
  attribute: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])
const modalVisible = computed({
  get: () => props.show,
  set: (val) => { if (!val) props.onClose() }
})
async function handleSubmit(formData) {
  try {
    await api.post(endpoints.attributes.update(props.attribute.id), formData)
    emit('updated')
    props.onClose()
  } catch (e) {
    // handle error
  }
}
</script> 