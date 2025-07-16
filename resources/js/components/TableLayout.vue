<template>
  <div class="bg-white rounded-2xl shadow-lg p-6">
    <slot name="header"></slot>
    <slot name="filter"></slot>
    <div v-if="loading" class="text-center py-8">Đang tải...</div>
    <div v-else>
      <slot name="table"></slot>
      <slot name="pagination">
        <div v-if="pagination && pagination.total > pagination.per_page" class="flex justify-center mt-4">
          <button
            v-for="page in totalPages"
            :key="page"
            :class="['px-3 py-1 mx-1 rounded', page === pagination.current_page ? 'bg-indigo-600 text-white' : 'bg-gray-100']"
            @click="$emit('page-change', page)"
          >{{ page }}</button>
        </div>
      </slot>
    </div>
    <slot name="actions"></slot>
  </div>
</template>
<script setup>
import { computed } from 'vue'
const props = defineProps({
  loading: Boolean,
  pagination: Object, // { current_page, per_page, total }
  hasData: Boolean
})
const totalPages = computed(() => {
  if (!props.pagination) return 1
  return Math.ceil(props.pagination.total / props.pagination.per_page)
})
</script> 