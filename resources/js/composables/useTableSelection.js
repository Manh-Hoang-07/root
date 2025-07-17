import { ref, computed, watch } from 'vue'

export default function useTableSelection(rows, rowKey = 'id') {
  const selected = ref([])

  const isAllSelected = computed(() =>
    rows.value.length > 0 && selected.value.length === rows.value.length
  )

  function toggleSelectAll() {
    if (isAllSelected.value) {
      selected.value = []
    } else {
      selected.value = rows.value.map(row => row[rowKey])
    }
  }

  function toggleSelect(rowId) {
    if (selected.value.includes(rowId)) {
      selected.value = selected.value.filter(id => id !== rowId)
    } else {
      selected.value.push(rowId)
    }
  }

  // Reset selection if rows change
  watch(rows, () => {
    selected.value = []
  })

  return {
    selected,
    isAllSelected,
    toggleSelectAll,
    toggleSelect
  }
} 