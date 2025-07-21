<template>
  <Modal v-if="show" v-model="modalVisible" title="Thêm người dùng mới">
    <Form :user="null" :mode="'create'" :status-enums="statusEnums" :gender-enums="genderEnums" @submit="handleSubmit" @cancel="onClose" />
  </Modal>
</template>
<script setup>
import Form from './form.vue'
import endpoints from '@/api/endpoints'
import Modal from '@/components/Modal.vue'
import { computed } from 'vue'
const props = defineProps({
  show: Boolean,
  statusEnums: Array,
  genderEnums: Array,
  onClose: Function
})
const emit = defineEmits(['created'])
const modalVisible = computed({
  get: () => props.show,
  set: (val) => { if (!val) props.onClose() }
})
async function handleSubmit(formData) {
  try {
    await axios.post(endpoints.users.create, formData)
    emit('created')
    props.onClose()
  } catch (e) {
    // handle error (nếu muốn show lỗi từ server thì có thể truyền xuống form qua props)
  }
}
</script> 