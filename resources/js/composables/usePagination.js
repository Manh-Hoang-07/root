import { ref } from 'vue'

export default function usePagination(init = { current: 1, perPage: 10, total: 0 }) {
  const currentPage = ref(init.current)
  const perPage = ref(init.perPage)
  const total = ref(init.total)

  function setPage(page) {
    currentPage.value = page
  }

  function setTotal(val) {
    total.value = val
  }

  return {
    currentPage,
    perPage,
    total,
    setPage,
    setTotal
  }
} 