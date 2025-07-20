<template>
  <BaseForm 
    :initial-data="category" 
    :default-values="defaultValues"
    :use-form-data="true"
    @submit="$emit('submit', $event)" 
    @cancel="$emit('cancel')"
  >
    <template #default="{ form }">
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
    </template>
  </BaseForm>
</template>

<script setup>
import BaseForm from '@/components/BaseForm.vue'
import useApiOptions from '@/composables/useApiOptions'
import ImageUploader from '@/components/ImageUploader.vue'

const props = defineProps({
  category: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])

// Giá trị mặc định cho form
const defaultValues = { 
  name: '', 
  description: '', 
  parent_id: null, 
  status: 'active', 
  image: null 
}

const { options: parentOptions } = useApiOptions('/api/admin/categories')
</script> 