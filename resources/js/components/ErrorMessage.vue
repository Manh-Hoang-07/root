<template>
  <div v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</div>
</template>
<script setup>
const props = defineProps({
  error: String
})
</script> 