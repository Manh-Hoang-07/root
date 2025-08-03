// Hàm tạo endpoints động cho resource
function createResourceEndpoints(resource, custom = {}) {
  const base = `/api/admin/${resource}`;
  const endpoints = {
    list: base,
    create: base,
    show: id => `${base}/${id}`,
    update: id => `${base}/${id}`,
    delete: id => `${base}/${id}`,
    changeStatus: id => `${base}/toggle-status/${id}`,
    ...custom, // cho phép override hoặc thêm endpoint mới
  };

  // Nếu là users thì thêm changePassword và assignRoles
  if (resource === 'users') {
    endpoints.changePassword = id => `${base}/${id}/change-password`;
    endpoints.showWithRoles = id => `${base}/${id}/with-roles`;
    endpoints.assignRoles = id => `${base}/${id}/assign-roles`;
  }

  return endpoints;
}

// Khai báo endpoints cho từng resource
const endpoints = {
  users: createResourceEndpoints('users'),
  products: createResourceEndpoints('products', {
    getForSelect: '/api/admin/products/select/list'
  }),
  orders: createResourceEndpoints('orders'),
  warehouses: createResourceEndpoints('warehouses', {
    customEndpoint: id => `/api/admin/warehouses/${id}/custom-action`
  }),
  categories: createResourceEndpoints('categories', {
    search: '/api/admin/categories/search'
  }),
  brands: createResourceEndpoints('brands', {
    search: '/api/admin/brands/search'
  }),
  attributes: createResourceEndpoints('attributes'),
  attributeValues: createResourceEndpoints('attribute-values'), // Thêm dòng này
  permissions: createResourceEndpoints('permissions'),
  roles: createResourceEndpoints('roles'),
  inventory: createResourceEndpoints('inventory', {
    import: '/api/admin/inventory/import',
    export: '/api/admin/inventory/export',
    expiringSoon: '/api/admin/inventory/expiring-soon',
    expired: '/api/admin/inventory/expired',
    lowStock: '/api/admin/inventory/low-stock',
    filterOptions: '/api/admin/inventory/filter-options'
  }),
  enums: type => `/api/enums/${type}`,
  // Thêm resource khác nếu cần
  // Thêm resource shipping
  shippingApi: createResourceEndpoints('shipping/api'),
  shippingServices: createResourceEndpoints('shipping/services', {
    changeStatus: id => `/api/admin/shipping/services/${id}/toggle-status`
  }),
  shippingZones: createResourceEndpoints('shipping/zones'),
  shippingPricing: createResourceEndpoints('shipping/pricing'),
  shippingPromotions: createResourceEndpoints('shipping/promotions'),
  shippingDelivery: createResourceEndpoints('shipping/delivery'),
  shippingAdvanced: createResourceEndpoints('shipping/advanced'),
};

export default endpoints; 