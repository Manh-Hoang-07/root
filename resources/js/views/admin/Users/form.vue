<template>
  <FormLayout @submit="onSubmit" @cancel="$emit('cancel')">
    <template #title>
      <h2 class="text-lg font-semibold mb-4">{{ mode === 'edit' ? 'Chỉnh sửa tài khoản' : 'Thêm tài khoản mới' }}</h2>
    </template>
    <!-- Các trường nhập liệu -->
    <div>
      <label class="block text-sm font-medium mb-1">Tên đăng nhập</label>
      <input v-model="form.username" type="text" class="w-full px-4 py-2 border rounded-xl" maxlength="50" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Email</label>
      <input v-model="form.email" type="email" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Số điện thoại</label>
      <input v-model="form.phone" type="text" class="w-full px-4 py-2 border rounded-xl" maxlength="20" />
    </div>
    <div v-if="mode === 'create'">
      <label class="block text-sm font-medium mb-1">Mật khẩu</label>
      <input v-model="form.password" required type="password" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div v-else>
      <label class="block text-sm font-medium mb-1">Mật khẩu (để trống nếu không đổi)</label>
      <input v-model="form.password" type="password" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Trạng thái</label>
      <select v-model="form.status" class="w-full px-4 py-2 border rounded-xl">
        <option value="">Chọn trạng thái</option>
        <option v-for="s in statusEnums" :key="s.value" :value="s.value">{{ s.name }}</option>
      </select>
    </div>
    <div class="font-semibold text-base mt-6 mb-2">Thông tin xác thực</div>
    <div>
      <label class="block text-sm font-medium mb-1">Email xác thực lúc</label>
      <input v-model="form.email_verified_at" type="datetime-local" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">SĐT xác thực lúc</label>
      <input v-model="form.phone_verified_at" type="datetime-local" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Đăng nhập cuối</label>
      <input v-model="form.last_login_at" type="datetime-local" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div class="font-semibold text-base mt-6 mb-2">Thông tin cá nhân</div>
    <div>
      <label class="block text-sm font-medium mb-1">Họ tên</label>
      <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Giới tính</label>
      <select v-model="form.gender" class="w-full px-4 py-2 border rounded-xl">
        <option value="">Chọn giới tính</option>
        <option v-for="g in genderEnums" :key="g.value" :value="g.value">{{ g.name }}</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Ngày sinh</label>
      <input v-model="form.birthday" type="date" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Địa chỉ</label>
      <input v-model="form.address" type="text" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Ảnh đại diện</label>
      <ImageUploader v-model="form.avatar" :default-url="avatarDefaultUrl" @remove="form.remove_avatar = true" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Giới thiệu</label>
      <textarea v-model="form.about" class="w-full px-4 py-2 border rounded-xl"></textarea>
    </div>
  </FormLayout>
</template>
<script setup>
import { ref, watch } from 'vue'
import FormLayout from '@/components/FormLayout.vue'
import ImageUploader from '@/components/ImageUploader.vue'
const props = defineProps({
  user: Object,
  mode: String,
  statusEnums: Array,
  genderEnums: Array
})
const emit = defineEmits(['submit', 'cancel'])
const form = ref({
  username: '', email: '', password: '', status: '', gender: '', phone: '', address: '', avatar: null,
  email_verified_at: null, phone_verified_at: null, last_login_at: null, birthday: null, about: '',
  remove_avatar: false
})
const avatarDefaultUrl = ref(null)
watch(() => props.user, (val) => {
  if (val) {
    form.value = {
      username: val.username || '',
      email: val.email || '',
      password: '',
      status: val.status || '',
      gender: val.gender || '',
      phone: val.phone || '',
      address: val.address || '',
      avatar: null,
      email_verified_at: val.email_verified_at || null,
      phone_verified_at: val.phone_verified_at || null,
      last_login_at: val.last_login_at || null,
      birthday: val.birthday || null,
      about: val.about || '',
      remove_avatar: false
    }
    // Nếu có avatar cũ, hiển thị preview
    if (val.avatar) {
      avatarDefaultUrl.value = val.avatar.startsWith('http') ? val.avatar : `/storage/${val.avatar}`
    } else if (val.profile && val.profile.avatar) {
      avatarDefaultUrl.value = val.profile.avatar.startsWith('http') ? val.profile.avatar : `/storage/${val.profile.avatar}`
    } else {
      avatarDefaultUrl.value = null
    }
  } else {
    form.value = {
      username: '', email: '', password: '', status: '', gender: '', phone: '', address: '', avatar: null,
      email_verified_at: null, phone_verified_at: null, last_login_at: null, birthday: null, about: '',
      remove_avatar: false
    }
    avatarDefaultUrl.value = null
  }
}, { immediate: true })
function onSubmit() {
  const formData = new FormData()
  Object.entries(form.value).forEach(([key, value]) => {
    if (key === 'avatar' && !value) return
    if (value !== null && value !== undefined && value !== '') {
      formData.append(key, value)
    }
  })
  emit('submit', formData)
}
</script> 