import { onMounted } from 'vue'
import { useAuthStore } from '../stores/auth.js'

export function useAuthInit() {
  const authStore = useAuthStore()
  
  onMounted(async () => {
    console.log('🚀 Initializing auth store...')
    console.log('🔍 Before checkAuth:', {
      isAuthenticated: authStore.isAuthenticated,
      user: !!authStore.user,
      userRole: authStore.userRole,
      isFetchingUser: authStore.isFetchingUser
    })
    
    await authStore.checkAuth()
    
    console.log('✅ Auth store initialized:', {
      isAuthenticated: authStore.isAuthenticated,
      user: !!authStore.user,
      userRole: authStore.userRole,
      isFetchingUser: authStore.isFetchingUser
    })
  })
  
  return { authStore }
} 