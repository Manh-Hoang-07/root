<template>
  <div class="bg-white p-4 rounded-lg shadow mb-6">
    <form @submit.prevent="applyFilters">
      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <slot></slot>
        
        <div class="flex items-end space-x-2">
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
          >
            Lọc
          </button>
          <button
            type="button"
            @click="resetFilters"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none"
          >
            Đặt lại
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
const emit = defineEmits(['apply', 'reset'])

function applyFilters() {
  emit('apply')
}

function resetFilters() {
  emit('reset')
}
</script> 