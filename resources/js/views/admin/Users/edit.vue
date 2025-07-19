<template>
  <Modal v-if="show" v-model="modalVisible" title="Chỉnh sửa người dùng">
    <Form :user="user" :mode="'edit'" :status-enums="statusEnums" :gender-enums="genderEnums" @submit="handleSubmit" @cancel="onClose" />
  </Modal>
</template>
<script setup>
import Form from './form.vue'
import endpoints from '@/api/endpoints'
import Modal from '@/components/Modal.vue'
import { computed } from 'vue'
const props = defineProps({
  show: Boolean,
  user: Object,
  statusEnums: Array,
  genderEnums: Array,
  onClose: Function
})
const emit = defineEmits(['updated'])
const modalVisible = computed({
  get: () => props.show,
  set: (val) => { if (!val) props.onClose() }
})
async function handleSubmit(formData) {
  try {
    await axios.post(endpoints.users.update(props.user.id), formData)
    emit('updated')
    props.onClose()
  } catch (e) {
    // handle error
  }
}
</script> 