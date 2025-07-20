import { ref } from 'vue'

/**
 * Composable để xử lý form data một cách nhất quán
 * Hỗ trợ cả FormData và JSON
 */
export default function useFormData() {
  const form = ref({})
  
  /**
   * Tạo data để gửi API
   * @param {Object} formData - Dữ liệu form
   * @param {boolean} useFormData - Có sử dụng FormData không (mặc định false - dùng JSON)
   * @returns {Object|FormData} - Data đã được xử lý
   */
  const prepareData = (formData, useFormData = false) => {
    if (useFormData) {
      const data = new FormData()
      Object.entries(formData).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
          data.append(key, value)
        }
      })
      return data
    } else {
      // Tạo JSON object
      const data = {}
      Object.entries(formData).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
          data[key] = value
        }
      })
      return data
    }
  }
  
  /**
   * Reset form về giá trị mặc định
   * @param {Object} defaultValues - Giá trị mặc định
   */
  const resetForm = (defaultValues = {}) => {
    form.value = { ...defaultValues }
  }
  
  /**
   * Cập nhật form với dữ liệu từ API
   * @param {Object} data - Dữ liệu từ API
   */
  const setFormData = (data) => {
    if (data) {
      form.value = { ...data }
    }
  }
  
  return {
    form,
    prepareData,
    resetForm,
    setFormData
  }
} 