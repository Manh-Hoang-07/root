<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <FormWrapper
      :default-values="defaultValues"
      :rules="validationRules"
      :api-errors="apiErrors"
      :submit-text="user ? 'Cập nhật' : 'Thêm mới'"
      @submit="handleSubmit"
      @cancel="onClose"
    >
      <template #default="{ form, errors, clearError, isSubmitting }">
        <!-- Tên đăng nhập -->
        <FormField
          v-model="form.username"
          label="Tên đăng nhập"
          name="username"
          :error="errors.username"
          required
          autocomplete="username"
          @update:model-value="clearError('username')"
        />
        
        <!-- Email -->
        <FormField
          v-model="form.email"
          label="Email"
          name="email"
          type="email"
          :error="errors.email"
          required
          autocomplete="email"
          @update:model-value="clearError('email')"
        />
        
        <!-- Số điện thoại -->
        <FormField
          v-model="form.phone"
          label="Số điện thoại"
          name="phone"
          type="tel"
          :error="errors.phone"
          autocomplete="tel"
          @update:model-value="clearError('phone')"
        />
        
        <!-- Mật khẩu -->
        <FormField
          v-if="!user"
          v-model="form.password"
          label="Mật khẩu"
          name="password"
          type="password"
          :error="errors.password"
          required
          autocomplete="new-password"
          @update:model-value="clearError('password')"
        />
        
        <FormField
          v-if="!user"
          v-model="form.password_confirmation"
          label="Xác nhận mật khẩu"
          name="password_confirmation"
          type="password"
          :error="errors.password_confirmation"
          required
          autocomplete="new-password"
          @update:model-value="clearError('password_confirmation')"
        />
        
        <!-- Họ tên -->
        <FormField
          v-model="form.name"
          label="Họ tên"
          name="name"
          :error="errors.name"
          autocomplete="name"
          @update:model-value="clearError('name')"
        />
        
        <!-- Giới tính -->
        <FormField
          v-model="form.gender"
          label="Giới tính"
          name="gender"
          type="select"
          :options="genderOptions"
          :error="errors.gender"
          @update:model-value="clearError('gender')"
        />
        
        <!-- Ngày sinh -->
        <FormField
          v-model="form.birthday"
          label="Ngày sinh"
          name="birthday"
          type="date"
          :error="errors.birthday"
          @update:model-value="clearError('birthday')"
        />
        
        <!-- Địa chỉ -->
        <FormField
          v-model="form.address"
          label="Địa chỉ"
          name="address"
          :error="errors.address"
          autocomplete="street-address"
          @update:model-value="clearError('address')"
        />
        
        <!-- Ảnh đại diện -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1" for="user-image">Ảnh đại diện</label>
          <ImageUploader
            v-model="form.image"
            :default-url="imageUrl"
            @remove="form.remove_image = true"
          />
        </div>
        
        <!-- Giới thiệu -->
        <FormField
          v-model="form.about"
          label="Giới thiệu"
          name="about"
          type="textarea"
          :error="errors.about"
          autocomplete="off"
          @update:model-value="clearError('about')"
        />
        
        <!-- Trạng thái -->
        <FormField
          v-model="form.status"
          label="Trạng thái"
          name="status"
          type="select"
          :options="statusOptions"
          :error="errors.status"
          @update:model-value="clearError('status')"
        />
      </template>
    </FormWrapper>
  </Modal>
</template>

<script setup>
import { computed } from 'vue'
import Modal from '@/components/Core/Modal.vue'
import FormWrapper from '@/components/Core/FormWrapper.vue'
import FormField from '@/components/Core/FormField.vue'
import ImageUploader from '@/components/Core/ImageUploader.vue'
import { useFormDefaults } from '@/utils/useFormDefaults'
import { useUrl } from '@/utils/useUrl'

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
  apiErrors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['submit', 'cancel'])

const formTitle = computed(() => props.user ? 'Chỉnh sửa người dùng' : 'Thêm người dùng mới')
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})

const defaultValues = useFormDefaults(props, 'user', {
  username: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  name: '',
  gender: '',
  birthday: '',
  address: '',
  image: null,
  about: '',
  status: '',
  remove_image: false
})

const imageUrl = useUrl(props, 'user', 'image')

const validationRules = computed(() => ({
  username: [
    { required: 'Tên đăng nhập là bắt buộc.' },
    { max: [50, 'Tên đăng nhập không được vượt quá 50 ký tự.'] }
  ],
  email: [
    { required: 'Email là bắt buộc.' },
    { email: 'Email không hợp lệ.' }
  ],
  phone: [
    { max: [20, 'Số điện thoại không được vượt quá 20 ký tự.'] }
  ],
  password: props.user ? [] : [
    { required: 'Mật khẩu là bắt buộc.' },
    { min: [8, 'Mật khẩu phải có ít nhất 8 ký tự.'] }
  ],
  password_confirmation: props.user ? [] : [
    { required: 'Vui lòng xác nhận mật khẩu.' }
  ],
  name: [
    { max: [255, 'Họ tên không được vượt quá 255 ký tự.'] }
  ],
  address: [
    { max: [255, 'Địa chỉ không được vượt quá 255 ký tự.'] }
  ],
  about: [
    { max: [500, 'Giới thiệu không được vượt quá 500 ký tự.'] }
  ]
}))

const statusOptions = computed(() =>
  (props.statusEnums || []).map(opt => ({
    value: opt.id,
    label: opt.name
  }))
)

const genderOptions = computed(() =>
  (props.genderEnums || []).map(opt => ({
    value: opt.id,
    label: opt.name
  }))
)

function handleSubmit(form) {
  emit('submit', form)
}

function onClose() {
  emit('cancel')
}
</script> 
