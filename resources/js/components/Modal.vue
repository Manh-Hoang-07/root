<template>
  <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div :class="[
      'bg-white shadow-lg relative flex flex-col',
      fullscreen
        ? 'rounded-none w-full h-full m-0 max-w-none max-h-none'
        : 'rounded-xl w-full max-w-lg mx-4 max-h-[90vh]'
    ]">
      <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0">
        <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>
        <button @click="close" class="text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
      </div>
      <div class="px-6 py-4 overflow-y-auto flex-1">
        <slot />
      </div>
      <div v-if="$slots.footer" class="px-6 py-3 border-t bg-gray-50 rounded-b-xl flex justify-end gap-2 flex-shrink-0">
        <slot name="footer" />
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: Boolean, required: true },
  title: { type: String, default: '' },
  fullscreen: { type: Boolean, default: false }
})
const emit = defineEmits(['update:modelValue'])
function close() {
  emit('update:modelValue', false)
}
</script> 