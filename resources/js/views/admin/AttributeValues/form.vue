<template>
  <FormLayout @submit="onSubmit" @cancel="$emit('cancel')">
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
      <label class="block text-sm font-medium mb-1">Hiển thị</label>
      <input v-model="form.display_value" type="text" class="w-full px-4 py-2 border rounded-xl" />
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
  </FormLayout>
</template>
<script setup>
import { ref, watch } from 'vue'
import FormLayout from '@/components/FormLayout.vue'
import useApiOptions from '@/composables/useApiOptions'
import endpoints from '@/api/endpoints'
const props = defineProps({
  attributeValue: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const form = ref({ attribute_id: '', value: '', display_value: '', sort_order: '', status: 'active', description: '' })
const { options: attributeOptions, loading: loadingAttributes } = useApiOptions(endpoints.attributes.list)
watch(() => props.attributeValue, (val) => {
  if (val) {
    form.value = { attribute_id: val.attribute_id || '', value: val.value || '', display_value: val.display_value || '', sort_order: val.sort_order || '', status: val.status || 'active', description: val.description || '' }
  } else {
    form.value = { attribute_id: '', value: '', display_value: '', sort_order: '', status: 'active', description: '' }
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