<template>
  <FormLayout @submit="onSubmit" @cancel="$emit('cancel')">
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
      <ImageUploader v-model="form.image" :default-url="imageDefaultUrl" @remove="form.remove_image = true" />
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
const form = ref({ name: '', description: '', status: 'active', image: null, remove_image: false })
const imageDefaultUrl = ref(null)
watch(() => props.brand, (val) => {
  if (val) {
    form.value = { name: val.name || '', description: val.description || '', status: val.status || 'active', image: null, remove_image: false }
    imageDefaultUrl.value = val.image
      ? (val.image.startsWith('http') || val.image.startsWith('/storage/') ? val.image : `/storage/${val.image}`)
      : null
    console.log('Edit brand:', val)
    console.log('imageDefaultUrl:', imageDefaultUrl.value)
  } else {
    form.value = { name: '', description: '', status: 'active', image: null, remove_image: false }
    imageDefaultUrl.value = null
  }
}, { immediate: true })
function onSubmit() {
  const formData = new FormData()
  Object.entries(form.value).forEach(([key, value]) => {
    if (key === 'image' && !value) return
    if (value !== null && value !== undefined && value !== '') {
      formData.append(key, value)
    }
  })
  emit('submit', formData)
}
</script> 