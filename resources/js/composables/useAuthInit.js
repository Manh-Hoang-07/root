import { onMounted } from 'vue'
import { useAuthStore } from '../stores/auth.js'

export function useAuthInit() {
  const authStore = useAuthStore()
  
  onMounted(async () => {
    console.log('🚀 Initializing auth store...')
    await authStore.checkAuth()
    console.log('✅ Auth store initialized:', {
      isAuthenticated: authStore.isAuthenticated,
      user: authStore.user,
      userRole: authStore.userRole
    })
  })
  
  return { authStore }
} 