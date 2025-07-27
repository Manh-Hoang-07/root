import { Auth } from '../utils/auth'

// Auth directives giống Laravel
export const authDirectives = {
  // v-auth - Chỉ hiển thị khi đã đăng nhập
  auth: {
    mounted(el, binding) {
      if (!Auth.check()) {
        el.style.display = 'none'
      }
    }
  },

  // v-guest - Chỉ hiển thị khi chưa đăng nhập
  guest: {
    mounted(el, binding) {
      if (Auth.check()) {
        el.style.display = 'none'
      }
    }
  },

  // v-role - Chỉ hiển thị khi có role
  role: {
    mounted(el, binding) {
      const requiredRole = binding.value
      if (!Auth.hasRole(requiredRole)) {
        el.style.display = 'none'
      }
    }
  },

  // v-can - Chỉ hiển thị khi có permission
  can: {
    mounted(el, binding) {
      const permission = binding.value
      if (!Auth.can(permission)) {
        el.style.display = 'none'
      }
    }
  },

  // v-admin - Chỉ hiển thị cho admin
  admin: {
    mounted(el, binding) {
      if (!Auth.isAdmin()) {
        el.style.display = 'none'
      }
    }
  },

  // v-user - Chỉ hiển thị cho user thường
  user: {
    mounted(el, binding) {
      if (!Auth.isUser()) {
        el.style.display = 'none'
      }
    }
  }
} 