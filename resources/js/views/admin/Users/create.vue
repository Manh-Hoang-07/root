<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Thêm người dùng mới</h3>
      </div>
      <div class="p-6 max-h-[80vh] overflow-y-auto">
        <Form :user="null" :mode="'create'" :status-enums="statusEnums" :gender-enums="genderEnums" @submit="handleSubmit" @cancel="onClose" />
      </div>
    </div>
  </div>
</template>
<script setup>
import Form from './form.vue'
import endpoints from '@/api/endpoints'
const props = defineProps({
  show: Boolean,
  statusEnums: Array,
  genderEnums: Array,
  onClose: Function
})
const emit = defineEmits(['created'])
async function handleSubmit(formData) {
  try {
    await axios.post(endpoints.users.create, formData)
    emit('created')
    props.onClose()
  } catch (e) {
    // handle error
  }
}
</script> 