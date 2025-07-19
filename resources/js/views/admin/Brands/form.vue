<template>
  <FormLayout @submit="onSubmit" @cancel="$emit('cancel')">
    <template #title>
      <h2 class="text-lg font-semibold mb-4">{{ mode === 'edit' ? 'Chỉnh sửa thương hiệu' : 'Thêm thương hiệu mới' }}</h2>
    </template>
    <div>
      <label class="block text-sm font-medium mb-1">Tên thương hiệu</label>
      <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-xl" maxlength="100" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Mô tả</label>
      <textarea v-model="form.description" class="w-full px-4 py-2 border rounded-xl"></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Trạng thái</label>
      <select v-model="form.status" class="w-full px-4 py-2 border rounded-xl">
        <option value="active">Hoạt động</option>
        <option value="inactive">Ngừng hoạt động</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Logo</label>
      <ImageUploader v-model="form.logo" :default-url="logoDefaultUrl" @remove="form.remove_logo = true" />
    </div>
  </FormLayout>
</template>
<script setup>
import { ref, watch } from 'vue'
import FormLayout from '@/components/FormLayout.vue'
import ImageUploader from '@/components/ImageUploader.vue'
const props = defineProps({
  brand: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const form = ref({ name: '', description: '', status: 'active', logo: null, remove_logo: false })
const logoDefaultUrl = ref(null)
watch(() => props.brand, (val) => {
  if (val) {
    form.value = { name: val.name || '', description: val.description || '', status: val.status || 'active', logo: null, remove_logo: false }
    if (val.logo) logoDefaultUrl.value = val.logo.startsWith('http') ? val.logo : `/storage/${val.logo}`
    else logoDefaultUrl.value = null
  } else {
    form.value = { name: '', description: '', status: 'active', logo: null, remove_logo: false }
    logoDefaultUrl.value = null
  }
}, { immediate: true })
function onSubmit() {
  const formData = new FormData()
  Object.entries(form.value).forEach(([key, value]) => {
    if (key === 'logo' && !value) return
    if (value !== null && value !== undefined && value !== '') {
      formData.append(key, value)
    }
  })
  emit('submit', formData)
}
</script> 