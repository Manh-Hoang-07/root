export default function formToFormData(form, options = {}) {
  const submitData = new FormData()
  for (const key in form) {
    if (options.exclude && options.exclude.includes(key)) continue

    // Nếu là boolean true cho remove_xxx, append 1
    if (key.startsWith('remove_') && form[key]) {
      submitData.append(key, 1)
      continue
    }
    // Nếu là chuỗi rỗng, có thể bỏ qua (tùy chọn)
    if (form[key] === '' && options.skipEmpty) continue

    // Mặc định: append cả string (đường dẫn) hoặc file object
    submitData.append(key, form[key])
  }
  return submitData
} 