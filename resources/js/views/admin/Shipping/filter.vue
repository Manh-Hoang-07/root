<template>
  <div class="flex flex-wrap items-center gap-1 bg-white border border-gray-200 rounded px-2 py-1">
    <div class="flex items-center">
      <MagnifyingGlassIcon class="w-3 h-3 text-gray-400 mr-1" />
      <input
        v-model="filters.search"
        @input="$emit('update:filters', { ...filters })"
        class="h-6 px-1 text-xs border-none focus:ring-0 outline-none bg-transparent"
        placeholder="Tìm kiếm khu vực vận chuyển"
      />
    </div>
    <button @click="$emit('clear')" class="h-6 px-2 text-xs rounded bg-gray-100 hover:bg-gray-200">Xóa lọc</button>
  </div>
</template>

<script setup>
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
const props = defineProps({
  filters: Object
})
</script> 