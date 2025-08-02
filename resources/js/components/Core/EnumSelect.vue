<template>
  <div class="enum-select">
    <select 
      v-model="selectedValue" 
      :class="selectClass"
      :disabled="disabled || loading"
      @change="handleChange"
    >
      <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
      <option 
        v-for="item in enumData" 
        :key="item.id" 
        :value="item.value"
      >
        {{ item.label }}
      </option>
    </select>
    
    <div v-if="loading" class="loading-indicator">
      <span class="text-sm text-gray-500">Đang tải...</span>
    </div>
    
    <div v-if="error" class="error-message">
      <span class="text-sm text-red-500">{{ error }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { getEnumSync } from '@/constants/enums'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    required: true
  },
  placeholder: {
    type: String,
    default: 'Chọn...'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  useStatic: {
    type: Boolean,
    default: true
  },
  forceRefresh: {
    type: Boolean,
    default: false
  },
  selectClass: {
    type: String,
    default: 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const loading = ref(false)
const error = ref(null)

const selectedValue = ref(props.modelValue)
const enumData = ref([])

// Watch for modelValue changes
watch(() => props.modelValue, (newValue) => {
  selectedValue.value = newValue
})

// Watch for type changes
watch(() => props.type, () => {
  loadEnumData()
})

const handleChange = () => {
  emit('update:modelValue', selectedValue.value)
  emit('change', selectedValue.value)
}

const loadEnumData = () => {
  // Chỉ sử dụng static enum để tránh API calls
  enumData.value = getEnumSync(props.type)
}

onMounted(() => {
  loadEnumData()
})
</script>

<style scoped>
.enum-select {
  position: relative;
}

.loading-indicator {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}

.error-message {
  margin-top: 4px;
}
</style> 
