<template>
  <FormLayout @submit="onSubmit" @cancel="$emit('cancel')">
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
      <select v-model="form.parent_id" class="w-full px-4 py-2 border rounded-xl">
        <option :value="null">-- Không có --</option>
        <option v-for="opt in parentOptions" :key="opt.value" :value="opt.value">{{ opt.name }}</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Trạng thái</label>
      <select v-model="form.status" class="w-full px-4 py-2 border rounded-xl">
        <option value="active">Hoạt động</option>
        <option value="inactive">Không hoạt động</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Ảnh</label>
      <ImageUploader v-model="form.image" />
    </div>
  </FormLayout>
</template>
<script setup>
import { ref, watch } from 'vue'
import FormLayout from '@/components/FormLayout.vue'
import useApiOptions from '@/composables/useApiOptions'
import ImageUploader from '@/components/ImageUploader.vue'
const props = defineProps({
  category: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const form = ref({ name: '', description: '', parent_id: null, status: 'active', image: '' })
const imageDefaultUrl = ref(null)
const { options: parentOptions } = useApiOptions('/api/admin/categories')
watch(() => props.category, (val) => {
  if (val) {
    form.value = { name: val.name || '', description: val.description || '', parent_id: val.parent_id || null, status: val.status || 'active', image: null }
    imageDefaultUrl.value = val.image ? (val.image.startsWith('http') ? val.image : `/storage/${val.image}`) : null
  } else {
    form.value = { name: '', description: '', parent_id: null, status: 'active', image: null }
    imageDefaultUrl.value = null
  }
}, { immediate: true })
function onSubmit() {
  console.log('Form submit:', form.value)
  if (typeof form.value.image === 'string') {
    emit('submit', { ...form.value })
  } else {
    const formData = new FormData()
    Object.entries(form.value).forEach(([key, value]) => {
      formData.append(key, value ?? '')
    })
    emit('submit', formData)
  }
}
</script> 