<template>
  <Modal :model-value="true" title="Thêm zone mới" @update:model-value="$emit('close')">
    <form @submit.prevent="$emit('save')">
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Tên zone</label>
        <input type="text" class="w-full px-4 py-2 border rounded-xl" required />
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Provinces</label>
        <input type="text" class="w-full px-4 py-2 border rounded-xl" required />
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Base Fee</label>
        <input type="number" class="w-full px-4 py-2 border rounded-xl" required />
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Delivery Time</label>
        <input type="text" class="w-full px-4 py-2 border rounded-xl" required />
      </div>
      <div class="flex justify-end gap-2 mt-6">
        <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="$emit('close')">Hủy</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Thêm</button>
      </div>
    </form>
  </Modal>
</template>
<script setup>
import Modal from '@/components/Core/Modal.vue'
</script> 
