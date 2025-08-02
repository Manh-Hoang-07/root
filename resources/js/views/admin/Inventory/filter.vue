<template>
  <div class="bg-white p-4 rounded-lg shadow-md mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Tìm kiếm -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
        <input
          v-model="filters.search"
          type="text"
          placeholder="Tên sản phẩm, mã sản phẩm..."
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          @input="debounceSearch"
        />
      </div>

      <!-- Kho hàng -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kho hàng</label>
        <select
          v-model="filters.warehouse_id"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          @change="emitFilters"
        >
          <option value="">Tất cả kho hàng</option>
          <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
            {{ warehouse.name }}
          </option>
        </select>
      </div>

      <!-- Thương hiệu -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Thương hiệu</label>
        <select
          v-model="filters.brand_id"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          @change="emitFilters"
        >
          <option value="">Tất cả thương hiệu</option>
          <option v-for="brand in brands" :key="brand.id" :value="brand.id">
            {{ brand.name }}
          </option>
        </select>
      </div>

      <!-- Danh mục -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
        <select
          v-model="filters.category_id"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          @change="emitFilters"
        >
          <option value="">Tất cả danh mục</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- Bộ lọc nâng cao -->
    <div class="mt-4 pt-4 border-t border-gray-200">
      <div class="flex flex-wrap gap-4">
        <!-- Trạng thái tồn kho -->
        <div class="flex items-center space-x-2">
          <label class="text-sm font-medium text-gray-700">Trạng thái:</label>
          <select
            v-model="filters.stock_status"
            class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @change="emitFilters"
          >
            <option value="">Tất cả trạng thái</option>
            <option value="in_stock">Còn hàng</option>
            <option value="low_stock">Sắp hết</option>
            <option value="out_of_stock">Hết hàng</option>
          </select>
        </div>

                 <!-- Trạng thái hạn sử dụng -->
         <div class="flex items-center space-x-2">
           <label class="text-sm font-medium text-gray-700">Hạn sử dụng:</label>
           <select
             v-model="filters.expiry_status"
             class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
             @change="emitFilters"
           >
             <option value="">Tất cả hạn sử dụng</option>
             <option value="valid">Còn hạn</option>
             <option value="expiring_soon">Sắp hết hạn</option>
             <option value="expired">Đã hết hạn</option>
           </select>
         </div>

        <!-- Sắp xếp -->
        <div class="flex items-center space-x-2">
          <label class="text-sm font-medium text-gray-700">Sắp xếp:</label>
          <select
            v-model="filters.sort_by"
            class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @change="emitFilters"
          >
            <option value="updated_at_desc">Cập nhật gần nhất</option>
            <option value="updated_at_asc">Cập nhật xa nhất</option>
            <option value="available_quantity_desc">Số lượng giảm dần</option>
            <option value="available_quantity_asc">Số lượng tăng dần</option>
            <option value="expiry_date_asc">Hạn sử dụng gần nhất</option>
            <option value="expiry_date_desc">Hạn sử dụng xa nhất</option>
            <option value="product_name_asc">Tên sản phẩm A-Z</option>
            <option value="product_name_desc">Tên sản phẩm Z-A</option>
          </select>
        </div>

        <!-- Nút reset -->
        <button
          @click="resetFilters"
          class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md hover:bg-gray-50"
        >
          Reset
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { debounce } from '@/utils/debounce'

const props = defineProps({
  initialFilters: {
    type: Object,
    default: () => ({})
  },
  warehouses: {
    type: Array,
    default: () => []
  },
  brands: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:filters'])

const filters = ref({
  search: '',
  warehouse_id: '',
  brand_id: '',
  category_id: '',
  stock_status: '',
  expiry_status: '',
  sort_by: 'updated_at_desc',
  ...props.initialFilters
})

// Debounce search
const debounceSearch = debounce(() => {
  emitFilters()
}, 300)

function emitFilters() {
  // Chỉ emit các filter có giá trị
  const activeFilters = {}
  Object.keys(filters.value).forEach(key => {
    if (filters.value[key] !== '' && filters.value[key] !== null && filters.value[key] !== undefined) {
      activeFilters[key] = filters.value[key]
    }
  })
  emit('update:filters', activeFilters)
}

function resetFilters() {
  filters.value = {
    search: '',
    warehouse_id: '',
    brand_id: '',
    category_id: '',
    stock_status: '',
    expiry_status: '',
    sort_by: 'updated_at_desc'
  }
  emitFilters()
}

// Watch for prop changes
watch(() => props.initialFilters, (newFilters) => {
  filters.value = { ...filters.value, ...newFilters }
}, { deep: true })

onMounted(() => {
  // Load filter options if not provided
  if (props.warehouses.length === 0 || props.brands.length === 0 || props.categories.length === 0) {
    // You can load them here if needed
  }
})
</script> 