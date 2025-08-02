<template>
  <div class="flex items-center space-x-2">
    <!-- Edit button -->
    <button 
      v-if="showEdit"
      @click="$emit('edit', item)" 
      class="p-2 rounded-full hover:bg-indigo-100 transition-colors duration-200"
      :title="editTitle || 'Sửa'"
    >
      <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
      </svg>
    </button>

    <!-- Delete button -->
    <button 
      v-if="showDelete"
      @click="$emit('delete', item)" 
      class="p-2 rounded-full hover:bg-red-100 transition-colors duration-200"
      :title="deleteTitle || 'Xóa'"
    >
      <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
      </svg>
    </button>

    <!-- View button -->
    <button 
      v-if="showView"
      @click="$emit('view', item)" 
      class="p-2 rounded-full hover:bg-blue-100 transition-colors duration-200"
      :title="viewTitle || 'Xem'"
    >
      <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
      </svg>
    </button>

    <!-- Custom actions -->
    <slot name="custom" :item="item"></slot>
  </div>
</template>

<script setup>
defineProps({
  item: {
    type: Object,
    required: true
  },
  showEdit: {
    type: Boolean,
    default: true
  },
  showDelete: {
    type: Boolean,
    default: true
  },
  showView: {
    type: Boolean,
    default: false
  },
  editTitle: {
    type: String,
    default: 'Sửa'
  },
  deleteTitle: {
    type: String,
    default: 'Xóa'
  },
  viewTitle: {
    type: String,
    default: 'Xem'
  }
})

defineEmits(['edit', 'delete', 'view'])
</script> 
