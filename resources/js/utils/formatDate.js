export function formatDate(date, format = 'dd/MM/yyyy') {
  if (!date) return ''
  const d = new Date(date)
  if (format === 'yyyy-MM-dd') {
    return d.toISOString().slice(0, 10)
  }
  // dd/MM/yyyy
  return d.toLocaleDateString('vi-VN')
} 