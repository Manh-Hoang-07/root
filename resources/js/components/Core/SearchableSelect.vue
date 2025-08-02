<template>
  <div class="searchable-select relative">
    <input
      v-model="displayValue"
      @input="handleInput"
      @focus="handleFocus"
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
        class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
      >
        {{ option.label }}
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
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import { debounce } from '@/utils/debounce'
import axios from 'axios'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
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
const selectedOption = ref(null)

// Display value for input
const displayValue = computed({
  get: () => {
    if (selectedOption.value) {
      return selectedOption.value.label
    }
    return searchQuery.value
  },
  set: (value) => {
    searchQuery.value = value
  }
})

// Filter options based on search query
const filteredOptions = computed(() => {
  if (!searchQuery.value) {
    return options.value.slice(0, 10) // Show first 10 items when no search
  }
  return options.value.filter(option => 
    option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// Load default options when focused
const loadDefaultOptions = async () => {
  if (options.value.length === 0) {
    loading.value = true
    try {
      // Load tất cả nếu ít hơn 50 items, nếu không thì load 50 items đầu
      const response = await axios.get(`${props.searchApi}?limit=50`)
      const allOptions = response.data.data || []
      
      if (allOptions.length <= 50) {
        // Nếu ít hơn 50 items, load tất cả
        options.value = allOptions
      } else {
        // Nếu nhiều hơn 50 items, chỉ load 50 items đầu và hiển thị thông báo
        options.value = allOptions.slice(0, 50)
      }
    } catch (error) {
      
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
  selectedOption.value = option
  searchQuery.value = ''
  showDropdown.value = false
  emit('update:modelValue', option.value)
  emit('change', option.value)
}

// Watch for modelValue changes to update selected option
watch(() => props.modelValue, async (newValue) => {
  if (newValue && !selectedOption.value) {
    // Try to find the option in current options
    const found = options.value.find(opt => opt.value === newValue)
    if (found) {
      selectedOption.value = found
    } else {
      // If not found, fetch it from API
      try {
        const response = await axios.get(`${props.searchApi}?id=${newValue}`)
        if (response.data.data && response.data.data.length > 0) {
          selectedOption.value = response.data.data[0]
        }
      } catch (error) {
        
      }
    }
  } else if (!newValue) {
    selectedOption.value = null
  }
}, { immediate: true })

// Load initial options if modelValue exists
onMounted(async () => {
  if (props.modelValue) {
    try {
      const response = await axios.get(`${props.searchApi}?id=${props.modelValue}`)
      if (response.data.data && response.data.data.length > 0) {
        selectedOption.value = response.data.data[0]
        // Add to options so it shows in dropdown
        options.value = [response.data.data[0]]
      }
    } catch (error) {
      
    }
  }
})
</script>

<style scoped>
.searchable-select {
  position: relative;
}

.dropdown {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style> 
