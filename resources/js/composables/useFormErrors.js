import { reactive } from 'vue'

export default function useFormErrors() {
  const errors = reactive({})

  function setErrors(newErrors) {
    Object.keys(errors).forEach(key => delete errors[key])
    if (newErrors) {
      Object.assign(errors, newErrors)
    }
  }

  function clearError(field) {
    delete errors[field]
  }

  function clearAll() {
    Object.keys(errors).forEach(key => delete errors[key])
  }

  return {
    errors,
    setErrors,
    clearError,
    clearAll
  }
} 