<template>
  <div>
    <CategoryForm 
      v-if="showModal"
      :show="showModal"
      :parent-categories="parentCategories"
      :api-errors="apiErrors"
      @submit="handleSubmit" 
      @cancel="onClose" 
    />
  </div>
</template>
<script setup>
import CategoryForm from './form.vue'
import endpoints from '@/api/endpoints'
import { ref, reactive, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  onClose: Function
})
const emit = defineEmits(['created'])

const showModal = ref(false)
const apiErrors = reactive({})
const parentCategories = ref([])

watch(() => props.show, (newValue) => {
  showModal.value = newValue
  if (newValue) fetchParentCategories()
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

async function handleSubmit(formData) {
  try {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    const response = await axios.post(endpoints.categories.create, formData)
    emit('created')
    props.onClose()
  } catch (error) {
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors
      for (const field in errors) {
        if (Array.isArray(errors[field])) {
          apiErrors[field] = errors[field][0]
        } else {
          apiErrors[field] = errors[field]
        }
      }
    }
  }
}

function onClose() {
  if (props.onClose) {
    props.onClose()
  }
}
</script> 