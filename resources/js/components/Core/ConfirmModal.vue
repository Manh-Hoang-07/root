<template>
  <Modal v-model="modalVisible" :title="title">
    <div class="p-4">
      <div class="mb-4 text-sm text-gray-600">
        {{ message }}
      </div>
      <div class="flex justify-end space-x-3">
        <button
          type="button"
          @click="onClose"
          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
        >
          Hủy
        </button>
        <button
          type="button"
          @click="handleConfirm"
          class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none"
        >
          Xác nhận
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import Modal from './Modal.vue'
import { computed } from 'vue'

const props = defineProps({
  show: Boolean,
  title: {
    type: String,
    default: 'Xác nhận'
  },
  message: {
    type: String,
    default: 'Bạn có chắc chắn muốn thực hiện hành động này?'
  },
  onClose: {
    type: Function,
    default: () => {}
  }
})

const emit = defineEmits(['confirm'])

const modalVisible = computed({
  get: () => props.show,
  set: () => props.onClose()
})

function handleConfirm() {
  emit('confirm')
  props.onClose()
}
</script> 
