# SystemConfigV2 - Frontend Composables

## 1. useSystemConfigV2.js

```javascript
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
```

## 2. useSiteConfig.js (Simplified)

```javascript
// composables/useSiteConfig.js
import { useSystemConfigV2 } from './useSystemConfigV2'

export const useSiteConfig = () => {
  const {
    config,
    loading,
    error,
    getGeneralConfig,
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
  } = useSystemConfigV2()

  // Load config khi khởi tạo
  const initConfig = async () => {
    try {
      await getGeneralConfig()
    } catch (err) {
      console.error('Failed to load site config:', err)
    }
  }

  return {
    config,
    loading,
    error,
    initConfig,
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
```

## 3. Sử dụng trong component

```vue
<!-- pages/home/index.vue -->
<template>
  <div>
    <div v-if="loading">Đang tải...</div>
    <div v-else-if="error">Lỗi: {{ error }}</div>
    <div v-else>
      <h1>{{ siteName }}</h1>
      <img :src="siteLogo" :alt="siteName" />
      <p>{{ siteDescription }}</p>
      <p>Email: {{ siteEmail }}</p>
      <p>Phone: {{ sitePhone }}</p>
      <p>Address: {{ siteAddress }}</p>
    </div>
  </div>
</template>

<script setup>
import { useSiteConfig } from '~/composables/useSiteConfig'

const {
  loading,
  error,
  initConfig,
  siteName,
  siteLogo,
  siteEmail,
  sitePhone,
  siteAddress,
  siteDescription
} = useSiteConfig()

// Khởi tạo config khi component mount
onMounted(async () => {
  await initConfig()
})
</script>
```

## 4. Admin composable (cho cập nhật config)

```javascript
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
```

## 5. Sử dụng trong admin page

```vue
<!-- pages/admin/config/index.vue -->
<template>
  <div>
    <h1>Cấu hình hệ thống</h1>
    
    <form @submit.prevent="handleSubmit">
      <div>
        <label>Tên website:</label>
        <input v-model="form.site_name" type="text" />
      </div>
      
      <div>
        <label>Email:</label>
        <input v-model="form.site_email" type="email" />
      </div>
      
      <div>
        <label>Số điện thoại:</label>
        <input v-model="form.site_phone" type="text" />
      </div>
      
      <div>
        <label>Địa chỉ:</label>
        <textarea v-model="form.site_address"></textarea>
      </div>
      
      <button type="submit" :disabled="loading">
        {{ loading ? 'Đang cập nhật...' : 'Cập nhật' }}
      </button>
    </form>
    
    <div v-if="error" class="error">{{ error }}</div>
  </div>
</template>

<script setup>
import { useSystemConfigV2Admin } from '~/composables/useSystemConfigV2Admin'

const { loading, error, updateGeneralConfig } = useSystemConfigV2Admin()

const form = ref({
  site_name: '',
  site_email: '',
  site_phone: '',
  site_address: ''
})

const handleSubmit = async () => {
  try {
    await updateGeneralConfig(form.value)
    alert('Cập nhật thành công!')
  } catch (err) {
    console.error('Update failed:', err)
  }
}
</script>
```

## 6. Environment Variables

```bash
# .env
NUXT_PUBLIC_API_BASE_URL=http://localhost:8000/api
```

## 7. Nuxt Config

```javascript
// nuxt.config.js
export default {
  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://localhost:8000/api'
    }
  }
}
```

## Notes

- Thay `$fetch` bằng `fetch` nếu không dùng Nuxt
- Cập nhật `baseURL` theo domain thực tế
- Xử lý authentication token phù hợp với hệ thống
- Thêm error handling và loading states
- Có thể thêm retry logic cho các request quan trọng
