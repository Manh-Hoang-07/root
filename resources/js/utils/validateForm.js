export default function validateForm(form, rules) {
  const newErrors = {}

  for (const field in rules) {
    // Đảm bảo form[field] không undefined và convert sang string an toàn
    const fieldValue = form && form[field] !== undefined && form[field] !== null ? form[field] : ''
    
    // Xử lý khác nhau cho từng loại field
    let value
    if (field === 'password') {
      // Password không nên trim
      value = fieldValue.toString()
    } else {
      // Các field khác có thể trim
      value = fieldValue.toString().trim()
    }
    
    for (const ruleObj of rules[field]) {
      if (typeof ruleObj === 'string') {
        // Dạng ngắn, không custom message
        if (ruleObj === 'required' && !value) {
          newErrors[field] = 'Trường này là bắt buộc.'
          break
        }
        if (ruleObj === 'email' && value && !/^\S+@\S+\.\S+$/.test(value)) {
          newErrors[field] = 'Email không hợp lệ.'
          break
        }
      } else if (typeof ruleObj === 'object') {
        // Dạng object: { rule: message } hoặc { rule: [param, message] }
        for (const rule in ruleObj) {
          const msg = ruleObj[rule]
          if (rule === 'required' && !value) {
            newErrors[field] = msg
            break
          }
          if (rule === 'email' && value && !/^\S+@\S+\.\S+$/.test(value)) {
            newErrors[field] = msg
            break
          }
          if (rule === 'min' && value) {
            const [min, minMsg] = Array.isArray(msg) ? msg : [msg, `Tối thiểu ${msg} ký tự.`]
            if (value.length < min) {
              newErrors[field] = minMsg
              break
            }
          }
          if (rule === 'match' && value) {
            const [otherField, matchMsg] = Array.isArray(msg) ? msg : [msg, 'Giá trị xác nhận không khớp.']
            const otherValue = form && form[otherField] !== undefined && form[otherField] !== null ? form[otherField] : ''
            const otherValueStr = otherField === 'password' ? otherValue.toString() : otherValue.toString().trim()
            if (value !== otherValueStr) {
              newErrors[field] = matchMsg
              break
            }
          }
          // Bổ sung xử lý pattern
          if (rule === 'pattern' && value) {
            const [regex, patternMsg] = Array.isArray(msg) ? msg : [msg, 'Định dạng không hợp lệ.']
            if (!regex.test(value)) {
              newErrors[field] = patternMsg
              break
            }
          }
        }
      }
    }
  }

  return newErrors
} 