<template>
  <div class="animate-pulse">
    <!-- Table Skeleton -->
    <div v-if="type === 'table'" class="bg-white shadow-md rounded-lg overflow-hidden">
      <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <div class="h-4 bg-gray-200 rounded w-1/4"></div>
          <div class="h-8 bg-gray-200 rounded w-24"></div>
        </div>
      </div>
      
      <div class="divide-y divide-gray-200">
        <div v-for="i in rows" :key="i" class="px-6 py-4">
          <div class="flex items-center space-x-4">
            <div v-for="j in columns" :key="j" class="flex-1">
              <div class="h-4 bg-gray-200 rounded"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card Skeleton -->
    <div v-else-if="type === 'card'" class="bg-white rounded-lg shadow-md p-6">
      <div class="flex items-center space-x-4 mb-4">
        <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
        <div class="flex-1">
          <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
          <div class="h-3 bg-gray-200 rounded w-1/2"></div>
        </div>
      </div>
      <div class="space-y-2">
        <div class="h-3 bg-gray-200 rounded"></div>
        <div class="h-3 bg-gray-200 rounded w-5/6"></div>
        <div class="h-3 bg-gray-200 rounded w-4/6"></div>
      </div>
    </div>

    <!-- Form Skeleton -->
    <div v-else-if="type === 'form'" class="space-y-6">
      <div v-for="i in fields" :key="i" class="space-y-2">
        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
        <div class="h-10 bg-gray-200 rounded"></div>
      </div>
      <div class="flex justify-end space-x-3 pt-4">
        <div class="h-10 bg-gray-200 rounded w-20"></div>
        <div class="h-10 bg-gray-200 rounded w-20"></div>
      </div>
    </div>

    <!-- List Skeleton -->
    <div v-else-if="type === 'list'" class="space-y-3">
      <div v-for="i in items" :key="i" class="flex items-center space-x-3 p-3 bg-white rounded-lg shadow">
        <div class="w-10 h-10 bg-gray-200 rounded"></div>
        <div class="flex-1">
          <div class="h-4 bg-gray-200 rounded w-3/4 mb-1"></div>
          <div class="h-3 bg-gray-200 rounded w-1/2"></div>
        </div>
        <div class="w-20 h-8 bg-gray-200 rounded"></div>
      </div>
    </div>

    <!-- Default Skeleton -->
    <div v-else class="space-y-3">
      <div v-for="i in items" :key="i" class="h-4 bg-gray-200 rounded"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'default',
    validator: (value) => ['table', 'card', 'form', 'list', 'default'].includes(value)
  },
  rows: {
    type: Number,
    default: 5
  },
  columns: {
    type: Number,
    default: 4
  },
  fields: {
    type: Number,
    default: 5
  },
  items: {
    type: Number,
    default: 3
  }
})
</script>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}
</style> 