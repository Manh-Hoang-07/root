<template>
  <Modal v-if="show" v-model="modalVisible" title="Chỉnh sửa người dùng">
    <UserForm
      ref="userForm"
      mode="edit"
      :user="user"
      :status-enums="statusEnums"
      :gender-enums="genderEnums"
      :api-errors="apiErrors"
      @submit="handleSubmit"
      @cancel="onClose"
    />
  </Modal>
</template>

<script setup>
import UserForm from './UserForm.vue'
import endpoints from '@/api/endpoints'
import Modal from '@/components/Modal.vue'
import { computed, ref } from 'vue'
import { useApiFormSubmit } from '@/utils/useApiFormSubmit'

const props = defineProps({
  show: Boolean,
  user: Object,
  statusEnums: {
    type: Array,
    default: () => []
  },
  genderEnums: {
    type: Array,
    default: () => []
  },
  onClose: Function
})

const emit = defineEmits(['updated'])

const modalVisible = computed({
  get: () => props.show,
  set: (val) => { if (!val) props.onClose() }
})

const userForm = ref(null)

const { apiErrors, submit } = useApiFormSubmit({
  endpoint: endpoints.users.update(props.user.id),
  emit,
  onClose: props.onClose,
  eventName: 'updated',
  method: 'put'
})

async function handleSubmit(formData) {
  await submit(formData)
}
</script> 