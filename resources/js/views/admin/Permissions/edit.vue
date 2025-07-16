<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Chỉnh sửa quyền</h3>
      </div>
      <form @submit.prevent="submit" class="p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Tên quyền</label>
          <input v-model="form.name" required class="w-full px-3 py-2 border rounded-lg" placeholder="Tên quyền" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Tên quyền hiển thị</label>
          <input v-model="form.display_name" required class="w-full px-3 py-2 border rounded-lg" placeholder="Tên quyền hiển thị" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Guard</label>
          <input v-model="form.guard_name" class="w-full px-3 py-2 border rounded-lg" placeholder="Guard (mặc định: web)" />
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Hủy</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
import { ref, watch, toRefs } from 'vue'
import axios from 'axios'
const emit = defineEmits(['close', 'saved'])
const props = defineProps({ permission: Object })
const form = ref({ name: '', display_name: '', guard_name: '' })
watch(() => props.permission, (val) => {
  if (val) form.value = { name: val.name, display_name: val.display_name, guard_name: val.guard_name }
}, { immediate: true })
const submit = async () => {
  try {
    await axios.put(`/api/admin/permissions/${props.permission.id}`, form.value)
    emit('saved')
    emit('close')
  } catch (e) {
    alert('Cập nhật quyền thất bại!')
  }
}
</script> 