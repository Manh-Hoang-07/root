export enum ShippingStatus {
  PENDING = 'pending',
  PREPARING = 'preparing',
  SHIPPED = 'shipped',
  DELIVERED = 'delivered',
  RETURNED = 'returned',
}

export const ShippingStatusLabels: Record<ShippingStatus, string> = {
  [ShippingStatus.PENDING]: 'Chờ vận chuyển',
  [ShippingStatus.PREPARING]: 'Đang chuẩn bị',
  [ShippingStatus.SHIPPED]: 'Đã giao hàng',
  [ShippingStatus.DELIVERED]: 'Đã nhận hàng',
  [ShippingStatus.RETURNED]: 'Đã trả hàng',
};

export const ShippingStatusColors: Record<ShippingStatus, string> = {
  [ShippingStatus.PENDING]: 'warning',
  [ShippingStatus.PREPARING]: 'info',
  [ShippingStatus.SHIPPED]: 'primary',
  [ShippingStatus.DELIVERED]: 'success',
  [ShippingStatus.RETURNED]: 'danger',
};

export const getShippingStatusLabel = (status: ShippingStatus): string => {
  return ShippingStatusLabels[status];
};

export const getShippingStatusColor = (status: ShippingStatus): string => {
  return ShippingStatusColors[status];
};

