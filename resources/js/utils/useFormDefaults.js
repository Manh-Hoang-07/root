import { computed } from 'vue'

export function useFormDefaults(props, objectName, fallback = {}) {
  return computed(() => {
    const obj = props[objectName] || {}
    return { ...fallback, ...obj }
  })
} 