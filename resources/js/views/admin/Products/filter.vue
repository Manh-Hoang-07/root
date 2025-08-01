<template>
  <AdminFilter @apply="applyFilters" @reset="resetFilters">
    <AdminFilterItem
      id="search"
      label="Tìm kiếm"
      type="text"
      v-model="filters.search"
      placeholder="Tìm theo tên sản phẩm, mô tả, SKU"
      @input="debouncedSearch"
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
      id="brand_id"
      label="Thương hiệu"
      type="select"
      v-model="filters.brand_id"
      placeholder="Tất cả thương hiệu"
      :options="brandOptions"
    />
    <AdminFilterItem
      id="category_id"
      label="Danh mục"
      type="select"
      v-model="filters.category_id"
      placeholder="Tất cả danh mục"
      :options="categoryOptions"
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
import { reactive, ref, onMounted } from 'vue'
import AdminFilter from '@/components/Admin/AdminFilter.vue'
import AdminFilterItem from '@/components/Admin/AdminFilterItem.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'
import { debounce } from '@/utils/debounce'

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
  brand_id: props.initialFilters.brand_id || '',
  category_id: props.initialFilters.category_id || '',
  sort_by: props.initialFilters.sort_by || 'created_at_desc',
})

// Debounced search function
const debouncedSearch = debounce(() => {
  emit('update:filters', { ...filters })
}, 300)

const statusOptions = [
  { value: 'active', label: 'Đang bán' },
  { value: 'inactive', label: 'Ngừng bán' }
]

const brandOptions = ref([])
const categoryOptions = ref([])

const sortOptions = [
  { value: 'created_at_desc', label: 'Mới nhất' },
  { value: 'created_at_asc', label: 'Cũ nhất' },
  { value: 'name_asc', label: 'Tên (A-Z)' },
  { value: 'name_desc', label: 'Tên (Z-A)' },
  { value: 'price_asc', label: 'Giá tăng dần' },
  { value: 'price_desc', label: 'Giá giảm dần' }
]

onMounted(async () => {
  await Promise.all([
    fetchBrands(),
    fetchCategories()
  ])
})

async function fetchBrands() {
  try {
    const response = await axios.get(endpoints.brands.list)
    brandOptions.value = [
      { value: '', label: 'Tất cả thương hiệu' },
      ...(response.data.data || []).map(brand => ({
        value: brand.id,
        label: brand.name
      }))
    ]
  } catch (error) {
    console.error('Error fetching brands:', error)
    brandOptions.value = [{ value: '', label: 'Tất cả thương hiệu' }]
  }
}

async function fetchCategories() {
  try {
    const response = await axios.get(endpoints.categories.list)
    categoryOptions.value = [
      { value: '', label: 'Tất cả danh mục' },
      ...(response.data.data || []).map(category => ({
        value: category.id,
        label: category.name
      }))
    ]
  } catch (error) {
    console.error('Error fetching categories:', error)
    categoryOptions.value = [{ value: '', label: 'Tất cả danh mục' }]
  }
}

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
