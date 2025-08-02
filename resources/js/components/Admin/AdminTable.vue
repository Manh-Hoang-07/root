<template>
  <div class="admin-table">
    <!-- Header với tiêu đề và nút thêm mới -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">{{ title }}</h1>
      <button 
        v-if="showAddButton"
        @click="$emit('add')" 
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
      >
        {{ addButtonText }}
      </button>
    </div>

    <!-- Filter section -->
    <div v-if="$slots.filters" class="mb-6 bg-white p-4 rounded-lg shadow">
      <slot name="filters"></slot>
    </div>

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th v-if="showCheckbox" class="w-12 px-6 py-3">
              <input 
                type="checkbox" 
                :checked="allSelected" 
                @change="$emit('toggle-all')"
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
              />
            </th>
            <slot name="header"></slot>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Thao tác
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <template v-if="items.length">
            <tr v-for="(item, index) in items" :key="getItemKey(item, index)" :class="{ 'bg-gray-50': index % 2 === 0 }">
              <td v-if="showCheckbox" class="px-6 py-4 whitespace-nowrap">
                <input 
                  type="checkbox" 
                  :checked="isSelected(item)"
                  @change="$emit('toggle-item', item)"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                />
              </td>
              <slot name="row" :item="item" :index="index"></slot>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <slot name="actions" :item="item" :index="index">
                  <Actions 
                    :item="item"
                    @edit="$emit('edit', item)"
                    @delete="$emit('delete', item)"
                  />
                </slot>
              </td>
            </tr>
          </template>
          <tr v-else>
            <td :colspan="colSpan" class="px-6 py-4 text-center text-gray-500">
              {{ emptyText }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Phân trang -->
    <div v-if="showPagination" class="mt-4 flex justify-between items-center">
      <div class="text-sm text-gray-700">
        <span v-if="pagination">
          Hiển thị {{ pagination.from || 0 }} đến {{ pagination.to || 0 }} trên tổng số {{ pagination.total || 0 }} bản ghi
        </span>
      </div>
      <div class="flex space-x-1">
        <slot name="pagination">
          <button 
            v-if="pagination && pagination.links"
            v-for="page in pagination.links" 
            :key="page.label"
            @click="$emit('page-change', page.url)"
            :disabled="!page.url"
            :class="[
              'px-3 py-1 border rounded',
              page.active 
                ? 'bg-blue-600 text-white' 
                : 'bg-white text-gray-700 hover:bg-gray-50',
              !page.url && 'opacity-50 cursor-not-allowed'
            ]"
            v-html="page.label"
          ></button>
        </slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Actions from '../Core/Actions.vue'

const props = defineProps({
  // Dữ liệu
  items: {
    type: Array,
    default: () => []
  },
  // Cấu hình
  title: {
    type: String,
    default: 'Danh sách dữ liệu'
  },
  showAddButton: {
    type: Boolean,
    default: true
  },
  addButtonText: {
    type: String,
    default: 'Thêm mới'
  },
  emptyText: {
    type: String,
    default: 'Không có dữ liệu'
  },
  showCheckbox: {
    type: Boolean,
    default: false
  },
  showPagination: {
    type: Boolean,
    default: true
  },
  // Phân trang
  pagination: {
    type: Object,
    default: null
  },
  // Selection
  selectedItems: {
    type: Array,
    default: () => []
  },
  // Cấu hình khác
  itemKey: {
    type: String,
    default: 'id'
  }
})

const emit = defineEmits([
  'add', 'edit', 'delete', 
  'toggle-all', 'toggle-item', 
  'page-change'
])

// Tính số cột để colspan khi không có dữ liệu
const colSpan = computed(() => {
  // Base: 1 cho cột actions
  let count = 1
  
  // Thêm 1 nếu có checkbox
  if (props.showCheckbox) {
    count += 1
  }
  
  // Đếm số cột header (mỗi th tag trong header slot)
  // Với Inventory: 6 cột header + 1 cột actions = 7
  // Với các table khác: tùy theo số th tags
  // Tạm thời hardcode cho Inventory là 7
  if (props.title && props.title.includes('Tồn kho')) {
    count = 7 // 6 cột header + 1 cột actions
  } else {
    // Mặc định 5 cột cho các table khác
    count = 5
  }
  
  return count
})

// Kiểm tra xem tất cả các item có được chọn không
const allSelected = computed(() => {
  return props.items.length > 0 && props.selectedItems.length === props.items.length
})

// Kiểm tra xem một item có được chọn không
function isSelected(item) {
  return props.selectedItems.some(selectedItem => 
    selectedItem[props.itemKey] === item[props.itemKey]
  )
}

// Lấy key cho item
function getItemKey(item, index) {
  return item[props.itemKey] || index
}
</script> 