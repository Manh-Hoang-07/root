<template>
  <div class="flex items-center justify-center" :class="containerClass">
    <div class="relative">
      <!-- Spinner -->
      <div 
        class="animate-spin rounded-full border-2 border-gray-300 border-t-blue-500"
        :class="spinnerClass"
      ></div>
      
      <!-- Loading text -->
      <div v-if="showText" class="mt-4 text-center">
        <p class="text-sm text-gray-600">{{ text }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

// Props
const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  text: {
    type: String,
    default: 'Đang tải...'
  },
  showText: {
    type: Boolean,
    default: true
  },
  fullScreen: {
    type: Boolean,
    default: false
  }
});

// Computed
const spinnerClass = computed(() => {
  const sizes = {
    sm: 'w-4 h-4',
    md: 'w-8 h-8',
    lg: 'w-12 h-12',
    xl: 'w-16 h-16'
  };
  return sizes[props.size];
});

const containerClass = computed(() => {
  return props.fullScreen 
    ? 'fixed inset-0 bg-white bg-opacity-75 z-50' 
    : 'py-8';
});
</script> 
