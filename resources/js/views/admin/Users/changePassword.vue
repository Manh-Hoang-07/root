<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="onClose">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Đổi mật khẩu cho {{ user?.name || user?.email }}</h3>
      </div>
      <div class="p-6">
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Mật khẩu mới</label>
            <input v-model="password" type="password" required class="w-full px-4 py-2 border rounded-xl" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Xác nhận mật khẩu</label>
            <input v-model="confirmPassword" type="password" required class="w-full px-4 py-2 border rounded-xl" />
          </div>
          <div class="flex justify-end gap-2 pt-4">
            <button type="button" @click="onClose" class="px-4 py-2 bg-gray-100 rounded-xl hover:bg-gray-200">Hủy</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">Đổi mật khẩu</button>
          </div>
        </form>
        <div v-if="error" class="text-red-500 mt-2">{{ error }}</div>
        <div v-if="success" class="text-green-600 mt-2">Đổi mật khẩu thành công!</div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import axios from 'axios'
import endpoints from '@/api/endpoints'
const props = defineProps({
  show: Boolean,
  user: Object,
  onClose: Function
})
const emit = defineEmits(['changed'])
const password = ref('')
const confirmPassword = ref('')
const error = ref('')
const success = ref(false)
async function handleSubmit() {
  error.value = ''
  success.value = false
  if (password.value !== confirmPassword.value) {
    error.value = 'Mật khẩu xác nhận không khớp.'
    return
  }
  try {
    await axios.post(endpoints.users.changePassword(props.user.id), { password: password.value })
    success.value = true
    emit('changed')
    setTimeout(() => props.onClose(), 1000)
  } catch (e) {
    error.value = 'Đổi mật khẩu thất bại!'
  }
}
</script> 