<template>
  <BaseForm 
    :initial-data="warehouse" 
    :default-values="defaultValues"
    :use-form-data="false"
    @submit="$emit('submit', $event)" 
    @cancel="$emit('cancel')"
  >
    <template #default="{ form }">
      <div>
        <label class="block text-sm font-medium mb-1">Tên kho hàng</label>
        <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-xl" maxlength="100" required />
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Địa chỉ</label>
        <input v-model="form.address" type="text" class="w-full px-4 py-2 border rounded-xl" required />
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Thành phố</label>
        <input v-model="form.city" type="text" class="w-full px-4 py-2 border rounded-xl" required />
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Tỉnh/Thành</label>
        <input v-model="form.province" type="text" class="w-full px-4 py-2 border rounded-xl" required />
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Số điện thoại</label>
        <input v-model="form.phone" type="text" class="w-full px-4 py-2 border rounded-xl" />
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input v-model="form.email" type="email" class="w-full px-4 py-2 border rounded-xl" />
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Trạng thái</label>
        <select v-model="form.status" class="w-full px-4 py-2 border rounded-xl">
          <option value="active">Hoạt động</option>
          <option value="inactive">Không hoạt động</option>
        </select>
      </div>
    </template>
  </BaseForm>
</template>

<script setup>
import BaseForm from '@/components/BaseForm.vue'

const props = defineProps({
  warehouse: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])

// Giá trị mặc định cho form
const defaultValues = { 
  name: '', 
  address: '', 
  city: '', 
  province: '', 
  phone: '', 
  email: '', 
  status: 'active' 
}
</script> 