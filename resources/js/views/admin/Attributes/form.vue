<template>
  <BaseForm 
    :initial-data="attribute" 
    :default-values="defaultValues"
    :use-form-data="false"
    @submit="$emit('submit', $event)" 
    @cancel="$emit('cancel')"
  >
    <template #default="{ form }">
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
    </template>
  </BaseForm>
</template>

<script setup>
import BaseForm from '@/components/BaseForm.vue'

const props = defineProps({
  attribute: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])

// Giá trị mặc định cho form
const defaultValues = { 
  name: '', 
  type: '', 
  status: 'active', 
  description: '' 
}
</script> 