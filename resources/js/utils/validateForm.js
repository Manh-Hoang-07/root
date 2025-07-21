export default function validateForm(form, rules) {
  const newErrors = {}

  for (const field in rules) {
    const value = (form[field] ?? '').toString().trim()
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
            if (value !== (form[otherField] ?? '').toString().trim()) {
              newErrors[field] = matchMsg
              break
            }
          }
        }
      }
    }
    // if (newErrors[field]) break // chỉ báo lỗi đầu tiên (xoá dòng này để báo tất cả lỗi)
  }

  return newErrors
} 