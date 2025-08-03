// Static enums - được hardcode để tối ưu tốc độ
export const ENUMS = {
  user_status: [
    { id: 'active', name: 'active', value: 'active', label: 'Hoạt động' },
    { id: 'inactive', name: 'inactive', value: 'inactive', label: 'Không hoạt động' },
    { id: 'pending', name: 'pending', value: 'pending', label: 'Chờ xác nhận' },
    { id: 'banned', name: 'banned', value: 'banned', label: 'Bị cấm' }
  ],
  gender: [
    { id: 'male', name: 'male', value: 'male', label: 'Nam' },
    { id: 'female', name: 'female', value: 'female', label: 'Nữ' },
    { id: 'other', name: 'other', value: 'other', label: 'Khác' }
  ],
  basic_status: [
    { id: 'active', name: 'active', value: 'active', label: 'Hoạt động' },
    { id: 'inactive', name: 'inactive', value: 'inactive', label: 'Không hoạt động' }
  ],
  role_status: [
    { id: 'active', name: 'active', value: 'active', label: 'Hoạt động' },
    { id: 'inactive', name: 'inactive', value: 'inactive', label: 'Không hoạt động' }
  ],
  product_status: [
    { id: 'active', name: 'active', value: 'active', label: 'Đang bán' },
    { id: 'inactive', name: 'inactive', value: 'inactive', label: 'Ngừng bán' },
    { id: 'draft', name: 'draft', value: 'draft', label: 'Bản nháp' },
    { id: 'outofstock', name: 'outofstock', value: 'outofstock', label: 'Hết hàng' }
  ],
  order_status: [
    { id: 'pending', name: 'pending', value: 'pending', label: 'Chờ xử lý' },
    { id: 'processing', name: 'processing', value: 'processing', label: 'Đang xử lý' },
    { id: 'completed', name: 'completed', value: 'completed', label: 'Hoàn thành' },
    { id: 'cancelled', name: 'cancelled', value: 'cancelled', label: 'Đã huỷ' },
    { id: 'refunded', name: 'refunded', value: 'refunded', label: 'Đã hoàn tiền' }
  ],
  variant_status: [
    { id: 'active', name: 'active', value: 'active', label: 'Hoạt động' },
    { id: 'inactive', name: 'inactive', value: 'inactive', label: 'Không hoạt động' },
    { id: 'outofstock', name: 'outofstock', value: 'outofstock', label: 'Hết hàng' }
  ],
  stock_status: [
    { id: 'in_stock', name: 'in_stock', value: 'in_stock', label: 'Còn hàng' },
    { id: 'low_stock', name: 'low_stock', value: 'low_stock', label: 'Sắp hết' },
    { id: 'out_of_stock', name: 'out_of_stock', value: 'out_of_stock', label: 'Hết hàng' }
  ],
  expiry_status: [
    { id: 'valid', name: 'valid', value: 'valid', label: 'Còn hạn' },
    { id: 'expiring_soon', name: 'expiring_soon', value: 'expiring_soon', label: 'Sắp hết hạn' },
    { id: 'expired', name: 'expired', value: 'expired', label: 'Đã hết hạn' }
  ]
}

// Helper functions
export const getEnum = (type) => ENUMS[type] || []
export const getEnumSync = (type) => ENUMS[type] || []

export const getEnumLabel = (type, value) => {
  const enumData = ENUMS[type]
  if (!enumData) return ''
  
  const item = enumData.find(item => item.value === value)
  return item ? item.label : ''
}

export const getEnumName = (type, value) => {
  const enumData = ENUMS[type]
  if (!enumData) return ''
  
  const item = enumData.find(item => item.value === value)
  return item ? item.name : ''
}

export const getEnumById = (type, id) => {
  const enumData = ENUMS[type]
  if (!enumData) return null
  
  return enumData.find(item => item.id === id) || null
}

export const getEnumByValue = (type, value) => {
  const enumData = ENUMS[type]
  if (!enumData) return null
  
  return enumData.find(item => item.value === value) || null
} 