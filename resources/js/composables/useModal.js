import { ref } from 'vue'

export default function useModal(init = false) {
  const show = ref(init)
  function open() { show.value = true }
  function close() { show.value = false }
  return { show, open, close }
} 