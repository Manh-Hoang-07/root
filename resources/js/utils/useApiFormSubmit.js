import { reactive } from 'vue'
import axios from 'axios'

export function useApiFormSubmit({ endpoint, emit, onClose, eventName = 'created', method = 'post' }) {
  const apiErrors = reactive({})

  async function submit(formData) {
    try {
      // Xóa lỗi cũ
      Object.keys(apiErrors).forEach(key => delete apiErrors[key])

      let response
      if (method === 'put' || method === 'patch') {
        response = await axios[method](endpoint, formData)
      } else {
        response = await axios.post(endpoint, formData)
      }
      emit(eventName)
      if (onClose) onClose()
      return response
    } catch (error) {
      if (error.response?.status === 422 && error.response?.data?.errors) {
        const errors = error.response.data.errors
        for (const field in errors) {
          apiErrors[field] = Array.isArray(errors[field]) ? errors[field][0] : errors[field]
        }
      }
      throw error
    }
  }

  return { apiErrors, submit }
} 