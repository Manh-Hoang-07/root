<template>
  <FormWrapper
    :initial-data="user"
    :default-values="defaultValues"
    :rules="validationRules"
    :api-errors="apiErrors"
    :show-debug="showDebug"
    submit-text="Lưu thông tin"
    @submit="handleSubmit"
    @cancel="$emit('cancel')"
    @error="handleError"
    ref="formWrapper"
  >
    <template #default="{ form, errors, clearError }">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Thông tin đăng nhập -->
        <div class="md:col-span-2 font-semibold text-base mb-2">Thông tin đăng nhập</div>
        
        <FormField
          v-model="form.username"
          label="Tên đăng nhập"
          name="username"
          type="text"
          :maxlength="50"
          :error="errors.username"
          required
          @update:model-value="clearError('username')"
        />
        
        <FormField
          v-model="form.email"
          label="Email"
          name="email"
          type="email"
          :error="errors.email"
          required
          @update:model-value="clearError('email')"
        />
        
        <FormField
          v-model="form.phone"
          label="Số điện thoại"
          name="phone"
          type="tel"
          :maxlength="20"
          :error="errors.phone"
          @update:model-value="clearError('phone')"
        />
        
        <FormField
          v-if="mode === 'create'"
          v-model="form.password"
          label="Mật khẩu"
          name="password"
          type="password"
          :error="errors.password"
          required
          @update:model-value="clearError('password')"
        />
        
        <FormField
          v-if="mode === 'create'"
          v-model="form.password_confirmation"
          label="Xác nhận mật khẩu"
          name="password_confirmation"
          type="password"
          :error="errors.password_confirmation"
          required
          @update:model-value="clearError('password_confirmation')"
        />
        
        <FormField
          v-else
          v-model="form.password"
          label="Mật khẩu (để trống nếu không đổi)"
          name="password"
          type="password"
          :error="errors.password"
          @update:model-value="clearError('password')"
        />
        
        <FormField
          v-model="form.status"
          label="Trạng thái"
          name="status"
          type="select"
          :options="statusOptions"
          placeholder="Chọn trạng thái"
          :error="errors.status"
          @update:model-value="clearError('status')"
        />
        
        <!-- Thông tin xác thực -->
        <div class="md:col-span-2 font-semibold text-base mt-6 mb-2">Thông tin xác thực</div>
        
        <FormField
          v-model="form.email_verified_at"
          label="Email xác thực lúc"
          name="email_verified_at"
          type="datetime-local"
          :error="errors.email_verified_at"
          @update:model-value="clearError('email_verified_at')"
        />
        
        <FormField
          v-model="form.phone_verified_at"
          label="SĐT xác thực lúc"
          name="phone_verified_at"
          type="datetime-local"
          :error="errors.phone_verified_at"
          @update:model-value="clearError('phone_verified_at')"
        />
        
        <FormField
          v-model="form.last_login_at"
          label="Đăng nhập cuối"
          name="last_login_at"
          type="datetime-local"
          :error="errors.last_login_at"
          @update:model-value="clearError('last_login_at')"
        />
        
        <!-- Thông tin cá nhân -->
        <div class="md:col-span-2 font-semibold text-base mt-6 mb-2">Thông tin cá nhân</div>
        
        <FormField
          v-model="form.name"
          label="Họ tên"
          name="name"
          type="text"
          :error="errors.name"
          @update:model-value="clearError('name')"
        />
        
        <FormField
          v-model="form.gender"
          label="Giới tính"
          name="gender"
          type="select"
          :options="genderOptions"
          placeholder="Chọn giới tính"
          :error="errors.gender"
          @update:model-value="clearError('gender')"
        />
        
        <FormField
          v-model="form.birthday"
          label="Ngày sinh"
          name="birthday"
          type="date"
          :error="errors.birthday"
          @update:model-value="clearError('birthday')"
        />
        
        <FormField
          v-model="form.address"
          label="Địa chỉ"
          name="address"
          type="text"
          :error="errors.address"
          @update:model-value="clearError('address')"
        />
        
        <div class="md:col-span-2">
          <label class="block text-sm font-medium mb-1">Ảnh đại diện</label>
          <ImageUploader v-model="form.avatar" :default-url="avatarDefaultUrl" @remove="form.remove_avatar = true" />
          <div v-if="errors.avatar" class="text-red-500 text-sm mt-1">{{ errors.avatar }}</div>
        </div>
        
        <FormField
          v-model="form.about"
          label="Giới thiệu"
          name="about"
          type="textarea"
          :error="errors.about"
          class="md:col-span-2"
          @update:model-value="clearError('about')"
        />
      </div>
    </template>
  </FormWrapper>
</template>

<script setup>
import FormWrapper from '@/components/FormWrapper.vue'
import FormField from '@/components/FormField.vue'
import ImageUploader from '@/components/ImageUploader.vue'
import { computed, ref } from 'vue'

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
  }
})

const emit = defineEmits(['submit', 'cancel'])

const formWrapper = ref(null)
const showDebug = ref(false) // Set to true to show debug panel

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

// Format options cho select
const statusOptions = computed(() => {
  if (!Array.isArray(props.statusEnums)) return [];
  return props.statusEnums.map(item => ({
    value: item.value,
    label: item.name
  })) || []
})

const genderOptions = computed(() => {
  if (!Array.isArray(props.genderEnums)) return [];
  return props.genderEnums.map(item => ({
    value: item.value,
    label: item.name
  })) || []
})

// Validation rules
const validationRules = computed(() => ({
  username: [{ required: 'Tên đăng nhập là bắt buộc.' }],
  email: [
    { required: 'Email là bắt buộc.' },
    { email: 'Email không hợp lệ.' }
  ],
  password: props.mode === 'create' ? [
    { required: 'Mật khẩu là bắt buộc.' },
    { min: [6, 'Mật khẩu tối thiểu 6 ký tự.'] }
  ] : [],
  password_confirmation: props.mode === 'create' ? [
    { required: 'Vui lòng xác nhận mật khẩu.' },
    { match: ['password', 'Mật khẩu xác nhận không khớp.'] }
  ] : []
}))

// Phương thức xử lý submit
function handleSubmit(formData) {
  emit('submit', formData)
}

// Phương thức xử lý lỗi validation
function handleError(errors) {
  console.log('Form validation errors:', errors)
}

// Phương thức để set lỗi từ server
function setServerErrors(apiErrors) {
  if (formWrapper.value) {
    formWrapper.value.setServerErrors(apiErrors)
  }
}

// Expose các phương thức cần thiết
defineExpose({
  setServerErrors
})
</script> 