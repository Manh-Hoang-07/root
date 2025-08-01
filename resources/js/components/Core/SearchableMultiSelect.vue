<template>
  <div class="searchable-multi-select relative">
    <!-- Selected items display -->
    <div class="selected-items mb-2">
      <span 
        v-for="item in selectedItems" 
        :key="item.value"
        class="inline-flex items-center px-2 py-1 rounded-full text-sm bg-blue-100 text-blue-800 mr-2 mb-1"
      >
        {{ item.label }}
        <button 
          @click="removeItem(item)" 
          class="ml-1 text-blue-600 hover:text-blue-800 focus:outline-none"
        >
          ×
        </button>
      </span>
    </div>
    
    <!-- Search input -->
    <input
      v-model="searchQuery"
      @input="handleInput"
      @focus="showDropdown = true"
      @blur="handleBlur"
      :placeholder="placeholder"
      :disabled="disabled"
      :class="[
        'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500',
        error ? 'border-red-500' : 'border-gray-300',
        disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'
      ]"
    />
    
    <!-- Loading indicator -->
    <div v-if="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
      <div class="animate-spin h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full"></div>
    </div>
    
    <!-- Dropdown -->
    <div 
      v-if="showDropdown && (filteredOptions.length > 0 || searchQuery.length > 0)" 
      class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
    >
      <div 
        v-for="option in filteredOptions" 
        :key="option.value"
        @click="selectOption(option)"
        class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0 flex items-center justify-between"
      >
        <span>{{ option.label }}</span>
        <span v-if="isSelected(option)" class="text-blue-600">✓</span>
      </div>
      
      <!-- No results -->
      <div v-if="searchQuery.length > 0 && filteredOptions.length === 0 && !loading" class="px-3 py-2 text-gray-500">
        Không tìm thấy kết quả
      </div>
      
      <!-- Too many items notice -->
      <div v-if="!searchQuery && options.length > 50" class="px-3 py-2 text-sm text-gray-500 bg-gray-50 border-t">
        Hiển thị 50 items đầu tiên. Vui lòng tìm kiếm để xem thêm.
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
  } else {
    selectedItems.value = []
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