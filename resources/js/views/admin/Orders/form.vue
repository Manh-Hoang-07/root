<template>
  <FormLayout @submit="onSubmit" @cancel="$emit('cancel')">
    <div>
      <label class="block text-sm font-medium mb-1">Khách hàng</label>
      <input v-model="form.customer_name" type="text" class="w-full px-4 py-2 border rounded-xl" maxlength="100" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Tổng tiền</label>
      <input v-model="form.total_amount" type="number" class="w-full px-4 py-2 border rounded-xl" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Trạng thái</label>
      <select v-model="form.status" class="w-full px-4 py-2 border rounded-xl">
        <option value="pending">Chờ xử lý</option>
        <option value="completed">Hoàn thành</option>
        <option value="cancelled">Đã hủy</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Mô tả</label>
      <textarea v-model="form.description" class="w-full px-4 py-2 border rounded-xl"></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Ngày đặt</label>
      <input v-model="form.created_at" type="date" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Ngày cập nhật</label>
      <input v-model="form.updated_at" type="date" class="w-full px-4 py-2 border rounded-xl" />
    </div>
  </FormLayout>
</template>
<script setup>
import { ref, watch } from 'vue'
import FormLayout from '@/components/FormLayout.vue'
const props = defineProps({
  order: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const form = ref({ customer_name: '', total_amount: '', status: 'pending', description: '', created_at: '', updated_at: '' })
watch(() => props.order, (val) => {
  if (val) {
    form.value = { customer_name: val.customer_name || '', total_amount: val.total_amount || '', status: val.status || 'pending', description: val.description || '', created_at: val.created_at || '', updated_at: val.updated_at || '' }
  } else {
    form.value = { customer_name: '', total_amount: '', status: 'pending', description: '', created_at: '', updated_at: '' }
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