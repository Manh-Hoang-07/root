<template>
  <section class="bg-white rounded-xl shadow-sm border p-6 mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
      <div>
        <h2 class="text-lg font-semibold text-gray-900">{{ title }}</h2>
        <p v-if="description" class="text-gray-500 text-sm mt-1">{{ description }}</p>
      </div>
      <div v-if="$slots.actions">
        <slot name="actions" />
      </div>
    </div>
    <div>
      <slot />
    </div>
  </section>
</template>
<script setup>
defineProps({
  title: String,
  description: String
})
</script> 
