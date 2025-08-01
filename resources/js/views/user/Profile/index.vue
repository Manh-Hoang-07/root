<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl p-8">
      <div class="flex items-center space-x-6 mb-8">
        <img :src="user.profile?.avatar || defaultAvatar" alt="Avatar" class="w-20 h-20 rounded-full object-cover border-4 border-indigo-200" />
        <div>
          <div class="text-2xl font-bold text-gray-800 mb-1">{{ user.name }}</div>
          <div class="text-gray-500 text-sm mb-1">{{ user.email }}</div>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(user.status)">
            {{ getStatusLabel(user.status) }}
          </span>
        </div>
        <button v-if="!editMode" @click="editMode = true" class="ml-auto px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-200">Chỉnh sửa</button>
      </div>
      <form v-if="editMode" @submit.prevent="saveProfile" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="text-gray-600 text-sm mb-1 block">Họ tên</label>
            <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" required />
          </div>
          <div>
            <label class="text-gray-600 text-sm mb-1 block">Giới tính</label>
            <select v-model="form.gender" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
              <option v-for="g in genderEnums" :key="g.value" :value="g.value">{{ g.label }}</option>
            </select>
          </div>
          <div>
            <label class="text-gray-600 text-sm mb-1 block">Số điện thoại</label>
            <input v-model="form.phone" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" />
          </div>
          <div>
            <label class="text-gray-600 text-sm mb-1 block">Địa chỉ</label>
            <input v-model="form.address" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" />
          </div>
        </div>
        <div>
          <label class="text-gray-600 text-sm mb-1 block">Ảnh đại diện</label>
          <input type="file" @change="onAvatarChange" accept="image/*" />
        </div>
        <div class="flex space-x-3">
          <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Lưu</button>
          <button type="button" @click="cancelEdit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Hủy</button>
        </div>
      </form>
      <div v-else>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <div class="text-gray-600 text-sm mb-1">Giới tính</div>
            <div class="font-medium">{{ getGenderLabel(user.profile?.gender) }}</div>
          </div>
          <div>
            <div class="text-gray-600 text-sm mb-1">Số điện thoại</div>
            <div class="font-medium">{{ user.profile?.phone || '—' }}</div>
          </div>
          <div>
            <div class="text-gray-600 text-sm mb-1">Địa chỉ</div>
            <div class="font-medium">{{ user.profile?.address || '—' }}</div>
          </div>
          <div>
            <div class="text-gray-600 text-sm mb-1">Ngày tham gia</div>
            <div class="font-medium">{{ formatDate(user.created_at) }}</div>
          </div>
        </div>
        <div class="mt-8">
          <div class="text-gray-600 text-sm mb-2">Tài khoản liên kết</div>
          <div class="flex space-x-4">
            <div v-for="sa in user.social_accounts" :key="sa.id" class="flex items-center space-x-2 bg-gray-50 px-3 py-1 rounded-lg">
              <span class="font-semibold">{{ sa.provider }}</span>
              <span class="text-xs text-gray-400">({{ sa.provider_id }})</span>
            </div>
            <div v-if="!user.social_accounts || user.social_accounts.length === 0" class="text-gray-400">Không có</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { getEnumSync } from '@/constants/enums'

const user = ref({ profile: {}, social_accounts: [] })
const defaultAvatar = '/default-avatar.png'
const statusEnums = ref([]) // [{ value, label }]
const genderEnums = ref([]) // [{ value, label }]
const editMode = ref(false)
const form = ref({ name: '', gender: '', phone: '', address: '', avatar: null })

const fetchUser = async () => {
  try {
    const res = await axios.get('/api/user')
    user.value = res.data
    form.value.name = res.data.name
    form.value.gender = res.data.profile?.gender || ''
    form.value.phone = res.data.profile?.phone || ''
    form.value.address = res.data.profile?.address || ''
    form.value.avatar = null
  } catch (e) {}
}
const fetchEnums = () => {
  // Sử dụng static enum thay vì gọi API
  statusEnums.value = getEnumSync('user_status')
  genderEnums.value = getEnumSync('gender')
}
onMounted(async () => {
  // Load enums immediately (static)
  fetchEnums()
  
  // Fetch user data
  await fetchUser()
})
const getStatusLabel = (status) => {
  const found = statusEnums.value.find(s => s.value === status)
  return found ? found.label : status
}
const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-gray-100 text-gray-800',
    suspended: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
const getGenderLabel = (gender) => {
  const found = genderEnums.value.find(g => g.value === gender)
  return found ? found.label : gender
}
const formatDate = (dateString) => {
  if (!dateString) return '—'
  return new Intl.DateTimeFormat('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(dateString))
}
const onAvatarChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.avatar = file
  }
}
const saveProfile = async () => {
  const formData = new FormData()
  formData.append('name', form.value.name)
  formData.append('gender', form.value.gender)
  formData.append('phone', form.value.phone)
  formData.append('address', form.value.address)
  if (form.value.avatar) {
    formData.append('avatar', form.value.avatar)
  }
  try {
    await axios.post('/api/user/profile', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
    await fetchUser()
    editMode.value = false
  } catch (e) {}
}
const cancelEdit = () => {
  editMode.value = false
  fetchUser()
}
</script> 
