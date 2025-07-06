<template>
  <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
    <div class="flex items-center space-x-4">
      <div class="flex-shrink-0">
        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
          {{ userInitials }}
        </div>
      </div>
      <div class="flex-1 min-w-0">
        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ user.name }}</h3>
        <p class="text-sm text-gray-500 truncate">{{ user.email }}</p>
        <p class="text-xs text-gray-400 mt-1">ID: {{ user.id }}</p>
      </div>
      <div class="flex-shrink-0">
        <div class="flex space-x-2">
          <button 
            @click="$emit('edit', user)"
            class="text-blue-600 hover:text-blue-800 p-1 rounded"
            title="Sửa"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
          </button>
          <button 
            @click="$emit('delete', user.id)"
            class="text-red-600 hover:text-red-800 p-1 rounded"
            title="Xóa"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
    
    <div class="mt-4 pt-4 border-t border-gray-200">
      <div class="flex justify-between text-sm text-gray-500">
        <span>Ngày tạo:</span>
        <span>{{ formatDate(user.created_at) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

// Props
const props = defineProps({
  user: {
    type: Object,
    required: true
  }
});

// Emits
defineEmits(['edit', 'delete']);

// Computed
const userInitials = computed(() => {
  return props.user.name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2);
});

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('vi-VN');
};
</script> 