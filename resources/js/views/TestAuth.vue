<template>
  <div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold mb-6">Test Authentication</h1>
    
    <!-- Debug Info -->
    <div class="bg-gray-100 p-4 rounded-lg mb-6">
      <h2 class="text-lg font-semibold mb-2">Debug Info:</h2>
      <pre class="text-sm">{{ debugInfo }}</pre>
    </div>
    
    <!-- Login Form -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">Login Test</h2>
      
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Email:</label>
          <input 
            v-model="form.email" 
            type="email" 
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Password:</label>
          <input 
            v-model="form.password" 
            type="password" 
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>
        
        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 disabled:opacity-50"
        >
          {{ loading ? 'Đang đăng nhập...' : 'Đăng nhập' }}
        </button>
      </form>
      
      <div v-if="message" class="mt-4 p-3 rounded-md" :class="messageClass">
        {{ message }}
      </div>
    </div>
    
    <!-- Test Buttons -->
    <div class="mt-6 space-y-2">
      <button 
        @click="testCookies"
        class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600"
      >
        Test Cookies
      </button>
      
      <button 
        @click="testAuthStore"
        class="bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600"
      >
        Test Auth Store
      </button>
      
      <button 
        @click="testFetchUser"
        class="bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600"
      >
        Test Fetch User
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth.js'
import { debugCookies, debugAuthStore } from '../utils/debug.js'

const authStore = useAuthStore()

const form = ref({
  email: 'admin@example.com',
  password: 'password'
})

const loading = ref(false)
const message = ref('')
const messageClass = ref('')

const debugInfo = computed(() => ({
  cookies: document.cookie,
  authStore: {
    isAuthenticated: authStore.isAuthenticated,
    user: authStore.user,
    userRole: authStore.userRole,
    isAdmin: authStore.isAdmin,
    isUser: authStore.isUser
  }
}))

const handleLogin = async () => {
  loading.value = true
  message.value = ''
  
  try {
    const result = await authStore.login(form.value)
    
    if (result.success) {
      message.value = 'Đăng nhập thành công!'
      messageClass.value = 'bg-green-100 text-green-800'
    } else {
      message.value = result.message || 'Đăng nhập thất bại'
      messageClass.value = 'bg-red-100 text-red-800'
    }
  } catch (error) {
    message.value = 'Lỗi: ' + error.message
    messageClass.value = 'bg-red-100 text-red-800'
  } finally {
    loading.value = false
  }
}

const testCookies = () => {
  debugCookies()
}

const testAuthStore = async () => {
  await debugAuthStore()
}

const testFetchUser = async () => {
  try {
    const success = await authStore.fetchUserInfo()
    console.log('Fetch user result:', success)
    if (success) {
      message.value = 'Fetch user thành công!'
      messageClass.value = 'bg-green-100 text-green-800'
    } else {
      message.value = 'Fetch user thất bại'
      messageClass.value = 'bg-red-100 text-red-800'
    }
  } catch (error) {
    message.value = 'Lỗi fetch user: ' + error.message
    messageClass.value = 'bg-red-100 text-red-800'
  }
}

onMounted(async () => {
  console.log('🚀 TestAuth mounted')
  await authStore.checkAuth()
})
</script> 