<template>
  <div>
    <CategoryForm 
      v-if="showModal"
      :show="showModal"
      :category="category"
      :parent-categories="parentCategories"
      :status-enums="statusEnums"
      :api-errors="apiErrors"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import CategoryForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, watch } from 'vue'
import { useApiFormSubmit } from '@/utils/useApiFormSubmit'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  category: Object,
  onClose: Function
})
const emit = defineEmits(['updated'])

const showModal = ref(false)
const parentCategories = ref([])
const statusEnums = ref([])

const { apiErrors, submit } = useApiFormSubmit({
  endpoint: endpoints.categories.update(props.category?.id),
  emit,
  onClose: props.onClose,
  eventName: 'updated',
  method: 'put'
})

watch(() => props.show, (newValue) => {
  showModal.value = newValue
  if (newValue) {
    fetchParentCategories()
    fetchStatusEnums()
  }
}, { immediate: true })

async function fetchParentCategories() {
  try {
    const response = await axios.get(endpoints.categories.list, {
      params: { per_page: 100 }
    })
    parentCategories.value = response.data.data || []
  } catch (error) {
    parentCategories.value = []
  }
}

async function fetchStatusEnums() {
  try {
    const response = await axios.get(endpoints.enums('BasicStatus'))
    statusEnums.value = Array.isArray(response.data) ? response.data : []
  } catch (error) {
    statusEnums.value = []
  }
}

async function handleSubmit(formData) {
  await submit(formData)
}

function onClose() {
  if (props.onClose) {
    props.onClose()
  }
}
</script> 