import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

// Import components
import Home from '../views/home/Home/index.vue';
import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import AdminLayout from '../layouts/AdminLayout.vue';
import AdminDashboard from '../views/admin/Dashboard/index.vue';
import AdminProducts from '../views/admin/Products/index.vue';
import AdminPosts from '../views/admin/Posts/index.vue';
import AdminOrders from '../views/admin/Orders/index.vue';
import AdminUsers from '../views/admin/Users/index.vue';
import AdminPermissions from '../views/admin/Permissions/index.vue';
import AdminWarehouses from '../views/admin/Warehouses/index.vue';
import AdminShipping from '../views/admin/Shipping/index.vue';
import AdminReports from '../views/admin/Reports/index.vue';
import AdminSettings from '../views/admin/Settings/index.vue';

// User routes
import UserDashboard from '../views/user/Dashboard/index.vue';
import UserProfile from '../views/user/Profile/index.vue';
import UserOrders from '../views/user/Orders/index.vue';

const routes = [
  // Public routes
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },

  // Admin routes (dùng layout riêng)
  {
    path: '/admin',
    component: AdminLayout,
    children: [
      {
        path: '',
        name: 'AdminDashboard',
        component: AdminDashboard
      },
      {
        path: 'products',
        name: 'AdminProducts',
        component: AdminProducts
      },
      {
        path: 'posts',
        name: 'AdminPosts',
        component: AdminPosts
      },
      {
        path: 'orders',
        name: 'AdminOrders',
        component: AdminOrders
      },
      {
        path: 'users',
        name: 'AdminUsers',
        component: AdminUsers
      },
      {
        path: 'permissions',
        name: 'AdminPermissions',
        component: AdminPermissions
      },
      {
        path: 'warehouses',
        name: 'AdminWarehouses',
        component: AdminWarehouses
      },
      {
        path: 'shipping',
        name: 'AdminShipping',
        component: AdminShipping
      },
      {
        path: 'reports',
        name: 'AdminReports',
        component: AdminReports
      },
      {
        path: 'settings',
        name: 'AdminSettings',
        component: AdminSettings
      }
    ]
  },

  // User routes
  {
    path: '/user',
    name: 'UserDashboard',
    component: UserDashboard,
    meta: { requiresAuth: true, role: 'user' }
  },
  {
    path: '/user/profile',
    name: 'UserProfile',
    component: UserProfile,
    meta: { requiresAuth: true, role: 'user' }
  },
  {
    path: '/user/orders',
    name: 'UserOrders',
    component: UserOrders,
    meta: { requiresAuth: true, role: 'user' }
  },

  // Legacy route (redirect to admin)
  {
    path: '/users',
    redirect: '/admin/users'
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  
  // Check if route requires authentication (chỉ áp dụng cho user)
  if (to.meta.requiresAuth && to.meta.role === 'user' && !authStore.isAuthenticated) {
    next('/login');
    return;
  }
  
  next();
});

export default router; 