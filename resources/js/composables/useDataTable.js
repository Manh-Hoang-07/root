import { ref, reactive, computed } from 'vue'
import apiClient from '@/api/apiClient'

export function useDataTable(endpoint, options = {}) {
  const {
    defaultFilters = {},
    defaultSort = 'created_at_desc',
    cacheEnabled = true,
    debounceTime = 300
  } = options

  // State
  const items = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = reactive({
    current_page: 1,
    from: 0,
    to: 0,
    total: 0,
    per_page: 10,
    links: []
  })
  
  const filters = reactive({ ...defaultFilters })
  const cache = new Map()
  let debounceTimer = null

  // Computed
  const hasData = computed(() => items.value.length > 0)
  const isEmpty = computed(() => !loading.value && items.value.length === 0)
  const isFirstPage = computed(() => pagination.current_page === 1)
  const isLastPage = computed(() => pagination.current_page === pagination.last_page)

  // Cache key generator
  const getCacheKey = (params) => {
    return JSON.stringify({ ...filters, ...params })
  }

  // Debounced fetch function
  const debouncedFetch = (params = {}) => {
    if (debounceTimer) {
      clearTimeout(debounceTimer)
    }
    
    debounceTimer = setTimeout(() => {
      fetchData(params)
    }, debounceTime)
  }

  // Main fetch function with caching
  const fetchData = async (params = {}) => {
    loading.value = true
    error.value = null
    
    try {
      const requestParams = { ...filters, ...params }
      const cacheKey = getCacheKey(requestParams)
      
      // Check cache first
      if (cacheEnabled && cache.has(cacheKey)) {
        const cachedData = cache.get(cacheKey)
        items.value = cachedData.data
        Object.assign(pagination, cachedData.meta)
        loading.value = false
        return cachedData
      }
      
      // Fetch from server using apiClient (with authentication)
      const response = await apiClient.get(endpoint, { params: requestParams })
      const { data, meta } = response.data
      
      // Update state
      items.value = data
      if (meta) {
        Object.assign(pagination, meta)
      }
      
      // Cache the result
      if (cacheEnabled) {
        cache.set(cacheKey, { data, meta })
      }
      
      return { data, meta }
    } catch (err) {
      error.value = err.response?.data?.message || 'Có lỗi xảy ra khi tải dữ liệu'
      console.error('DataTable fetch error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // Filter handlers
  const updateFilters = (newFilters) => {
    Object.assign(filters, newFilters)
    debouncedFetch({ page: 1 })
  }

  const resetFilters = () => {
    Object.assign(filters, defaultFilters)
    debouncedFetch({ page: 1 })
  }

  // Pagination handlers
  const changePage = (page) => {
    fetchData({ page })
  }

  const changePageSize = (size) => {
    pagination.per_page = size
    fetchData({ page: 1, per_page: size })
  }

  // CRUD operations
  const createItem = async (itemData) => {
    try {
      const response = await apiClient.post(endpoint, itemData)
      // Clear cache and refresh
      if (cacheEnabled) {
        cache.clear()
      }
      await fetchData()
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Có lỗi xảy ra khi tạo mới'
      throw err
    }
  }

  const updateItem = async (id, itemData) => {
    try {
      const response = await apiClient.put(`${endpoint}/${id}`, itemData)
      // Clear cache and refresh
      if (cacheEnabled) {
        cache.clear()
      }
      await fetchData()
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Có lỗi xảy ra khi cập nhật'
      throw err
    }
  }

  const deleteItem = async (id) => {
    try {
      await apiClient.delete(`${endpoint}/${id}`)
      // Clear cache and refresh
      if (cacheEnabled) {
        cache.clear()
      }
      await fetchData()
    } catch (err) {
      error.value = err.response?.data?.message || 'Có lỗi xảy ra khi xóa'
      throw err
    }
  }

  // Utility functions
  const clearCache = () => {
    cache.clear()
  }

  const refresh = () => {
    return fetchData()
  }

  return {
    // State
    items,
    loading,
    error,
    pagination,
    filters,
    
    // Computed
    hasData,
    isEmpty,
    isFirstPage,
    isLastPage,
    
    // Methods
    fetchData,
    updateFilters,
    resetFilters,
    changePage,
    changePageSize,
    createItem,
    updateItem,
    deleteItem,
    clearCache,
    refresh,
    debouncedFetch
  }
} 