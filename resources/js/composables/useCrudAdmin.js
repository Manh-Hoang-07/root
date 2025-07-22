import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

/**
 * Composable để quản lý các thao tác CRUD chung cho tất cả các menu admin
 * @param {Object} options - Các tùy chọn
 * @param {Object} options.endpoints - Các endpoint API
 * @param {String} options.resourceName - Tên resource (vd: 'người dùng', 'sản phẩm')
 * @param {Function} options.transformItem - Hàm biến đổi dữ liệu trước khi hiển thị
 * @param {Function} options.beforeSubmit - Hàm xử lý dữ liệu trước khi gửi lên server
 * @param {Function} options.afterFetch - Hàm xử lý dữ liệu sau khi lấy từ server
 * @returns {Object} - Các state và methods cho CRUD
 */
export default function useCrudAdmin(options) {
  // Destructure options
  const { 
    endpoints, 
    resourceName = 'dữ liệu',
    transformItem = (item) => item,
    beforeSubmit = (data) => data,
    afterFetch = (data) => data
  } = options

  // State
  const items = ref([])
  const selectedItems = ref([])
  const selectedItem = ref(null)
  const loading = ref(false)
  const apiErrors = reactive({})
  
  // Modal state
  const showCreateModal = ref(false)
  const showEditModal = ref(false)
  const showDeleteModal = ref(false)
  const showViewModal = ref(false)
  
  // Pagination
  const pagination = reactive({
    current_page: 1,
    from: 0,
    to: 0,
    total: 0,
    per_page: 10,
    links: []
  })
  
  // Filters
  const filters = reactive({
    search: '',
    page: 1
  })

  // CRUD operations
  async function fetchItems(page = 1) {
    loading.value = true
    try {
      const params = { ...filters, page }
      const response = await axios.get(endpoints.list, { params })
      
      // Transform items
      if (response.data.data) {
        items.value = response.data.data.map(transformItem)
      }
      
      // Update pagination
      if (response.data.meta) {
        const meta = response.data.meta
        pagination.current_page = meta.current_page
        pagination.from = meta.from
        pagination.to = meta.to
        pagination.total = meta.total
        pagination.per_page = meta.per_page
        pagination.links = meta.links
      }
      
      // After fetch hook
      afterFetch(response.data)
      
      return response.data
    } catch (error) {
      console.error(`Error fetching ${resourceName}:`, error)
      return null
    } finally {
      loading.value = false
    }
  }
  
  async function createItem(data) {
    loading.value = true
    clearApiErrors()
    
    try {
      // Transform data before submit
      const transformedData = beforeSubmit(data)
      
      // Submit to API
      const response = await axios.post(endpoints.create, transformedData)
      
      // Refresh list
      await fetchItems(pagination.current_page)
      
      // Close modal
      closeCreateModal()
      
      return response.data
    } catch (error) {
      console.error(`Error creating ${resourceName}:`, error)
      handleApiError(error)
      return null
    } finally {
      loading.value = false
    }
  }
  
  async function updateItem(data) {
    if (!selectedItem.value) return null
    
    loading.value = true
    clearApiErrors()
    
    try {
      // Transform data before submit
      const transformedData = beforeSubmit(data)
      
      // Submit to API
      const response = await axios.post(
        endpoints.update(selectedItem.value.id), 
        transformedData
      )
      
      // Refresh list
      await fetchItems(pagination.current_page)
      
      // Close modal
      closeEditModal()
      
      return response.data
    } catch (error) {
      console.error(`Error updating ${resourceName}:`, error)
      handleApiError(error)
      return null
    } finally {
      loading.value = false
    }
  }
  
  async function deleteItem() {
    if (!selectedItem.value) return false
    
    loading.value = true
    
    try {
      await axios.delete(endpoints.delete(selectedItem.value.id))
      
      // Refresh list
      await fetchItems(pagination.current_page)
      
      // Close modal
      closeDeleteModal()
      
      return true
    } catch (error) {
      console.error(`Error deleting ${resourceName}:`, error)
      return false
    } finally {
      loading.value = false
    }
  }
  
  async function deleteSelectedItems() {
    if (!selectedItems.value.length) return false
    
    loading.value = true
    
    try {
      await Promise.all(
        selectedItems.value.map(item => 
          axios.delete(endpoints.delete(item.id))
        )
      )
      
      // Refresh list
      await fetchItems(pagination.current_page)
      
      // Clear selection
      selectedItems.value = []
      
      return true
    } catch (error) {
      console.error(`Error deleting ${resourceName}:`, error)
      return false
    } finally {
      loading.value = false
    }
  }
  
  // Selection handlers
  function toggleSelectAll() {
    if (selectedItems.value.length === items.value.length) {
      selectedItems.value = []
    } else {
      selectedItems.value = [...items.value]
    }
  }
  
  function toggleSelectItem(item) {
    const index = selectedItems.value.findIndex(i => i.id === item.id)
    if (index === -1) {
      selectedItems.value.push(item)
    } else {
      selectedItems.value.splice(index, 1)
    }
  }
  
  // Modal handlers
  function openCreateModal() {
    showCreateModal.value = true
  }
  
  function closeCreateModal() {
    showCreateModal.value = false
    clearApiErrors()
  }
  
  function openEditModal(item) {
    selectedItem.value = item
    showEditModal.value = true
  }
  
  function closeEditModal() {
    showEditModal.value = false
    selectedItem.value = null
    clearApiErrors()
  }
  
  function openDeleteModal(item) {
    selectedItem.value = item
    showDeleteModal.value = true
  }
  
  function closeDeleteModal() {
    showDeleteModal.value = false
    selectedItem.value = null
  }
  
  function openViewModal(item) {
    selectedItem.value = item
    showViewModal.value = true
  }
  
  function closeViewModal() {
    showViewModal.value = false
    selectedItem.value = null
  }
  
  // API error handling
  function handleApiError(error) {
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors
      
      // Process errors
      for (const field in errors) {
        if (Array.isArray(errors[field])) {
          apiErrors[field] = errors[field][0]
        } else {
          apiErrors[field] = errors[field]
        }
      }
    }
  }
  
  function clearApiErrors() {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
  }
  
  // Pagination handler
  function changePage(url) {
    if (!url) return
    
    const urlObj = new URL(url)
    const page = urlObj.searchParams.get('page')
    fetchItems(page)
  }
  
  // Filter handlers
  function applyFilters() {
    fetchItems(1)
  }
  
  function resetFilters() {
    for (const key in filters) {
      if (key !== 'page') {
        filters[key] = ''
      }
    }
    filters.page = 1
    fetchItems(1)
  }
  
  // Initialize
  onMounted(() => {
    fetchItems()
  })
  
  return {
    // State
    items,
    selectedItems,
    selectedItem,
    loading,
    apiErrors,
    pagination,
    filters,
    
    // Modal state
    showCreateModal,
    showEditModal,
    showDeleteModal,
    showViewModal,
    
    // CRUD operations
    fetchItems,
    createItem,
    updateItem,
    deleteItem,
    deleteSelectedItems,
    
    // Selection handlers
    toggleSelectAll,
    toggleSelectItem,
    
    // Modal handlers
    openCreateModal,
    closeCreateModal,
    openEditModal,
    closeEditModal,
    openDeleteModal,
    closeDeleteModal,
    openViewModal,
    closeViewModal,
    
    // Pagination handler
    changePage,
    
    // Filter handlers
    applyFilters,
    resetFilters,
    
    // API error handling
    clearApiErrors
  }
} 