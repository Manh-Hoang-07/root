import { useAuthStore } from '../stores/auth.js'
import { onMounted } from 'vue'

export function useAuthInit() {
  const authStore = useAuthStore()

  onMounted(async () => {
    // Kiểm tra auth khi app khởi động
    await authStore.checkAuth()
  })

  return {
    authStore
  }
} 