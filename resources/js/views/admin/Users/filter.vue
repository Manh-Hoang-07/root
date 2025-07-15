<template>
  <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="relative">
        <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
        <input
          :value="filters.search"
          @input="$emit('update:filters', { ...filters, search: $event.target.value })"
          type="text"
          placeholder="Tìm kiếm người dùng..."
          class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        />
      </div>
      <select :value="filters.status" @change="$emit('update:filters', { ...filters, status: $event.target.value })" class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
        <option value="">Tất cả trạng thái</option>
        <option v-for="s in statusEnums" :key="s.value" :value="s.value">{{ s.name }}</option>
      </select>
      <button @click="$emit('clear')" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
        Xóa bộ lọc
      </button>
    </div>
  </div>
</template>
<script setup>
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
const props = defineProps({
  filters: Object,
  statusEnums: Array
})
</script> 