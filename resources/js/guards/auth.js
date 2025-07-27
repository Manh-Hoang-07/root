import { Auth } from '../utils/auth'

// Auth guards giống Laravel
export const authGuards = {
  // Kiểm tra đã đăng nhập
  auth: (to, from, next) => {
    if (Auth.check()) {
      next()
    } else {
      next('/login')
    }
  },

  // Kiểm tra chưa đăng nhập (guest)
  guest: (to, from, next) => {
    if (Auth.guest()) {
      next()
    } else {
      next('/dashboard')
    }
  },

  // Kiểm tra role admin
  admin: (to, from, next) => {
    if (Auth.isAdmin()) {
      next()
    } else {
      next('/dashboard')
    }
  },

  // Kiểm tra role user
  user: (to, from, next) => {
    if (Auth.isUser()) {
      next()
    } else {
      next('/dashboard')
    }
  },

  // Kiểm tra có permission
  can: (permission) => {
    return (to, from, next) => {
      if (Auth.can(permission)) {
        next()
      } else {
        next('/dashboard')
      }
    }
  },

  // Kiểm tra có role
  role: (role) => {
    return (to, from, next) => {
      if (Auth.hasRole(role)) {
        next()
      } else {
        next('/dashboard')
      }
    }
  }
} 