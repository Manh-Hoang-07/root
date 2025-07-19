<template>
  <FormLayout @submit="onSubmit" @cancel="$emit('cancel')">
    <div>
      <label class="block text-sm font-medium mb-1">Tên thuộc tính</label>
      <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-xl" maxlength="100" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Kiểu</label>
      <input v-model="form.type" type="text" class="w-full px-4 py-2 border rounded-xl" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Trạng thái</label>
      <select v-model="form.status" class="w-full px-4 py-2 border rounded-xl">
        <option value="active">Hoạt động</option>
        <option value="inactive">Không hoạt động</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Mô tả</label>
      <textarea v-model="form.description" class="w-full px-4 py-2 border rounded-xl"></textarea>
    </div>
  </FormLayout>
</template>
<script setup>
import { ref, watch } from 'vue'
import FormLayout from '@/components/FormLayout.vue'
const props = defineProps({
  attribute: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const form = ref({ name: '', type: '', status: 'active', description: '' })
watch(() => props.attribute, (val) => {
  if (val) {
    form.value = { name: val.name || '', type: val.type || '', status: val.status || 'active', description: val.description || '' }
  } else {
    form.value = { name: '', type: '', status: 'active', description: '' }
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