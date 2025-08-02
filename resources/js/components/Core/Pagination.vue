<template>
  <nav class="flex flex-wrap items-center justify-center gap-2 py-3 select-none" aria-label="Pagination">
    <button
      class="px-3 py-1 rounded-lg bg-gray-100 hover:bg-indigo-100 text-gray-600 font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
      :disabled="currentPage === 1 || loading"
      @click="$emit('page-change', 1)"
      title="Trang đầu"
    >
      <ChevronDoubleLeftIcon class="w-4 h-4" />
    </button>
    <button
      class="px-3 py-1 rounded-lg bg-gray-100 hover:bg-indigo-100 text-gray-600 font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
      :disabled="currentPage === 1 || loading"
      @click="$emit('page-change', currentPage - 1)"
      title="Trang trước"
    >
      <ChevronLeftIcon class="w-4 h-4" />
    </button>

    <span v-if="totalPages <= 7">
      <button
        v-for="page in totalPages"
        :key="page"
        class="mx-0.5 px-3 py-1 rounded-lg font-semibold transition"
        :class="page === currentPage ? 'bg-indigo-600 text-white shadow' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700'"
        :disabled="loading"
        @click="$emit('page-change', page)"
      >
        {{ page }}
      </button>
    </span>
    <span v-else>
      <button
        v-for="page in visiblePages"
        :key="page"
        class="mx-0.5 px-3 py-1 rounded-lg font-semibold transition"
        :class="page === currentPage ? 'bg-indigo-600 text-white shadow' : 'bg-gray-100 hover:bg-indigo-100 text-gray-700'"
        :disabled="loading || page === '...'"
        @click="$emit('page-change', page)"
      >
        {{ page }}
      </button>
    </span>

    <button
      class="px-3 py-1 rounded-lg bg-gray-100 hover:bg-indigo-100 text-gray-600 font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
      :disabled="currentPage === totalPages || loading"
      @click="$emit('page-change', currentPage + 1)"
      title="Trang sau"
    >
      <ChevronRightIcon class="w-4 h-4" />
    </button>
    <button
      class="px-3 py-1 rounded-lg bg-gray-100 hover:bg-indigo-100 text-gray-600 font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
      :disabled="currentPage === totalPages || loading"
      @click="$emit('page-change', totalPages)"
      title="Trang cuối"
    >
      <ChevronDoubleRightIcon class="w-4 h-4" />
    </button>

    <div class="flex items-center gap-1 ml-4 text-sm text-gray-500">
      <span>Trang</span>
      <input
        type="number"
        min="1"
        :max="totalPages"
        v-model.number="inputPage"
        @keyup.enter="jumpToPage"
        class="w-12 px-2 py-1 border rounded focus:outline-none focus:ring focus:border-indigo-400 text-center"
        :disabled="loading"
      />
      <span>/ {{ totalPages }}</span>
    </div>
    <div class="ml-4 text-sm text-gray-500 hidden sm:block">
      Tổng: <span class="font-semibold text-indigo-600">{{ totalItems }}</span> bản ghi
    </div>
  </nav>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { ChevronLeftIcon, ChevronRightIcon, ChevronDoubleLeftIcon, ChevronDoubleRightIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  currentPage: { type: Number, default: 1 },
  totalPages: { type: Number, default: 1 },
  pageSize: { type: Number, default: 10 },
  totalItems: { type: Number, default: 0 },
  loading: { type: Boolean, default: false }
})
const emit = defineEmits(['page-change'])

const inputPage = ref(props.currentPage)
watch(() => props.currentPage, (val) => {
  inputPage.value = val
})

function jumpToPage() {
  let page = inputPage.value
  if (page < 1) page = 1
  if (page > props.totalPages) page = props.totalPages
  emit('page-change', page)
}

const visiblePages = computed(() => {
  const pages = []
  if (props.totalPages <= 7) {
    for (let i = 1; i <= props.totalPages; i++) pages.push(i)
    return pages
  }
  if (props.currentPage <= 4) {
    return [1,2,3,4,5,'...',props.totalPages]
  }
  if (props.currentPage >= props.totalPages - 3) {
    return [1,'...',props.totalPages-4,props.totalPages-3,props.totalPages-2,props.totalPages-1,props.totalPages]
  }
  return [1,'...',props.currentPage-1,props.currentPage,props.currentPage+1,'...',props.totalPages]
})
</script>

<style scoped>
nav {
  user-select: none;
}
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type="number"] {
  -moz-appearance: textfield;
}
</style> 
