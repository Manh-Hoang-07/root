<template>
  <div class="searchable-multi-select relative">
    <!-- Selected items display -->
    <div class="selected-items mb-3">
      <div v-if="selectedItems.length > 0">
        <!-- Compact view when too many items -->
        <div v-if="selectedItems.length > 3 && !showAllSelected" class="flex items-center gap-2">
          <div class="flex items-center gap-1">
            <span 
              v-for="(item, index) in selectedItems.slice(0, 2)" 
              :key="item.value"
              class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm bg-blue-50 border border-blue-200 text-blue-700"
            >
              <span class="mr-2">{{ item.label }}</span>
              <button 
                @click="removeItem(item)" 
                class="text-blue-500 hover:text-blue-700 focus:outline-none transition-colors"
                type="button"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </span>
          </div>
          <button 
            @click="showAllSelected = true"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium focus:outline-none transition-colors"
            type="button"
          >
            +{{ selectedItems.length - 2 }} more
          </button>
        </div>
        
        <!-- Full view when expanded or few items -->
        <div v-else class="flex flex-wrap gap-2">
          <span 
            v-for="item in selectedItems" 
            :key="item.value"
            class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100 transition-colors"
          >
            <span class="mr-2">{{ item.label }}</span>
            <button 
              @click="removeItem(item)" 
              class="text-blue-500 hover:text-blue-700 focus:outline-none transition-colors"
              type="button"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </span>
          
          <!-- Collapse button when expanded -->
          <button 
            v-if="selectedItems.length > 3 && showAllSelected"
            @click="showAllSelected = false"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium focus:outline-none transition-colors"
            type="button"
          >
            Show less
          </button>
        </div>
      </div>
      <div v-else class="text-sm text-gray-500 italic">
        Chưa có danh mục nào được chọn
      </div>
    </div>
    
    <!-- Search input -->
    <div class="relative">
      <input
        v-model="searchQuery"
        @input="handleInput"
        @focus="handleFocus"
        @blur="handleBlur"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="[
          'w-full pl-10 pr-3 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
          error ? 'border-red-500' : 'border-gray-300',
          disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'
        ]"
      />
      <!-- Search icon -->
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
    </div>
    
    <!-- Loading indicator -->
    <div v-if="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
      <div class="animate-spin h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full"></div>
    </div>
    
    <!-- Dropdown -->
    <div 
      v-if="showDropdown && (filteredOptions.length > 0 || searchQuery.length > 0)" 
      class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto"
    >
      <div 
        v-for="option in filteredOptions" 
        :key="option.value"
        @click="selectOption(option)"
        class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 flex items-center justify-between transition-colors"
      >
        <span class="text-gray-700">{{ option.label }}</span>
        <span v-if="isSelected(option)" class="text-blue-600">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
          </svg>
        </span>
      </div>
      
      <!-- No results -->
      <div v-if="searchQuery.length > 0 && filteredOptions.length === 0 && !loading" class="px-4 py-3 text-gray-500 text-center">
        <svg class="w-6 h-6 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.881-6.08-2.33"></path>
        </svg>
        Không tìm thấy kết quả
      </div>
      
      <!-- Too many items notice -->
      <div v-if="!searchQuery && options.length > 50" class="px-4 py-3 text-sm text-gray-500 bg-gray-50 border-t border-gray-100">
        <div class="flex items-center">
          <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Hiển thị 50 items đầu tiên. Vui lòng tìm kiếm để xem thêm.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { debounce } from '@/utils/debounce'
import axios from 'axios'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  placeholder: {
    type: String,
    default: 'Tìm kiếm...'
  },
  searchApi: {
    type: String,
    required: true
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  minSearchLength: {
    type: Number,
    default: 2
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const searchQuery = ref('')
const showDropdown = ref(false)
const options = ref([])
const loading = ref(false)
const selectedItems = ref([])
const showAllSelected = ref(false)

// Filter options to exclude already selected items
const filteredOptions = computed(() => {
  if (!searchQuery.value) {
    return options.value.slice(0, 10).filter(option => !isSelected(option))
  }
  return options.value.filter(option => 
    !isSelected(option) && 
    option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// Load default options when focused
const loadDefaultOptions = async () => {
  if (options.value.length === 0) {
    loading.value = true
    try {
      const response = await axios.get(`${props.searchApi}?limit=50`)
      const allOptions = response.data.data || []
      options.value = allOptions
    } catch (error) {
      console.error('Error loading default options:', error)
    } finally {
      loading.value = false
    }
  }
}

// Debounced search function
const debouncedSearch = debounce(async () => {
  if (searchQuery.value.length < props.minSearchLength) {
    options.value = []
    return
  }
  
  loading.value = true
  try {
    const response = await axios.get(`${props.searchApi}?search=${encodeURIComponent(searchQuery.value)}`)
    options.value = response.data.data || []
  } catch (error) {
    console.error('Search error:', error)
    options.value = []
  } finally {
    loading.value = false
  }
}, 300)

// Handle input changes
const handleInput = () => {
  if (searchQuery.value.length >= props.minSearchLength) {
    debouncedSearch()
  } else {
    options.value = []
  }
}

// Handle focus
const handleFocus = async () => {
  showDropdown.value = true
  await loadDefaultOptions()
}

// Handle blur with delay to allow click events
const handleBlur = () => {
  setTimeout(() => {
    showDropdown.value = false
  }, 200)
}

// Select an option
const selectOption = (option) => {
  if (!isSelected(option)) {
    selectedItems.value.push(option)
    emit('update:modelValue', selectedItems.value.map(item => item.value))
    emit('change', selectedItems.value.map(item => item.value))
  }
  searchQuery.value = ''
}

// Remove an item
const removeItem = (item) => {
  selectedItems.value = selectedItems.value.filter(i => i.value !== item.value)
  emit('update:modelValue', selectedItems.value.map(item => item.value))
  emit('change', selectedItems.value.map(item => item.value))
}

// Check if option is already selected
const isSelected = (option) => {
  return selectedItems.value.some(item => item.value === option.value)
}

// Watch for modelValue changes to update selected items
watch(() => props.modelValue, async (newValue) => {
  if (newValue && newValue.length > 0) {
    // Try to find items in current options
    const foundItems = []
    const missingIds = []
    
    for (const id of newValue) {
      const found = options.value.find(opt => opt.value === id)
      if (found) {
        foundItems.push(found)
      } else {
        missingIds.push(id)
      }
    }
    
    // Fetch missing items from API
    if (missingIds.length > 0) {
      try {
        const response = await axios.get(`${props.searchApi}?ids=${missingIds.join(',')}`)
        if (response.data.data) {
          foundItems.push(...response.data.data)
        }
      } catch (error) {
        console.error('Error fetching missing items:', error)
      }
    }
    
    selectedItems.value = foundItems
    // Reset showAllSelected when items change
    showAllSelected.value = false
  } else {
    selectedItems.value = []
    showAllSelected.value = false
  }
}, { immediate: true })

// Load initial options if modelValue exists
onMounted(async () => {
  if (props.modelValue && props.modelValue.length > 0) {
    try {
      const response = await axios.get(`${props.searchApi}?ids=${props.modelValue.join(',')}`)
      if (response.data.data) {
        selectedItems.value = response.data.data
        // Add to options so they show in dropdown
        options.value = response.data.data
      }
    } catch (error) {
      console.error('Error loading initial items:', error)
    }
  }
})
</script>

<style scoped>
.searchable-multi-select {
  position: relative;
}

.dropdown {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.selected-items {
  min-height: 2rem;
}
</style> 