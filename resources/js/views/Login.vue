<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo/Brand -->
      <div class="text-center">
        <div class="mx-auto h-12 w-12 bg-blue-600 rounded-full flex items-center justify-center">
          <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
        </div>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
          Đăng nhập
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Đăng nhập vào tài khoản của bạn
        </p>
      </div>

      <!-- Login Form -->
      <div class="bg-white py-8 px-6 shadow-xl rounded-xl border border-gray-200">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Email Field -->
          <FormField
            v-model="form.email"
            label="Email"
            name="email"
            type="email"
            :error="errors.email"
            required
            placeholder="Nhập email của bạn"
            autocomplete="email"
            @update:model-value="clearError('email')"
          />

          <!-- Password Field -->
          <FormField
            v-model="form.password"
            label="Mật khẩu"
            name="password"
            type="password"
            :error="errors.password"
            required
            placeholder="Nhập mật khẩu của bạn"
            autocomplete="current-password"
            @update:model-value="clearError('password')"
          />

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <FormField
                v-model="form.remember"
                type="checkbox"
                name="remember"
                checkbox-label="Ghi nhớ đăng nhập"
                @update:model-value="clearError('remember')"
              />
            </div>
            <div class="text-sm">
              <a href="#" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                Quên mật khẩu?
              </a>
            </div>
          </div>

          <!-- Submit Button -->
          <div>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
            >
              <span v-if="isSubmitting" class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              {{ isSubmitting ? 'Đang đăng nhập...' : 'Đăng nhập' }}
            </button>
          </div>

          <!-- Error Message -->
          <div v-if="generalError" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-800">{{ generalError }}</p>
              </div>
            </div>
          </div>
        </form>

        <!-- Divider -->
        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">Hoặc</span>
            </div>
          </div>
        </div>

        <!-- Social Login Buttons -->
        <div class="mt-6 grid grid-cols-2 gap-3">
          <button
            type="button"
            class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors"
          >
            <svg class="h-5 w-5" viewBox="0 0 24 24">
              <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <span class="ml-2">Google</span>
          </button>

          <button
            type="button"
            class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors"
          >
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <span class="ml-2">Facebook</span>
          </button>
        </div>

        <!-- Register Link -->
        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            Chưa có tài khoản?
            <router-link 
              to="/register" 
              class="font-medium text-blue-600 hover:text-blue-500 transition-colors"
            >
              Đăng ký ngay
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import FormField from '../components/Core/FormField.vue'
import validateForm from '../utils/validateForm'

const router = useRouter()
const auth = useAuthStore()

// Form data
const form = reactive({
  email: '',
  password: '',
  remember: false
})

// Form state
const errors = reactive({})
const generalError = ref('')
const isSubmitting = ref(false)

// Validation rules
const validationRules = {
  email: [
    { required: 'Email không được để trống.' },
    { email: 'Email không hợp lệ.' }
  ],
  password: [
    { required: 'Mật khẩu không được để trống.' },
    { min: [6, 'Mật khẩu phải có ít nhất 6 ký tự.'] }
  ]
}

// Clear specific error
function clearError(field) {
  if (errors[field]) {
    delete errors[field]
  }
}

// Clear all errors
function clearAllErrors() {
  Object.keys(errors).forEach(key => delete errors[key])
  generalError.value = ''
}

// Validate form
function validate() {
  clearAllErrors()
  
  const validationErrors = validateForm(form, validationRules)
  if (Object.keys(validationErrors).length > 0) {
    Object.assign(errors, validationErrors)
    return false
  }
  
  return true
}

// Handle login
const handleLogin = async () => {
  if (!validate()) return
  
  isSubmitting.value = true
  generalError.value = ''
  clearAllErrors()
  
  try {
    const res = await auth.login({
      email: form.email,
      password: form.password,
      remember: form.remember
    })
    
    if (res.success) {
      // Redirect based on user role
      if (auth.userRole === 'admin') {
        router.push('/admin')
      } else {
        router.push('/')
      }
    } else {
      // Xử lý lỗi validation từ API
      if (res.errors) {
        Object.assign(errors, res.errors)
      } else {
        generalError.value = res.message || 'Đăng nhập thất bại'
      }
    }
  } catch (error) {
    console.error('Login error:', error)
    generalError.value = 'Lỗi kết nối. Vui lòng thử lại.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
/* Custom styles for better UX */
.form-field {
  transition: all 0.2s ease-in-out;
}

.form-field.has-error {
  opacity: 0.75;
}

/* Focus styles */
input:focus, textarea:focus, select:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

/* Button hover effects */
button:hover:not(:disabled) {
  transform: scale(1.05);
}

/* Loading animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style> 
