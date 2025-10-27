export enum ProductStatus {
  ACTIVE = 'active',
  INACTIVE = 'inactive',
  DRAFT = 'draft',
}

export const ProductStatusLabels: Record<ProductStatus, string> = {
  [ProductStatus.ACTIVE]: 'Hoạt động',
  [ProductStatus.INACTIVE]: 'Không hoạt động',
  [ProductStatus.DRAFT]: 'Bản nháp',
};

export const ProductStatusColors: Record<ProductStatus, string> = {
  [ProductStatus.ACTIVE]: 'success',
  [ProductStatus.INACTIVE]: 'danger',
  [ProductStatus.DRAFT]: 'warning',
};

export const getProductStatusLabel = (status: ProductStatus): string => {
  return ProductStatusLabels[status];
};

export const getProductStatusColor = (status: ProductStatus): string => {
  return ProductStatusColors[status];
};

