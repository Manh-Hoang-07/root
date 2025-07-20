<template>
  <BaseForm 
    :initial-data="brand" 
    :default-values="defaultValues"
    :use-form-data="true"
    @submit="$emit('submit', $event)" 
    @cancel="$emit('cancel')"
  >
    <template #default="{ form }">
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
    </template>
  </BaseForm>
</template>

<script setup>
import BaseForm from '@/components/BaseForm.vue'
import ImageUploader from '@/components/ImageUploader.vue'
import { computed } from 'vue'

const props = defineProps({
  brand: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])

// Giá trị mặc định cho form
const defaultValues = { 
  name: '', 
  description: '', 
  status: 'active', 
  image: null, 
  remove_image: false 
}

// Tính toán imageDefaultUrl từ brand data
const imageDefaultUrl = computed(() => {
  if (props.brand?.image) {
    return props.brand.image.startsWith('http') || props.brand.image.startsWith('/storage/') 
      ? props.brand.image 
      : `/storage/${props.brand.image}`
  }
  return null
})
</script> 