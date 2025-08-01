import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { ENUMS as STATIC_ENUMS } from '@/constants/enums'

export const useEnumStore = defineStore('enum', () => {
  // State
  const enums = ref({})
  const loading = ref(false)
  const error = ref(null)
  const cacheTimestamp = ref({})

  // Cache timeout - 24 giờ
  const CACHE_TIMEOUT = 24 * 60 * 60 * 1000

  // Getters
  const getEnum = computed(() => {
    return (type) => {
      // 1. Check store first
      if (enums.value[type]) {
        const timestamp = cacheTimestamp.value[type] || 0
        if (Date.now() - timestamp < CACHE_TIMEOUT) {
          return enums.value[type]
        }
      }
      
      // 2. Fallback to static enum
      return STATIC_ENUMS[type] || []
    }
  })

  const getEnumLabel = computed(() => {
    return (type, value) => {
      const enumData = getEnum.value(type)
      const item = enumData.find(item => item.value === value)
      return item ? item.label : ''
    }
  })

  const getEnumName = computed(() => {
    return (type, value) => {
      const enumData = getEnum.value(type)
      const item = enumData.find(item => item.value === value)
      return item ? item.name : ''
    }
  })

  // Actions
  const loadEnum = async (type, forceRefresh = false) => {
    // Check if we need to refresh
    if (!forceRefresh && enums.value[type]) {
      const timestamp = cacheTimestamp.value[type] || 0
      if (Date.now() - timestamp < CACHE_TIMEOUT) {
        return enums.value[type]
      }
    }

    loading.value = true
    error.value = null

    try {
      const response = await fetch(`/api/enums/${type}`)
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      
      const result = await response.json()
      
      if (!result.success) {
        throw new Error(result.message || 'Failed to load enum')
      }
      
      // Store the result
      enums.value[type] = result.data
      cacheTimestamp.value[type] = Date.now()
      
      return result.data
    } catch (err) {
      error.value = err.message
      console.error(`Failed to load enum ${type}:`, err)
      
      // Fallback to static enum
      if (STATIC_ENUMS[type]) {
        console.warn(`Using static enum for ${type} as fallback`)
        enums.value[type] = STATIC_ENUMS[type]
        return STATIC_ENUMS[type]
      }
      
      return []
    } finally {
      loading.value = false
    }
  }

  const loadAllEnums = async (types = null, forceRefresh = false) => {
    const enumTypes = types || ['user_status', 'gender', 'basic_status', 'role_status', 'product_status', 'order_status', 'variant_status']
    
    loading.value = true
    error.value = null

    try {
      await Promise.all(
        enumTypes.map(async (type) => {
          await loadEnum(type, forceRefresh)
        })
      )
    } catch (err) {
      error.value = err.message
      console.error('Failed to load all enums:', err)
    } finally {
      loading.value = false
    }
  }

  const clearCache = (type = null) => {
    if (type) {
      delete enums.value[type]
      delete cacheTimestamp.value[type]
    } else {
      enums.value = {}
      cacheTimestamp.value = {}
    }
  }

  const refreshEnum = (type) => {
    return loadEnum(type, true)
  }

  const refreshAllEnums = () => {
    return loadAllEnums(null, true)
  }

  const getCacheInfo = () => {
    const info = {}
    for (const type in cacheTimestamp.value) {
      const timestamp = cacheTimestamp.value[type]
      info[type] = {
        timestamp,
        age: Date.now() - timestamp,
        expired: Date.now() - timestamp > CACHE_TIMEOUT,
        hasData: !!enums.value[type]
      }
    }
    return info
  }

  const isCached = (type) => {
    if (!enums.value[type]) return false
    const timestamp = cacheTimestamp.value[type] || 0
    return Date.now() - timestamp < CACHE_TIMEOUT
  }

  // Initialize with static enums
  const initialize = () => {
    // Pre-load static enums into store
    for (const type in STATIC_ENUMS) {
      enums.value[type] = STATIC_ENUMS[type]
      cacheTimestamp.value[type] = Date.now()
    }
  }

  return {
    // State
    enums: computed(() => enums.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    
    // Getters
    getEnum,
    getEnumLabel,
    getEnumName,
    
    // Actions
    loadEnum,
    loadAllEnums,
    clearCache,
    refreshEnum,
    refreshAllEnums,
    getCacheInfo,
    isCached,
    initialize,
    
    // Static enums for direct access
    staticEnums: STATIC_ENUMS
  }
}) 