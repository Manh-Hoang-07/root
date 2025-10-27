export enum PaymentStatus {
  PENDING = 'pending',
  PAID = 'paid',
  FAILED = 'failed',
  REFUNDED = 'refunded',
  PARTIALLY_REFUNDED = 'partially_refunded',
}

export const PaymentStatusLabels: Record<PaymentStatus, string> = {
  [PaymentStatus.PENDING]: 'Chờ thanh toán',
  [PaymentStatus.PAID]: 'Đã thanh toán',
  [PaymentStatus.FAILED]: 'Thanh toán thất bại',
  [PaymentStatus.REFUNDED]: 'Đã hoàn tiền',
  [PaymentStatus.PARTIALLY_REFUNDED]: 'Hoàn tiền một phần',
};

export const PaymentStatusColors: Record<PaymentStatus, string> = {
  [PaymentStatus.PENDING]: 'warning',
  [PaymentStatus.PAID]: 'success',
  [PaymentStatus.FAILED]: 'danger',
  [PaymentStatus.REFUNDED]: 'info',
  [PaymentStatus.PARTIALLY_REFUNDED]: 'info',
};

export const getPaymentStatusLabel = (status: PaymentStatus): string => {
  return PaymentStatusLabels[status];
};

export const getPaymentStatusColor = (status: PaymentStatus): string => {
  return PaymentStatusColors[status];
};

