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
import { computed, reactive, ref } from 'vue'

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
const apiErrors = reactive({})

async function handleSubmit(formData) {
  try {
    // Xóa lỗi cũ
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    
    console.log('Submitting form data:', formData)
    const response = await axios.post(endpoints.users.update(props.user.id), formData)
    console.log('API response success:', response)
    emit('updated')
    props.onClose()
  } catch (error) {
    console.log('API error:', error.response)
    
    // Xử lý lỗi validation
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors
      console.log('API validation errors:', errors)
      
      // Cập nhật apiErrors reactive object
      for (const field in errors) {
        if (Array.isArray(errors[field])) {
          apiErrors[field] = errors[field][0]
        } else {
          apiErrors[field] = errors[field]
        }
      }
      
      console.log('API errors set:', apiErrors)
    }
  }
}
</script> 