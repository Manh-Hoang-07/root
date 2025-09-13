// composables/useSystemConfigV2.js
import { ref, computed } from 'vue'

export const useSystemConfigV2 = () => {
  const config = ref({})
  const loading = ref(false)
  const error = ref(null)

  // Base URL cho API
  const baseURL = process.env.NODE_ENV === 'production' 
    ? 'https://your-api-domain.com/api' 
    : 'http://localhost:8000/api'

  // Lấy cấu hình hệ thống chung
  const getGeneralConfig = async () => {
    try {
      loading.value = true
      error.value = null
      
      const response = await $fetch(`${baseURL}/config-v2/general`)
      
      if (response.success) {
        config.value = { ...config.value, ...response.data }
        return response.data
      } else {
        throw new Error(response.message || 'Lỗi khi lấy cấu hình')
      }
    } catch (err) {
      error.value = err.message
      console.error('Error fetching general config:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Lấy cấu hình email
  const getEmailConfig = async () => {
    try {
      loading.value = true
      error.value = null
      
      const response = await $fetch(`${baseURL}/config-v2/email`)
      
      if (response.success) {
        config.value = { ...config.value, ...response.data }
        return response.data
      } else {
        throw new Error(response.message || 'Lỗi khi lấy cấu hình email')
      }
    } catch (err) {
      error.value = err.message
      console.error('Error fetching email config:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Lấy cấu hình theo key
  const getConfigByKey = async (key, defaultValue = null) => {
    try {
      loading.value = true
      error.value = null
      
      const response = await $fetch(`${baseURL}/config-v2/key`, {
        params: { key, default: defaultValue }
      })
      
      if (response.success) {
        config.value[key] = response.data
        return response.data
      } else {
        throw new Error(response.message || 'Lỗi khi lấy cấu hình')
      }
    } catch (err) {
      error.value = err.message
      console.error('Error fetching config by key:', err)
      return defaultValue
    } finally {
      loading.value = false
    }
  }

  // Computed properties cho các config thường dùng
  const siteName = computed(() => config.value.site_name || 'Website')
  const siteLogo = computed(() => config.value.site_logo || '/images/logo.png')
  const siteEmail = computed(() => config.value.site_email || '')
  const sitePhone = computed(() => config.value.site_phone || '')
  const siteAddress = computed(() => config.value.site_address || '')
  const siteDescription = computed(() => config.value.site_description || '')
  const timezone = computed(() => config.value.timezone || 'Asia/Ho_Chi_Minh')
  const language = computed(() => config.value.language || 'vi')
  const maintenanceMode = computed(() => config.value.maintenance_mode || false)
  const itemsPerPage = computed(() => config.value.items_per_page || 20)

  return {
    // State
    config,
    loading,
    error,
    
    // Methods
    getGeneralConfig,
    getEmailConfig,
    getConfigByKey,
    
    // Computed
    siteName,
    siteLogo,
    siteEmail,
    sitePhone,
    siteAddress,
    siteDescription,
    timezone,
    language,
    maintenanceMode,
    itemsPerPage
  }
}
