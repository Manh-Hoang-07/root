<template>
  <AdminFilter @apply="applyFilters" @reset="resetFilters">
    <!-- Tìm kiếm theo tên -->
    <AdminFilterItem
      id="search"
      label="Tìm kiếm"
      type="text"
      v-model="filters.search"
      placeholder="Tìm theo tên đăng nhập, email"
    />
    
    <!-- Lọc theo trạng thái -->
    <AdminFilterItem
      id="status"
      label="Trạng thái"
      type="select"
      v-model="filters.status"
      placeholder="Tất cả trạng thái"
      :options="statusOptions"
    />
    
    <!-- Sắp xếp theo -->
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
import AdminFilter from '@/components/AdminFilter.vue'
import AdminFilterItem from '@/components/AdminFilterItem.vue'

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

// Các tùy chọn cho select
const statusOptions = [
  { value: 'active', label: 'Hoạt động' },
  { value: 'inactive', label: 'Không hoạt động' },
  { value: 'banned', label: 'Đã khóa' }
]

const sortOptions = [
  { value: 'created_at_desc', label: 'Mới nhất' },
  { value: 'created_at_asc', label: 'Cũ nhất' },
  { value: 'username_asc', label: 'Tên đăng nhập (A-Z)' },
  { value: 'username_desc', label: 'Tên đăng nhập (Z-A)' },
  { value: 'email_asc', label: 'Email (A-Z)' },
  { value: 'email_desc', label: 'Email (Z-A)' }
]

// Áp dụng bộ lọc
function applyFilters() {
  emit('update:filters', { ...filters })
}

// Đặt lại bộ lọc
function resetFilters() {
  Object.keys(filters).forEach(key => {
    filters[key] = ''
  })
  filters.sort_by = 'created_at_desc'
  emit('update:filters', { ...filters })
}
</script> 