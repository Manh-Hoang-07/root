<template>
  <BaseForm 
    :initial-data="user" 
    :default-values="defaultValues"
    :use-form-data="true"
    @submit="$emit('submit', $event)" 
    @cancel="$emit('cancel')"
  >
    <template #default="{ form }">
      <!-- Các trường nhập liệu -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        <div class="md:col-span-2 font-semibold text-base mt-6 mb-2">Thông tin xác thực</div>
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
        <div class="md:col-span-2 font-semibold text-base mt-6 mb-2">Thông tin cá nhân</div>
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
        <div class="md:col-span-2">
          <label class="block text-sm font-medium mb-1">Ảnh đại diện</label>
          <ImageUploader v-model="form.avatar" :default-url="avatarDefaultUrl" @remove="form.remove_avatar = true" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium mb-1">Giới thiệu</label>
          <textarea v-model="form.about" class="w-full px-4 py-2 border rounded-xl"></textarea>
        </div>
      </div>
    </template>
  </BaseForm>
</template>

<script setup>
import BaseForm from '@/components/BaseForm.vue'
import ImageUploader from '@/components/ImageUploader.vue'
import { computed } from 'vue'

const props = defineProps({
  user: Object,
  mode: String,
  statusEnums: Array,
  genderEnums: Array
})
const emit = defineEmits(['submit', 'cancel'])

// Giá trị mặc định cho form
const defaultValues = {
  username: '', 
  email: '', 
  password: '', 
  status: '', 
  gender: '', 
  phone: '', 
  address: '', 
  avatar: null,
  email_verified_at: null, 
  phone_verified_at: null, 
  last_login_at: null, 
  birthday: null, 
  about: '',
  remove_avatar: false
}

// Tính toán avatarDefaultUrl từ user data
const avatarDefaultUrl = computed(() => {
  if (props.user?.avatar) {
    return props.user.avatar.startsWith('http') ? props.user.avatar : `/storage/${props.user.avatar}`
  } else if (props.user?.profile?.avatar) {
    return props.user.profile.avatar.startsWith('http') ? props.user.profile.avatar : `/storage/${props.user.profile.avatar}`
  }
  return null
})
</script> 