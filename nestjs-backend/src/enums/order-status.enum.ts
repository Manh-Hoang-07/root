export enum OrderStatus {
  PENDING = 'pending',
  CONFIRMED = 'confirmed',
  PROCESSING = 'processing',
  SHIPPED = 'shipped',
  DELIVERED = 'delivered',
  CANCELLED = 'cancelled',
}

export const OrderStatusLabels: Record<OrderStatus, string> = {
  [OrderStatus.PENDING]: 'Chờ xử lý',
  [OrderStatus.CONFIRMED]: 'Đã xác nhận',
  [OrderStatus.PROCESSING]: 'Đang xử lý',
  [OrderStatus.SHIPPED]: 'Đã giao hàng',
  [OrderStatus.DELIVERED]: 'Đã nhận hàng',
  [OrderStatus.CANCELLED]: 'Đã hủy',
};

export const OrderStatusColors: Record<OrderStatus, string> = {
  [OrderStatus.PENDING]: 'warning',
  [OrderStatus.CONFIRMED]: 'info',
  [OrderStatus.PROCESSING]: 'primary',
  [OrderStatus.SHIPPED]: 'success',
  [OrderStatus.DELIVERED]: 'success',
  [OrderStatus.CANCELLED]: 'danger',
};

export const getOrderStatusLabel = (status: OrderStatus): string => {
  return OrderStatusLabels[status];
};

export const getOrderStatusColor = (status: OrderStatus): string => {
  return OrderStatusColors[status];
};

