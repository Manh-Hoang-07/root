<template>
  <form @submit.prevent="onSubmit" class="bg-white rounded-2xl shadow-lg p-6 space-y-4 min-w-[320px] max-w-md">
    <slot name="title"></slot>
    <slot></slot>
    <div class="flex justify-end gap-2 pt-4">
      <slot name="actions">
        <button type="button" @click="onCancel" class="px-4 py-2 bg-gray-100 rounded-xl hover:bg-gray-200">Hủy</button>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">Lưu</button>
      </slot>
    </div>
  </form>
</template>
<script setup>
const emit = defineEmits(['submit', 'cancel'])
function onSubmit(e) {
  emit('submit', e)
}
function onCancel() {
  emit('cancel')
}
</script> 