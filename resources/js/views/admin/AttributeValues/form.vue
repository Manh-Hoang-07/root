<template>
  <BaseForm 
    :initial-data="attributeValue" 
    :default-values="defaultValues"
    :use-form-data="false"
    @submit="$emit('submit', $event)" 
    @cancel="$emit('cancel')"
  >
    <template #default="{ form }">
      <div>
        <label class="block text-sm font-medium mb-1">Thuộc tính</label>
        <select v-model="form.attribute_id" class="w-full px-4 py-2 border rounded-xl">
          <option v-for="opt in attributeOptions" :key="opt.value" :value="opt.value">{{ opt.name }}</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Giá trị</label>
        <input v-model="form.value" type="text" class="w-full px-4 py-2 border rounded-xl" required />
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Tên hiển thị</label>
        <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-xl" />
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Thứ tự</label>
        <input v-model="form.sort_order" type="number" class="w-full px-4 py-2 border rounded-xl" />
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
import useApiOptions from '@/composables/useApiOptions'
import endpoints from '@/api/endpoints'

const props = defineProps({
  attributeValue: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])

// Giá trị mặc định cho form
const defaultValues = { 
  attribute_id: '', 
  value: '', 
  name: '', 
  sort_order: '', 
  status: 'active', 
  description: '' 
}

const { options: attributeOptions, loading: loadingAttributes } = useApiOptions(endpoints.attributes.list)
</script> 