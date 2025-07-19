<template>
  <FormLayout @submit="onSubmit" @cancel="$emit('cancel')">
    <template #title>
      <h2 class="text-lg font-semibold mb-4">{{ mode === 'edit' ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới' }}</h2>
    </template>
    <div>
      <label class="block text-sm font-medium mb-1">Tên sản phẩm</label>
      <input v-model="form.name" type="text" class="w-full px-4 py-2 border rounded-xl" maxlength="100" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">SKU</label>
      <input v-model="form.sku" type="text" class="w-full px-4 py-2 border rounded-xl" maxlength="50" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Danh mục</label>
      <input v-model="form.category_name" type="text" class="w-full px-4 py-2 border rounded-xl" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Thương hiệu</label>
      <input v-model="form.brand_name" type="text" class="w-full px-4 py-2 border rounded-xl" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Giá</label>
      <input v-model="form.price" type="number" class="w-full px-4 py-2 border rounded-xl" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Tồn kho</label>
      <input v-model="form.stock" type="number" class="w-full px-4 py-2 border rounded-xl" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Trạng thái</label>
      <select v-model="form.status" class="w-full px-4 py-2 border rounded-xl">
        <option value="active">Đang bán</option>
        <option value="inactive">Ngừng bán</option>
        <option value="draft">Bản nháp</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Mô tả</label>
      <textarea v-model="form.description" class="w-full px-4 py-2 border rounded-xl"></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Ảnh sản phẩm (URL)</label>
      <input v-model="form.image" type="text" class="w-full px-4 py-2 border rounded-xl" />
    </div>
  </FormLayout>
</template>
<script setup>
import { ref, watch } from 'vue'
import FormLayout from '@/components/FormLayout.vue'
const props = defineProps({
  product: Object,
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const form = ref({ name: '', sku: '', category_name: '', brand_name: '', price: '', stock: '', status: 'active', description: '', image: '' })
watch(() => props.product, (val) => {
  if (val) {
    form.value = { name: val.name || '', sku: val.sku || '', category_name: val.category_name || '', brand_name: val.brand_name || '', price: val.price || '', stock: val.stock || '', status: val.status || 'active', description: val.description || '', image: val.image || '' }
  } else {
    form.value = { name: '', sku: '', category_name: '', brand_name: '', price: '', stock: '', status: 'active', description: '', image: '' }
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