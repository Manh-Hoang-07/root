export enum ContactStatus {
  PENDING = 'pending',
  IN_PROGRESS = 'in_progress',
  COMPLETED = 'completed',
  CANCELLED = 'cancelled',
}

export const ContactStatusLabels: Record<ContactStatus, string> = {
  [ContactStatus.PENDING]: 'Chờ xử lý',
  [ContactStatus.IN_PROGRESS]: 'Đang xử lý',
  [ContactStatus.COMPLETED]: 'Hoàn thành',
  [ContactStatus.CANCELLED]: 'Đã hủy',
};

export const ContactStatusColors: Record<ContactStatus, string> = {
  [ContactStatus.PENDING]: 'warning',
  [ContactStatus.IN_PROGRESS]: 'info',
  [ContactStatus.COMPLETED]: 'success',
  [ContactStatus.CANCELLED]: 'danger',
};

export const getContactStatusLabel = (status: ContactStatus): string => {
  return ContactStatusLabels[status];
};

export const getContactStatusColor = (status: ContactStatus): string => {
  return ContactStatusColors[status];
};

export const isActive = (status: ContactStatus): boolean => {
  return [ContactStatus.PENDING, ContactStatus.IN_PROGRESS].includes(status);
};

