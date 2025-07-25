import { computed } from 'vue'

export function useUrl(props, objectName = 'user', field = 'avatar', fallbackField = 'profile') {
  return computed(() => {
    const obj = props[objectName] || {}
    if (obj[field]) {
      return obj[field].startsWith('http') ? obj[field] : `/storage/${obj[field]}`
    } else if (obj[fallbackField]?.[field]) {
      return obj[fallbackField][field].startsWith('http')
        ? obj[fallbackField][field]
        : `/storage/${obj[fallbackField][field]}`
    }
    return null
  })
} 