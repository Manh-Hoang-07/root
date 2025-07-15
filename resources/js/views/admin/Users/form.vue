<template>
  <form @submit.prevent="onSubmit" class="space-y-4">
    <div>
      <label class="block text-sm font-medium mb-1">Họ tên</label>
      <input v-model="form.name" required type="text" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Email</label>
      <input v-model="form.email" required type="email" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div v-if="mode === 'create'">
      <label class="block text-sm font-medium mb-1">Mật khẩu</label>
      <input v-model="form.password" required type="password" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Trạng thái</label>
      <select v-model="form.status" required class="w-full px-4 py-2 border rounded-xl">
        <option value="">Chọn trạng thái</option>
        <option v-for="s in statusEnums" :key="s.value" :value="s.value">{{ s.name }}</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Giới tính</label>
      <select v-model="form.gender" class="w-full px-4 py-2 border rounded-xl">
        <option value="">Chọn giới tính</option>
        <option v-for="g in genderEnums" :key="g.value" :value="g.value">{{ g.name }}</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Số điện thoại</label>
      <input v-model="form.phone" type="text" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Địa chỉ</label>
      <input v-model="form.address" type="text" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Ảnh đại diện</label>
      <input type="file" @change="onAvatarChange" accept="image/*" />
    </div>
    <div class="flex justify-end gap-2 pt-4">
      <button type="button" @click="$emit('cancel')" class="px-4 py-2 bg-gray-100 rounded-xl hover:bg-gray-200">Hủy</button>
      <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
        {{ mode === 'edit' ? 'Cập nhật' : 'Thêm' }}
      </button>
    </div>
  </form>
</template>
<script setup>
import { ref, watch } from 'vue'
const props = defineProps({
  user: Object,
  mode: String,
  statusEnums: Array,
  genderEnums: Array
})
const emit = defineEmits(['submit', 'cancel'])
const form = ref({
  name: '', email: '', password: '', status: '', gender: '', phone: '', address: '', avatar: null
})
watch(() => props.user, (val) => {
  if (val) {
    form.value = {
      name: val.name || '',
      email: val.email || '',
      password: '',
      status: val.status || '',
      gender: val.gender || '',
      phone: val.phone || '',
      address: val.address || '',
      avatar: null
    }
  } else {
    form.value = { name: '', email: '', password: '', status: '', gender: '', phone: '', address: '', avatar: null }
  }
}, { immediate: true })
function onAvatarChange(e) {
  const file = e.target.files[0]
  if (file) form.value.avatar = file
}
function onSubmit() {
  const formData = new FormData()
  Object.entries(form.value).forEach(([key, value]) => {
    if (key === 'avatar' && !value) return
    formData.append(key, value)
  })
  emit('submit', formData)
}
</script> 