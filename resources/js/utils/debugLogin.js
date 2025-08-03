// Debug utility cho trang Login
export function debugLogin() {
  console.log('=== LOGIN DEBUG ===')
  
  // Kiểm tra Vue availability
  if (typeof window !== 'undefined' && window.Vue) {
    console.log('✅ Vue is available globally')
  } else {
    console.log('ℹ️ Vue not available globally (normal in Vue 3)')
  }
  
  // Kiểm tra Vue Router availability
  if (typeof window !== 'undefined' && window.$router) {
    console.log('✅ Router is available globally')
  } else {
    console.log('ℹ️ Router not available globally (normal)')
  }
  
  // Test basic JavaScript functionality
  try {
    const testObj = { test: 'value' }
    console.log('✅ Basic object creation OK:', testObj)
  } catch (error) {
    console.error('❌ Basic object creation failed:', error)
  }
  
  // Test reactive-like behavior
  try {
    const testReactive = {
      email: '',
      password: '',
      remember: false
    }
    console.log('✅ Test reactive object creation OK:', testReactive)
  } catch (error) {
    console.error('❌ Test reactive object creation failed:', error)
  }
  
  // Test validation logic
  try {
    const testForm = {
      email: '',
      password: ''
    }
    const testRules = {
      email: [{ required: 'Email required' }],
      password: [{ required: 'Password required' }]
    }
    
    // Simple validation test
    const errors = {}
    for (const field in testRules) {
      const value = testForm[field] || ''
      if (!value.trim()) {
        errors[field] = testRules[field][0].required
      }
    }
    
    console.log('✅ Validation logic test OK:', errors)
  } catch (error) {
    console.error('❌ Validation logic test failed:', error)
  }
}

// Function để test form field events
export function testFormFieldEvent() {
  console.log('=== FORM FIELD EVENT TEST ===')
  
  // Simulate input event
  const mockEvent = {
    target: {
      value: 'test@example.com'
    }
  }
  
  console.log('Mock event:', mockEvent)
  console.log('Event target:', mockEvent.target)
  console.log('Event target value:', mockEvent.target?.value)
  
  return mockEvent
}

// Function để check form data structure
export function checkFormStructure(form) {
  console.log('=== FORM STRUCTURE CHECK ===')
  
  if (!form) {
    console.error('❌ Form is null or undefined')
    return false
  }
  
  console.log('Form object:', form)
  console.log('Form type:', typeof form)
  console.log('Form keys:', Object.keys(form))
  
  const requiredFields = ['email', 'password', 'remember']
  const missingFields = requiredFields.filter(field => !(field in form))
  
  if (missingFields.length > 0) {
    console.error('❌ Missing fields:', missingFields)
    return false
  }
  
  console.log('✅ All required fields present')
  
  // Check field values
  console.log('Email value:', form.email)
  console.log('Password value:', form.password)
  console.log('Remember value:', form.remember)
  
  return true
}

// Function để test form field update
export function testFormFieldUpdate(form, field, value) {
  console.log('=== FORM FIELD UPDATE TEST ===')
  console.log('Updating field:', field, 'with value:', value)
  
  if (!form) {
    console.error('❌ Form is null or undefined')
    return false
  }
  
  if (!(field in form)) {
    console.error('❌ Field not found in form:', field)
    return false
  }
  
  try {
    form[field] = value
    console.log('✅ Field updated successfully')
    console.log('New form state:', form)
    return true
  } catch (error) {
    console.error('❌ Field update failed:', error)
    return false
  }
} 

// Function để test form data persistence
export function testFormDataPersistence(form) {
  console.log('=== FORM DATA PERSISTENCE TEST ===')
  
  if (!form) {
    console.error('❌ Form is null or undefined')
    return false
  }
  
  // Test 1: Set values
  console.log('Test 1: Setting form values...')
  form.email = 'test@example.com'
  form.password = 'password123'
  form.remember = true
  
  console.log('Form after setting values:', { ...form })
  
  // Test 2: Check if values persist
  setTimeout(() => {
    console.log('Test 2: Checking if values persist after 1 second...')
    console.log('Form values:', { ...form })
    
    if (form.email === 'test@example.com' && form.password === 'password123' && form.remember === true) {
      console.log('✅ Form data persistence test PASSED')
    } else {
      console.log('❌ Form data persistence test FAILED')
    }
  }, 1000)
  
  return true
}

// Function để test validation without clearing form
export function testValidationWithoutClearing(form, rules) {
  console.log('=== VALIDATION WITHOUT CLEARING TEST ===')
  
  if (!form) {
    console.error('❌ Form is null or undefined')
    return false
  }
  
  // Set test data
  form.email = 'test@example.com'
  form.password = 'password123'
  form.remember = true
  
  console.log('Form before validation:', { ...form })
  
  // Simulate validation (without clearing form)
  const errors = {}
  
  for (const field in rules) {
    const fieldValue = form && form[field] !== undefined && form[field] !== null ? form[field] : ''
    let value = fieldValue.toString()
    
    if (field !== 'password') {
      value = value.trim()
    }
    
    console.log(`Validating ${field}: "${value}"`)
    
    // Simple required validation
    if (!value) {
      errors[field] = `${field} is required`
    }
  }
  
  console.log('Validation errors:', errors)
  console.log('Form after validation:', { ...form })
  
  if (Object.keys(errors).length === 0) {
    console.log('✅ Validation test PASSED - no errors')
  } else {
    console.log('❌ Validation test FAILED - has errors')
  }
  
  return Object.keys(errors).length === 0
}

// Function để test form input mà không ghi đè dữ liệu
export function testFormInput(form) {
  console.log('=== FORM INPUT TEST ===')
  
  if (!form) {
    console.error('❌ Form is null or undefined')
    return false
  }
  
  console.log('Current form state:', { ...form })
  
  // Test 1: Kiểm tra form có reactive không
  const originalEmail = form.email
  const originalPassword = form.password
  const originalRemember = form.remember
  
  console.log('Original values:', {
    email: originalEmail,
    password: originalPassword,
    remember: originalRemember
  })
  
  // Test 2: Thay đổi giá trị và kiểm tra
  form.email = 'new@example.com'
  form.password = 'newpassword'
  form.remember = true
  
  console.log('After change:', { ...form })
  
  // Test 3: Khôi phục giá trị gốc
  form.email = originalEmail
  form.password = originalPassword
  form.remember = originalRemember
  
  console.log('After restore:', { ...form })
  
  return true
}

// Function để monitor form changes without interference
export function monitorFormChanges(form) {
  console.log('=== FORM CHANGE MONITOR ===')
  
  if (!form) {
    console.error('❌ Form is null or undefined')
    return
  }
  
  // Monitor email changes
  if (form.email !== undefined) {
    console.log('Initial email value:', form.email)
  }
  
  // Monitor password changes
  if (form.password !== undefined) {
    console.log('Initial password value:', form.password)
  }
  
  // Monitor remember changes
  if (form.remember !== undefined) {
    console.log('Initial remember value:', form.remember)
  }
  
  // Set up periodic monitoring (every 5 seconds)
  const interval = setInterval(() => {
    console.log('Form state check:', {
      email: form.email,
      password: form.password,
      remember: form.remember
    })
  }, 5000)
  
  // Return function to stop monitoring
  return () => {
    clearInterval(interval)
    console.log('Form monitoring stopped')
  }
} 