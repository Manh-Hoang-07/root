<template>
  <transition name="fade">
    <div v-if="displayError" class="text-red-500 text-sm mt-1">{{ displayError }}</div>
  </transition>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
  error: [String, Array, null]
})



const displayError = computed(() => {
  if (!props.error) return null
  
  if (Array.isArray(props.error)) {
    return props.error[0]
  }
  
  return props.error
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style> 
