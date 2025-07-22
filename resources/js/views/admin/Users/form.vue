<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Tên người dùng -->
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
        <input
          id="username"
          v-model="formData.username"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': apiErrors.username }"
        />
        <p v-if="apiErrors.username" class="mt-1 text-sm text-red-600">{{ apiErrors.username }}</p>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
          id="email"
          v-model="formData.email"
          type="email"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': apiErrors.email }"
        />
        <p v-if="apiErrors.email" class="mt-1 text-sm text-red-600">{{ apiErrors.email }}</p>
      </div>

      <!-- Mật khẩu -->
      <div v-if="mode === 'create'">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
        <input
          id="password"
          v-model="formData.password"
          type="password"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': apiErrors.password }"
        />
        <p v-if="apiErrors.password" class="mt-1 text-sm text-red-600">{{ apiErrors.password }}</p>
      </div>

      <!-- Xác nhận mật khẩu -->
      <div v-if="mode === 'create'">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu</label>
        <input
          id="password_confirmation"
          v-model="formData.password_confirmation"
          type="password"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': apiErrors.password_confirmation }"
        />
        <p v-if="apiErrors.password_confirmation" class="mt-1 text-sm text-red-600">{{ apiErrors.password_confirmation }}</p>
      </div>

      <!-- Số điện thoại -->
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
        <input
          id="phone"
          v-model="formData.phone"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': apiErrors.phone }"
        />
        <p v-if="apiErrors.phone" class="mt-1 text-sm text-red-600">{{ apiErrors.phone }}</p>
      </div>

      <!-- Giới tính -->
      <div>
        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
        <select
          id="gender"
          v-model="formData.gender"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': apiErrors.gender }"
        >
          <option value="">Chọn giới tính</option>
          <option v-for="gender in genderEnums" :key="gender.value" :value="gender.value">
            {{ gender.name }}
          </option>
        </select>
        <p v-if="apiErrors.gender" class="mt-1 text-sm text-red-600">{{ apiErrors.gender }}</p>
      </div>

      <!-- Trạng thái -->
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
        <select
          id="status"
          v-model="formData.status"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': apiErrors.status }"
        >
          <option value="">Chọn trạng thái</option>
          <option v-for="status in statusEnums" :key="status.value" :value="status.value">
            {{ status.name }}
          </option>
        </select>
        <p v-if="apiErrors.status" class="mt-1 text-sm text-red-600">{{ apiErrors.status }}</p>
      </div>

      <!-- Địa chỉ -->
      <div>
        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
        <textarea
          id="address"
          v-model="formData.address"
          rows="2"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': apiErrors.address }"
        ></textarea>
        <p v-if="apiErrors.address" class="mt-1 text-sm text-red-600">{{ apiErrors.address }}</p>
      </div>

      <!-- Avatar -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh đại diện</label>
        <div class="flex items-start space-x-4">
          <div v-if="avatarPreview || (user && user.avatar)" class="w-24 h-24 border rounded-md overflow-hidden">
            <img :src="avatarPreview || (user && user.avatar)" alt="Avatar preview" class="w-full h-full object-cover" />
          </div>
          <div class="flex-1">
            <input
              type="file"
              @change="handleAvatarChange"
              accept="image/*"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              :class="{ 'border-red-500': apiErrors.avatar }"
            />
            <p v-if="apiErrors.avatar" class="mt-1 text-sm text-red-600">{{ apiErrors.avatar }}</p>
            <p class="mt-1 text-sm text-gray-500">PNG, JPG hoặc GIF (tối đa 2MB)</p>
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="flex justify-end space-x-3 pt-4">
        <button
          type="button"
          @click="onCancel"
          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
        >
          Hủy
        </button>
        <button
          type="submit"
          class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none"
        >
          {{ mode === 'create' ? 'Tạo người dùng' : 'Cập nhật' }}
        </button>
      </div>
    </form>
  </Modal>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue'
import Modal from '@/components/Modal.vue'

const props = defineProps({
  user: Object,
  mode: {
    type: String,
    default: 'create',
    validator: (value) => ['create', 'edit'].includes(value)
  },
  statusEnums: {
    type: Array,
    default: () => []
  },
  genderEnums: {
    type: Array,
    default: () => []
  },
  apiErrors: {
    type: Object,
    default: () => ({})
  },
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit', 'cancel'])

// Form title
const formTitle = computed(() => props.mode === 'create' ? 'Thêm người dùng mới' : 'Chỉnh sửa người dùng')

// Modal visibility
const modalVisible = computed({
  get: () => props.show,
  set: () => onCancel()
})

// Form data
const formData = reactive({
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
  status: '',
  gender: '',
  phone: '',
  address: '',
  avatar: null,
  remove_avatar: false
})

// Avatar preview
const avatarPreview = ref(null)

// Watch user prop to update form data
watch(() => props.user, (newUser) => {
  if (newUser) {
    formData.username = newUser.username || ''
    formData.email = newUser.email || ''
    formData.status = newUser.status || ''
    formData.gender = newUser.gender || ''
    formData.phone = newUser.phone || ''
    formData.address = newUser.address || ''
    formData.remove_avatar = false
    avatarPreview.value = null
  } else {
    resetForm()
  }
}, { immediate: true })

// Reset form
function resetForm() {
  formData.username = ''
  formData.email = ''
  formData.password = ''
  formData.password_confirmation = ''
  formData.status = ''
  formData.gender = ''
  formData.phone = ''
  formData.address = ''
  formData.avatar = null
  formData.remove_avatar = false
  avatarPreview.value = null
}

// Handle avatar change
function handleAvatarChange(event) {
  const file = event.target.files[0]
  if (!file) return

  formData.avatar = file
  formData.remove_avatar = false

  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    avatarPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

// Handle form submit
function handleSubmit() {
  // Create FormData object for file upload
  const submitData = new FormData()
  submitData.append('username', formData.username)
  submitData.append('email', formData.email)
  
  if (props.mode === 'create') {
    submitData.append('password', formData.password)
    submitData.append('password_confirmation', formData.password_confirmation)
  }
  
  if (formData.status !== '') {
    submitData.append('status', formData.status)
  }
  
  if (formData.gender !== '') {
    submitData.append('gender', formData.gender)
  }
  
  submitData.append('phone', formData.phone)
  submitData.append('address', formData.address)
  
  if (formData.avatar) {
    submitData.append('avatar', formData.avatar)
  }
  
  if (formData.remove_avatar) {
    submitData.append('remove_avatar', 1)
  }

  emit('submit', submitData)
}

// Close modal
function onCancel() {
  emit('cancel')
}
</script> 