<template>
  <FormLayout @submit="onSubmit" @cancel="$emit('cancel')">
    <template #title>
      <h2 class="text-lg font-semibold mb-4">{{ mode === 'edit' ? 'Chỉnh sửa kho hàng' : 'Thêm kho hàng mới' }}</h2>
    </template>
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
  </FormLayout>
</template>
<script setup>
import { ref, watch } from 'vue'
import FormLayout from '@/components/FormLayout.vue'
const props = defineProps({
  warehouse: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const form = ref({ name: '', address: '', city: '', province: '', phone: '', email: '', status: 'active' })
watch(() => props.warehouse, (val) => {
  if (val) {
    form.value = { name: val.name || '', address: val.address || '', city: val.city || '', province: val.province || '', phone: val.phone || '', email: val.email || '', status: val.status || 'active' }
  } else {
    form.value = { name: '', address: '', city: '', province: '', phone: '', email: '', status: 'active' }
  }
}, { immediate: true })
function onSubmit() {
  const formData = new FormData()
  Object.entries(form.value).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') {
      formData.append(key, value)
    }
  })
  emit('submit', formData)
}
</script> 