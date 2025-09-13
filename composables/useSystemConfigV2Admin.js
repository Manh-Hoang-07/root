// composables/useSystemConfigV2Admin.js
import { ref } from 'vue'

export const useSystemConfigV2Admin = () => {
  const loading = ref(false)
  const error = ref(null)

  // Base URL cho API
  const baseURL = process.env.NODE_ENV === 'production' 
    ? 'https://your-api-domain.com/api' 
    : 'http://localhost:8000/api'

  // Lấy admin token từ localStorage hoặc cookie
  const getAdminToken = () => {
    return localStorage.getItem('admin_token') || useCookie('admin_token').value
  }

  // Cập nhật cấu hình hệ thống chung
  const updateGeneralConfig = async (data) => {
    try {
      loading.value = true
      error.value = null
      
      const token = getAdminToken()
      if (!token) {
        throw new Error('Admin token không tồn tại')
      }

      const response = await $fetch(`${baseURL}/admin/config-v2/general`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: data
      })
      
      if (response.success) {
        return response.data
      } else {
        throw new Error(response.message || 'Lỗi khi cập nhật cấu hình')
      }
    } catch (err) {
      error.value = err.message
      console.error('Error updating general config:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Cập nhật cấu hình email
  const updateEmailConfig = async (data) => {
    try {
      loading.value = true
      error.value = null
      
      const token = getAdminToken()
      if (!token) {
        throw new Error('Admin token không tồn tại')
      }

      const response = await $fetch(`${baseURL}/admin/config-v2/email`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: data
      })
      
      if (response.success) {
        return response.data
      } else {
        throw new Error(response.message || 'Lỗi khi cập nhật cấu hình email')
      }
    } catch (err) {
      error.value = err.message
      console.error('Error updating email config:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Cập nhật cấu hình theo key
  const updateConfigByKey = async (key, value) => {
    try {
      loading.value = true
      error.value = null
      
      const token = getAdminToken()
      if (!token) {
        throw new Error('Admin token không tồn tại')
      }

      const response = await $fetch(`${baseURL}/admin/config-v2/key`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: { key, value }
      })
      
      if (response.success) {
        return response.data
      } else {
        throw new Error(response.message || 'Lỗi khi cập nhật cấu hình')
      }
    } catch (err) {
      error.value = err.message
      console.error('Error updating config by key:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    updateGeneralConfig,
    updateEmailConfig,
    updateConfigByKey
  }
}
