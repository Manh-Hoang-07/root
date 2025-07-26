import { computed } from 'vue'

export function useUrl(props, objectName = 'user', field = 'avatar', fallbackField = 'profile') {
  return computed(() => {
    const obj = props[objectName] || {}
    const val = obj[field]
    
    if (val) {
      // Nếu là URL http/https, giữ nguyên
      if (val.startsWith('http')) return val
      
      // Nếu đã có /storage/ ở đầu, chỉ giữ 1 lần
      if (val.startsWith('/storage/')) {
        return val.replace(/^(\/storage\/)+/, '/storage/')
      }
      
      // Nếu chỉ là path, nối /storage/
      return `/storage/${val.replace(/^\/storage\//, '')}`
    } else if (obj[fallbackField]?.[field]) {
      const fallbackVal = obj[fallbackField][field]
      
      // Nếu là URL http/https, giữ nguyên
      if (fallbackVal.startsWith('http')) return fallbackVal
      
      // Nếu đã có /storage/ ở đầu, chỉ giữ 1 lần
      if (fallbackVal.startsWith('/storage/')) {
        return fallbackVal.replace(/^(\/storage\/)+/, '/storage/')
      }
      
      // Nếu chỉ là path, nối /storage/
      return `/storage/${fallbackVal.replace(/^\/storage\//, '')}`
    }
    
    return null
  })
} 