export enum OrderStatus {
  PENDING = 'pending',
  CONFIRMED = 'confirmed',
  PROCESSING = 'processing',
  SHIPPED = 'shipped',
  DELIVERED = 'delivered',
  CANCELLED = 'cancelled',
  REFUNDED = 'refunded',
  RETURNED = 'returned',
  FAILED = 'failed',
}

export enum PaymentStatus {
  PENDING = 'pending',
  AUTHORIZED = 'authorized',
  CAPTURED = 'captured',
  PAID = 'paid',
  FAILED = 'failed',
  CANCELLED = 'cancelled',
  REFUNDED = 'refunded',
  PARTIALLY_REFUNDED = 'partially_refunded',
}

export enum PaymentMethod {
  CREDIT_CARD = 'credit_card',
  DEBIT_CARD = 'debit_card',
  PAYPAL = 'paypal',
  BANK_TRANSFER = 'bank_transfer',
  CASH_ON_DELIVERY = 'cash_on_delivery',
  DIGITAL_WALLET = 'digital_wallet',
  CRYPTOCURRENCY = 'cryptocurrency',
}

export enum ShippingStatus {
  NOT_SHIPPED = 'not_shipped',
  PREPARING = 'preparing',
  SHIPPED = 'shipped',
  IN_TRANSIT = 'in_transit',
  OUT_FOR_DELIVERY = 'out_for_delivery',
  DELIVERED = 'delivered',
  FAILED_DELIVERY = 'failed_delivery',
  RETURNED = 'returned',
}

export enum OrderType {
  STANDARD = 'standard',
  EXPRESS = 'express',
  SUBSCRIPTION = 'subscription',
  PRE_ORDER = 'pre_order',
  DIGITAL = 'digital',
}

/**
 * Order status transitions - defines which statuses can transition to which
 */
export const ORDER_STATUS_TRANSITIONS: Record<OrderStatus, OrderStatus[]> = {
  [OrderStatus.PENDING]: [OrderStatus.CONFIRMED, OrderStatus.CANCELLED, OrderStatus.FAILED],
  [OrderStatus.CONFIRMED]: [OrderStatus.PROCESSING, OrderStatus.CANCELLED],
  [OrderStatus.PROCESSING]: [OrderStatus.SHIPPED, OrderStatus.CANCELLED],
  [OrderStatus.SHIPPED]: [OrderStatus.DELIVERED, OrderStatus.RETURNED],
  [OrderStatus.DELIVERED]: [OrderStatus.RETURNED, OrderStatus.REFUNDED],
  [OrderStatus.CANCELLED]: [], // Terminal state
  [OrderStatus.REFUNDED]: [], // Terminal state
  [OrderStatus.RETURNED]: [OrderStatus.REFUNDED],
  [OrderStatus.FAILED]: [OrderStatus.PENDING], // Can retry
};

/**
 * Payment status transitions
 */
export const PAYMENT_STATUS_TRANSITIONS: Record<PaymentStatus, PaymentStatus[]> = {
  [PaymentStatus.PENDING]: [PaymentStatus.AUTHORIZED, PaymentStatus.PAID, PaymentStatus.FAILED, PaymentStatus.CANCELLED],
  [PaymentStatus.AUTHORIZED]: [PaymentStatus.CAPTURED, PaymentStatus.CANCELLED],
  [PaymentStatus.CAPTURED]: [PaymentStatus.PAID, PaymentStatus.REFUNDED],
  [PaymentStatus.PAID]: [PaymentStatus.REFUNDED, PaymentStatus.PARTIALLY_REFUNDED],
  [PaymentStatus.FAILED]: [PaymentStatus.PENDING], // Can retry
  [PaymentStatus.CANCELLED]: [], // Terminal state
  [PaymentStatus.REFUNDED]: [], // Terminal state
  [PaymentStatus.PARTIALLY_REFUNDED]: [PaymentStatus.REFUNDED],
};

/**
 * Check if order status transition is valid
 */
export function canTransitionOrderStatus(from: OrderStatus, to: OrderStatus): boolean {
  return ORDER_STATUS_TRANSITIONS[from]?.includes(to) || false;
}

/**
 * Check if payment status transition is valid
 */
export function canTransitionPaymentStatus(from: PaymentStatus, to: PaymentStatus): boolean {
  return PAYMENT_STATUS_TRANSITIONS[from]?.includes(to) || false;
}

/**
 * Get order status display name
 */
export function getOrderStatusDisplayName(status: OrderStatus): string {
  const displayNames: Record<OrderStatus, string> = {
    [OrderStatus.PENDING]: 'Pending',
    [OrderStatus.CONFIRMED]: 'Confirmed',
    [OrderStatus.PROCESSING]: 'Processing',
    [OrderStatus.SHIPPED]: 'Shipped',
    [OrderStatus.DELIVERED]: 'Delivered',
    [OrderStatus.CANCELLED]: 'Cancelled',
    [OrderStatus.REFUNDED]: 'Refunded',
    [OrderStatus.RETURNED]: 'Returned',
    [OrderStatus.FAILED]: 'Failed',
  };
  
  return displayNames[status] || status;
}

/**
 * Get payment status display name
 */
export function getPaymentStatusDisplayName(status: PaymentStatus): string {
  const displayNames: Record<PaymentStatus, string> = {
    [PaymentStatus.PENDING]: 'Pending',
    [PaymentStatus.AUTHORIZED]: 'Authorized',
    [PaymentStatus.CAPTURED]: 'Captured',
    [PaymentStatus.PAID]: 'Paid',
    [PaymentStatus.FAILED]: 'Failed',
    [PaymentStatus.CANCELLED]: 'Cancelled',
    [PaymentStatus.REFUNDED]: 'Refunded',
    [PaymentStatus.PARTIALLY_REFUNDED]: 'Partially Refunded',
  };
  
  return displayNames[status] || status;
}

/**
 * Get payment method display name
 */
export function getPaymentMethodDisplayName(method: PaymentMethod): string {
  const displayNames: Record<PaymentMethod, string> = {
    [PaymentMethod.CREDIT_CARD]: 'Credit Card',
    [PaymentMethod.DEBIT_CARD]: 'Debit Card',
    [PaymentMethod.PAYPAL]: 'PayPal',
    [PaymentMethod.BANK_TRANSFER]: 'Bank Transfer',
    [PaymentMethod.CASH_ON_DELIVERY]: 'Cash on Delivery',
    [PaymentMethod.DIGITAL_WALLET]: 'Digital Wallet',
    [PaymentMethod.CRYPTOCURRENCY]: 'Cryptocurrency',
  };
  
  return displayNames[method] || method;
}

/**
 * Check if order status is terminal (cannot be changed)
 */
export function isTerminalOrderStatus(status: OrderStatus): boolean {
  return ORDER_STATUS_TRANSITIONS[status].length === 0;
}

/**
 * Check if payment status is terminal (cannot be changed)
 */
export function isTerminalPaymentStatus(status: PaymentStatus): boolean {
  return PAYMENT_STATUS_TRANSITIONS[status].length === 0;
}

/**
 * Get order status color for UI
 */
export function getOrderStatusColor(status: OrderStatus): string {
  const colors: Record<OrderStatus, string> = {
    [OrderStatus.PENDING]: '#fbbf24', // yellow
    [OrderStatus.CONFIRMED]: '#3b82f6', // blue
    [OrderStatus.PROCESSING]: '#f59e0b', // amber
    [OrderStatus.SHIPPED]: '#8b5cf6', // purple
    [OrderStatus.DELIVERED]: '#10b981', // green
    [OrderStatus.CANCELLED]: '#6b7280', // gray
    [OrderStatus.REFUNDED]: '#ef4444', // red
    [OrderStatus.RETURNED]: '#f97316', // orange
    [OrderStatus.FAILED]: '#dc2626', // red
  };
  
  return colors[status] || '#6b7280';
}

/**
 * Get payment status color for UI
 */
export function getPaymentStatusColor(status: PaymentStatus): string {
  const colors: Record<PaymentStatus, string> = {
    [PaymentStatus.PENDING]: '#fbbf24', // yellow
    [PaymentStatus.AUTHORIZED]: '#3b82f6', // blue
    [PaymentStatus.CAPTURED]: '#8b5cf6', // purple
    [PaymentStatus.PAID]: '#10b981', // green
    [PaymentStatus.FAILED]: '#dc2626', // red
    [PaymentStatus.CANCELLED]: '#6b7280', // gray
    [PaymentStatus.REFUNDED]: '#ef4444', // red
    [PaymentStatus.PARTIALLY_REFUNDED]: '#f97316', // orange
  };
  
  return colors[status] || '#6b7280';
}
