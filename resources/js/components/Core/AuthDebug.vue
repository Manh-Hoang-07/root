<template>
  <div class="fixed bottom-4 right-4 bg-white p-4 rounded-lg shadow-lg border max-w-md z-50">
    <h3 class="font-bold text-lg mb-2">Auth Debug</h3>
    
    <div class="space-y-2 text-sm">
      <div>
        <strong>Cookie Token:</strong> 
        <span :class="debugInfo.hasCookieToken ? 'text-green-600' : 'text-red-600'">
          {{ debugInfo.hasCookieToken ? 'EXISTS' : 'NOT FOUND' }}
        </span>
      </div>
      
      <div>
        <strong>LocalStorage Token:</strong> 
        <span :class="debugInfo.hasLocalToken ? 'text-green-600' : 'text-red-600'">
          {{ debugInfo.hasLocalToken ? 'EXISTS' : 'NOT FOUND' }}
        </span>
      </div>
      
      <div>
        <strong>Auth Store:</strong> 
        <span :class="authStore.isAuthenticated ? 'text-green-600' : 'text-red-600'">
          {{ authStore.isAuthenticated ? 'AUTHENTICATED' : 'NOT AUTHENTICATED' }}
        </span>
      </div>
      
      <div v-if="authStore.user">
        <strong>User Role:</strong> 
        <span class="text-blue-600">{{ authStore.userRole }}</span>
      </div>
      
      <div v-if="apiTestResult">
        <strong>API Test:</strong> 
        <span :class="apiTestResult.success ? 'text-green-600' : 'text-red-600'">
          {{ apiTestResult.success ? 'SUCCESS' : 'FAILED' }}
        </span>
      </div>
    </div>
    
    <div class="mt-3 space-y-1">
      <button 
        @click="runDebug"
        class="w-full bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600"
      >
        Debug Auth
      </button>
      
      <button 
        @click="testAPI"
        class="w-full bg-green-500 text-white px-2 py-1 rounded text-xs hover:bg-green-600"
      >
        Test API
      </button>
      
      <button 
        @click="checkAuth"
        class="w-full bg-purple-500 text-white px-2 py-1 rounded text-xs hover:bg-purple-600"
      >
        Check Auth Store
      </button>
    </div>
    
    <button 
      @click="showDebug = !showDebug"
      class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
    >
      ×
    </button>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { debugAuth, testAuthAPI } from '@/utils/authDebug'

const authStore = useAuthStore()
const showDebug = ref(true)
const apiTestResult = ref(null)

const debugInfo = reactive({
  hasCookieToken: false,
  hasLocalToken: false,
  cookieToken: null,
  localToken: null
})

const runDebug = () => {
  const result = debugAuth()
  Object.assign(debugInfo, result)
}

const testAPI = async () => {
  apiTestResult.value = await testAuthAPI()
}

const checkAuth = async () => {
  console.log('=== AUTH STORE DEBUG ===')
  console.log('isAuthenticated:', authStore.isAuthenticated)
  console.log('user:', authStore.user)
  console.log('userRole:', authStore.userRole)
  console.log('isFetchingUser:', authStore.isFetchingUser)
  
  // Thử fetch user info
  const success = await authStore.fetchUserInfo()
  console.log('fetchUserInfo result:', success)
}

onMounted(() => {
  runDebug()
})
</script> 