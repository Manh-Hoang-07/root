import { reactive } from 'vue'
import axios from 'axios'
import formToFormData from './formToFormData'

export function useApiFormSubmit({ endpoint, emit, onClose, eventName = 'created', method = 'post' }) {
  const apiErrors = reactive({})

  async function submit(form) {
    try {
      // Xóa lỗi cũ
      Object.keys(apiErrors).forEach(key => delete apiErrors[key])

      // Luôn tạo mới FormData và append _method nếu là update
      const dataToSend = (method === 'put' || method === 'patch')
        ? formToFormData(form, { method })
        : formToFormData(form)

      const response = await axios.post(endpoint, dataToSend)
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