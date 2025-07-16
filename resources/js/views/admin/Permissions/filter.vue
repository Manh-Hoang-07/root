<template>
  <div class="mb-4 flex items-center space-x-2">
    <input v-model="localFilters.search" @keyup.enter="emitUpdate" type="text" placeholder="Tìm kiếm quyền..." class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
    <button @click="emitUpdate" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Lọc</button>
    <button @click="emitClear" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Xóa lọc</button>
  </div>
</template>
<script setup>
import { ref, watch, toRefs } from 'vue'
const emit = defineEmits(['update:filters', 'clear'])
const props = defineProps({ filters: Object })
const localFilters = ref({ search: '' })
watch(() => props.filters, (val) => {
  localFilters.value = { ...val }
}, { immediate: true })
const emitUpdate = () => {
  emit('update:filters', { ...localFilters.value })
}
const emitClear = () => {
  localFilters.value = { search: '' }
  emit('clear')
}
</script> 