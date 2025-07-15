// File cấu hình endpoint API cho admin (quản lý tài khoản)
export default {
  users: '/api/admin/users', // GET (list), POST (create)
  user: id => `/api/admin/users/${id}`, // GET (detail), PUT/PATCH (update), DELETE (delete)
  toggleUserStatus: id => `/api/admin/users/toggle-status/${id}`, // PATCH
  statuses: '/api/admin/users/statuses', // GET - lấy danh sách status enum
  enums: type => `/api/enums/${type}`,
}; 