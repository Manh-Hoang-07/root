<template>
  <div>
    <label v-if="label" class="block text-sm font-medium mb-1">{{ label }}</label>
    <div class="relative">
      <div 
        @click="toggleDropdown"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer bg-white"
        :class="{ 'border-red-500': error }"
      >
        <div class="flex flex-wrap gap-1">
          <span v-if="modelValue.length === 0" class="text-gray-500">{{ placeholder }}</span>
          <span 
            v-for="item in selectedItems" 
            :key="item.value"
            class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800"
          >
            {{ item.label }}
            <button 
              @click.stop="removeItem(item.value)"
              class="ml-1 text-indigo-600 hover:text-indigo-800"
            >
              ×
            </button>
          </span>
        </div>
      </div>
      
      <!-- Dropdown -->
      <div 
        v-if="showDropdown"
        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
      >
        <div 
          v-for="option in options" 
          :key="option.value"
          @click="toggleItem(option.value)"
          class="px-3 py-2 hover:bg-gray-100 cursor-pointer flex items-center"
        >
          <input 
            type="checkbox" 
            :checked="modelValue.includes(option.value)"
            class="mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            readonly
          />
          {{ option.label }}
        </div>
      </div>
    </div>
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  options: {
    type: Array,
    default: () => []
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Chọn...'
  },
  error: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])

const showDropdown = ref(false)

// Computed để lấy selected items với label
const selectedItems = computed(() => {
  return props.modelValue.map(value => {
    const option = props.options.find(opt => opt.value == value)
    return {
      value: value,
      label: option ? option.label : value
    }
  })
})

// Toggle dropdown
function toggleDropdown() {
  showDropdown.value = !showDropdown.value
}

// Toggle item selection
function toggleItem(value) {
  const newValue = [...props.modelValue]
  const index = newValue.indexOf(value)
  
  if (index > -1) {
    newValue.splice(index, 1)
  } else {
    newValue.push(value)
  }
  
  emit('update:modelValue', newValue)
}

// Remove item
function removeItem(value) {
  const newValue = props.modelValue.filter(item => item !== value)
  emit('update:modelValue', newValue)
}

// Close dropdown when clicking outside
function handleClickOutside(event) {
  if (!event.target.closest('.relative')) {
    showDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script> 
