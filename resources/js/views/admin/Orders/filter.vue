<template>
  <AdminFilter @apply="applyFilters" @reset="resetFilters">
    <AdminFilterItem
      id="search"
      label="Tìm kiếm"
      type="text"
      v-model="filters.search"
      placeholder="Tìm theo tên khách hàng"
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
import { reactive } from 'vue'
import AdminFilter from '@/components/Admin/AdminFilter.vue'
import AdminFilterItem from '@/components/Admin/AdminFilterItem.vue'

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
const statusOptions = [
  { value: 'pending', label: 'Chờ xử lý' },
  { value: 'completed', label: 'Hoàn thành' },
  { value: 'cancelled', label: 'Đã huỷ' }
]
const sortOptions = [
  { value: 'created_at_desc', label: 'Mới nhất' },
  { value: 'created_at_asc', label: 'Cũ nhất' },
  { value: 'customer_name_asc', label: 'Khách hàng (A-Z)' },
  { value: 'customer_name_desc', label: 'Khách hàng (Z-A)' }
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
