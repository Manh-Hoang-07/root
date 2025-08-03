import { ref } from 'vue'

const toasts = ref([])
let toastId = 0

export function useToast() {
  const showToast = (message, type = 'info', duration = 3000) => {
    const id = ++toastId
    const toast = {
      id,
      message,
      type,
      duration,
      visible: true
    }
    
    toasts.value.push(toast)
    
    // Auto remove after duration
    setTimeout(() => {
      removeToast(id)
    }, duration)
    
    return id
  }
  
  const removeToast = (id) => {
    const index = toasts.value.findIndex(toast => toast.id === id)
    if (index > -1) {
      toasts.value[index].visible = false
      setTimeout(() => {
        toasts.value.splice(index, 1)
      }, 300) // Animation duration
    }
  }
  
  const showSuccess = (message, duration) => {
    return showToast(message, 'success', duration)
  }
  
  const showError = (message, duration) => {
    return showToast(message, 'error', duration)
  }
  
  const showWarning = (message, duration) => {
    return showToast(message, 'warning', duration)
  }
  
  const showInfo = (message, duration) => {
    return showToast(message, 'info', duration)
  }
  
  const clearAll = () => {
    toasts.value = []
  }
  
  return {
    toasts,
    showToast,
    showSuccess,
    showError,
    showWarning,
    showInfo,
    removeToast,
    clearAll
  }
} 