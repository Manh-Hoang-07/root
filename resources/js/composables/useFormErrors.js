import { reactive, ref } from 'vue'

/**
 * Composable để quản lý lỗi form
 * Cung cấp các phương thức để thiết lập, xóa và quản lý lỗi
 * Hỗ trợ cả lỗi từ validation phía client và API
 */
export default function useFormErrors() {
  // Sử dụng reactive để lưu trữ lỗi
  const errors = reactive({})

  /**
   * Thiết lập lỗi mới
   * @param {Object} newErrors - Object chứa lỗi mới
   * @param {Boolean} append - Nếu true, sẽ thêm vào lỗi hiện tại thay vì thay thế
   */
  function setErrors(newErrors, append = false) {
    // Nếu không phải append, xóa lỗi cũ
    if (!append) {
      clearAll()
    }
    
    if (newErrors && typeof newErrors === 'object') {
      // Hỗ trợ cả format API trả về dạng mảng và dạng chuỗi
      for (const key in newErrors) {
        const error = newErrors[key]
        if (Array.isArray(error)) {
          errors[key] = error[0]  // Lấy lỗi đầu tiên nếu là mảng
        } else {
          errors[key] = error  // Sử dụng nguyên nếu là chuỗi
        }
      }
    }
  }

  /**
   * Thiết lập lỗi từ API
   * @param {Object} apiErrors - Object chứa lỗi từ API
   */
  function setServerErrors(apiErrors) {
    setErrors(apiErrors)
  }

  /**
   * Xóa lỗi của một trường cụ thể
   * @param {String} field - Tên trường cần xóa lỗi
   */
  function clearError(field) {
    if (field in errors) {
      delete errors[field]
    }
  }

  /**
   * Xóa tất cả lỗi
   */
  function clearAll() {
    Object.keys(errors).forEach(key => delete errors[key])
  }
  
  /**
   * Kiểm tra xem có lỗi nào không
   * @returns {Boolean} - true nếu có lỗi, false nếu không
   */
  function hasErrors() {
    return Object.keys(errors).length > 0
  }
  
  /**
   * Thiết lập lỗi cho một trường cụ thể
   * @param {String} field - Tên trường
   * @param {String} message - Thông báo lỗi
   */
  function setError(field, message) {
    errors[field] = message
  }

  return {
    errors,
    setErrors,
    setServerErrors,
    clearError,
    clearAll,
    hasErrors,
    setError
  }
} 