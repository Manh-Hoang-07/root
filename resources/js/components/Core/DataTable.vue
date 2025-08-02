<template>
  <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8">
    <!-- Action bar (slot) -->
    <div class="flex flex-wrap items-center justify-between gap-2 px-2 py-1 border-b border-gray-100">
      <div class="flex flex-wrap items-center gap-2">
        <slot name="actions"></slot>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <slot name="filter"></slot>
      </div>
    </div>
    <!-- Table -->
    <div class="overflow-x-auto w-full">
      <table class="min-w-full table-fixed text-sm">
        <thead>
          <tr class="bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-700 font-semibold text-sm uppercase tracking-wider border-b border-gray-200">
            <th class="w-6 px-1 py-1 text-center align-middle">
              <input type="checkbox" :checked="isAllSelected" @change="$emit('toggle-select-all')" class="accent-indigo-500 w-5 h-5 rounded border-gray-300 focus:ring-indigo-500" />
            </th>
            <slot name="thead"></slot>
            <th class="w-32 px-3 py-3 text-center align-middle">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <slot name="tbody"></slot>
        </tbody>
      </table>
    </div>
    <!-- Pagination -->
    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
      <slot name="pagination"></slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  isAllSelected: { type: Boolean, default: false }
})
</script>

<style scoped>
/* Đảm bảo table luôn căn đều, không bị lệch cột */
.table-fixed th, .table-fixed td {
  text-align: left;
  vertical-align: middle;
  white-space: nowrap;
}
.table-fixed th {
  font-weight: 600;
}
.table-fixed tr {
  transition: background 0.2s;
}
.table-fixed tbody tr:hover {
  background: #f5f3ff;
}
</style> 
