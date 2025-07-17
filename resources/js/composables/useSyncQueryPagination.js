import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export default function useSyncQueryPagination(filters, pagination, fetchData, filterKeys = ['search']) {
  const route = useRoute()
  const router = useRouter()

  // Đọc query khi vào trang
  onMounted(() => {
    if (route.query.page) {
      pagination.value.currentPage = parseInt(route.query.page)
    }
    filterKeys.forEach(key => {
      if (route.query[key]) filters.value[key] = route.query[key]
    })
    fetchData()
  })

  // Cập nhật URL khi đổi trang hoặc filter
  function updateUrl() {
    const query = { ...route.query }
    query.page = pagination.value.currentPage !== 1 ? pagination.value.currentPage : undefined
    filterKeys.forEach(key => {
      query[key] = filters.value[key] || undefined
    })
    router.replace({ query })
  }

  function onPageChange(page) {
    if (page !== pagination.value.currentPage) {
      pagination.value.currentPage = page
      updateUrl()
      fetchData()
    }
  }

  function onUpdateFilters(newFilters) {
    filters.value = newFilters
    pagination.value.currentPage = 1
    updateUrl()
    fetchData()
  }

  return { onPageChange, onUpdateFilters }
} 