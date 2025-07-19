<template>
  <Modal v-if="show" v-model="modalVisible" title="Chỉnh sửa danh mục">
    <Form :category="category" :mode="'edit'" @submit="handleSubmit" @cancel="onClose" />
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
  category: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])
const modalVisible = computed({
  get: () => props.show,
  set: (val) => { if (!val) props.onClose() }
})
async function handleSubmit(data) {
  try {
    if (data instanceof FormData) {
      await api.put(endpoints.categories.update(props.category.id), data)
    } else {
      await api.put(endpoints.categories.update(props.category.id), data)
    }
    emit('updated')
    props.onClose()
  } catch (e) {
    // handle error
  }
}
</script> 