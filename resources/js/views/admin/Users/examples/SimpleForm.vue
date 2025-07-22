<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Form đơn giản</h2>
    
    <FormWrapper
      :default-values="defaultValues"
      :rules="validationRules"
      submit-text="Lưu thông tin"
      @submit="handleSubmit"
      @cancel="handleCancel"
    >
      <template #default="{ form, errors, clearError }">
        <div class="space-y-4">
          <FormField
            v-model="form.name"
            label="Họ tên"
            name="name"
            required
            :error="errors.name"
            @update:model-value="clearError('name')"
          />
          
          <FormField
            v-model="form.email"
            label="Email"
            name="email"
            type="email"
            required
            :error="errors.email"
            @update:model-value="clearError('email')"
          />
          
          <FormField
            v-model="form.phone"
            label="Số điện thoại"
            name="phone"
            type="tel"
            :error="errors.phone"
            @update:model-value="clearError('phone')"
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
            v-model="form.message"
            label="Ghi chú"
            name="message"
            type="textarea"
            :error="errors.message"
            @update:model-value="clearError('message')"
          />
          
          <FormField
            v-model="form.subscribe"
            label="Đăng ký nhận tin"
            name="subscribe"
            type="checkbox"
            :error="errors.subscribe"
            @update:model-value="clearError('subscribe')"
          />
        </div>
      </template>
    </FormWrapper>
  </div>
</template>

<script setup>
import FormWrapper from '@/components/FormWrapper.vue'
import FormField from '@/components/FormField.vue'

// Giá trị mặc định cho form
const defaultValues = {
  name: '',
  email: '',
  phone: '',
  gender: '',
  message: '',
  subscribe: false
}

// Validation rules
const validationRules = {
  name: [{ required: 'Họ tên là bắt buộc.' }],
  email: [
    { required: 'Email là bắt buộc.' },
    { email: 'Email không hợp lệ.' }
  ]
}

// Options cho select
const genderOptions = [
  { value: 'male', label: 'Nam' },
  { value: 'female', label: 'Nữ' },
  { value: 'other', label: 'Khác' }
]

// Xử lý submit
function handleSubmit(formData) {
  console.log('Form submitted:', formData)
  // Gọi API hoặc xử lý dữ liệu ở đây
}

// Xử lý cancel
function handleCancel() {
  console.log('Form cancelled')
}
</script> 