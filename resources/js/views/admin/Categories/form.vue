<template>
  <FormLayout @submit="onSubmit" @cancel="$emit('cancel')">
    <template #title>
      <h2 class="text-lg font-semibold mb-4">{{ mode === 'edit' ? 'Chỉnh sửa danh mục' : 'Thêm danh mục mới' }}</h2>
    </template>
    <div>
      <label class="block text-sm font-medium mb-1">Tên danh mục</label>
      <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-xl" maxlength="100" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Mô tả</label>
      <textarea v-model="form.description" class="w-full px-4 py-2 border rounded-xl"></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Danh mục cha</label>
      <input v-model="form.parent_name" type="text" class="w-full px-4 py-2 border rounded-xl" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Trạng thái</label>
      <select v-model="form.status" class="w-full px-4 py-2 border rounded-xl">
        <option value="active">Hoạt động</option>
        <option value="inactive">Không hoạt động</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Ảnh (URL)</label>
      <input v-model="form.image" type="text" class="w-full px-4 py-2 border rounded-xl" />
    </div>
  </FormLayout>
</template>
<script setup>
import { ref, watch } from 'vue'
import FormLayout from '@/components/FormLayout.vue'
const props = defineProps({
  category: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const form = ref({ name: '', description: '', parent_name: '', status: 'active', image: '' })
watch(() => props.category, (val) => {
  if (val) {
    form.value = { name: val.name || '', description: val.description || '', parent_name: val.parent_name || '', status: val.status || 'active', image: val.image || '' }
  } else {
    form.value = { name: '', description: '', parent_name: '', status: 'active', image: '' }
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