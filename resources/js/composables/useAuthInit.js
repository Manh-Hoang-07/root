import { useAuthStore } from '../stores/auth.js'
import { onMounted, ref } from 'vue'

export function useAuthInit() {
  const authStore = useAuthStore()
  const isInitializing = ref(false)

  onMounted(async () => {
    // Chỉ kiểm tra auth một lần khi app khởi động
    if (!window.__authInitialized && !isInitializing.value) {
      isInitializing.value = true
      window.__authInitialized = true
      
      try {
        await authStore.checkAuth()
      } catch (error) {
        // Handle error silently
      } finally {
        isInitializing.value = false
      }
    }
  })

  return {
    authStore,
    isInitializing
  }
} 