<template>
  <AdminFilter @apply="applyFilters" @reset="resetFilters">
    <AdminFilterItem
      id="search"
      label="Tìm kiếm"
      type="text"
      v-model="filters.search"
      placeholder="Tìm theo tên kho, địa chỉ, thành phố..."
    />
    <AdminFilterItem
      id="status"
      label="Trạng thái"
      type="select"
      v-model="filters.status"
      placeholder="Tất cả trạng thái"
      :options="statusOptions"
    />
    <AdminFilterItem
      id="sort_by"
      label="Sắp xếp theo"
      type="select"
      v-model="filters.sort_by"
      :options="sortOptions"
    />
  </AdminFilter>
</template>
<script setup>
import { reactive, computed } from 'vue'
import AdminFilter from '@/components/Admin/AdminFilter.vue'
import AdminFilterItem from '@/components/Admin/AdminFilterItem.vue'
import { getEnumSync } from '@/constants/enums'

const props = defineProps({
  initialFilters: {
    type: Object,
    default: () => ({})
  }
})
const emit = defineEmits(['update:filters'])
const filters = reactive({
  search: props.initialFilters.search || '',
  status: props.initialFilters.status || '',
  sort_by: props.initialFilters.sort_by || 'created_at_desc',
})

const statusOptions = computed(() => {
  const enumData = getEnumSync('basic_status')
  const options = [{ value: '', label: 'Tất cả trạng thái' }]
  
  if (Array.isArray(enumData)) {
    options.push(...enumData.map(item => ({
      value: item.value,
      label: item.label
    })))
  }
  
  return options
})

const sortOptions = [
  { value: 'created_at_desc', label: 'Mới nhất' },
  { value: 'created_at_asc', label: 'Cũ nhất' },
  { value: 'name_asc', label: 'Tên (A-Z)' },
  { value: 'name_desc', label: 'Tên (Z-A)' }
]
function applyFilters() {
  emit('update:filters', { ...filters })
}
function resetFilters() {
  Object.keys(filters).forEach(key => {
    filters[key] = ''
  })
  filters.sort_by = 'created_at_desc'
  emit('update:filters', { ...filters })
}
</script> 
