<template>
  <Modal v-if="show" v-model="modalVisible" title="Thêm danh mục mới">
    <Form :category="null" :mode="'create'" @submit="handleSubmit" @cancel="onClose" />
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
async function handleSubmit(formData) {
  try {
    await api.post(endpoints.categories.create, formData)
    emit('created')
    props.onClose()
  } catch (e) {
    // handle error
  }
}
</script> 