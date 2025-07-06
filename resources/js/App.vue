<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Ẩn navbar nếu là admin -->
    <template v-if="!isAdminRoute">
      <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
          <div class="flex justify-between h-16">
            <div class="flex items-center">
              <h1 class="text-xl font-bold text-gray-800">Laravel Vue App</h1>
            </div>
            <div class="flex items-center space-x-4">
              <!-- Public Navigation -->
              <router-link 
                to="/" 
                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                active-class="text-blue-600"
              >
                Trang chủ
              </router-link>
              <router-link 
                to="/about" 
                class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                active-class="text-blue-600"
              >
                Giới thiệu
              </router-link>

              <!-- Admin Navigation -->
              <template v-if="isAdmin">
                <router-link 
                  to="/admin" 
                  class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                  active-class="text-blue-600"
                >
                  Dashboard
                </router-link>
                <router-link 
                  to="/admin/users" 
                  class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                  active-class="text-blue-600"
                >
                  Quản lý người dùng
                </router-link>
                <router-link 
                  to="/admin/settings" 
                  class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                  active-class="text-blue-600"
                >
                  Cài đặt
                </router-link>
              </template>

              <!-- User Navigation -->
              <template v-if="isUser">
                <router-link 
                  to="/user" 
                  class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                  active-class="text-blue-600"
                >
                  Dashboard
                </router-link>
                <router-link 
                  to="/user/profile" 
                  class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                  active-class="text-blue-600"
                >
                  Hồ sơ
                </router-link>
                <router-link 
                  to="/user/orders" 
                  class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                  active-class="text-blue-600"
                >
                  Đơn hàng
                </router-link>
              </template>

              <!-- Auth Buttons -->
              <template v-if="!isAuthenticated">
                <button 
                  @click="loginAsAdmin"
                  class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors text-sm"
                >
                  Đăng nhập Admin
                </button>
                <button 
                  @click="loginAsUser"
                  class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors text-sm"
                >
                  Đăng nhập User
                </button>
              </template>
              <template v-else>
                <button 
                  @click="logout"
                  class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-sm"
                >
                  Đăng xuất
                </button>
              </template>
            </div>
          </div>
        </div>
      </nav>
    </template>

    <main v-if="!isAdminRoute">
      <router-view />
    </main>
    <template v-else>
      <router-view />
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

// State
const isAuthenticated = ref(false);
const userRole = ref('');

// Computed
const isAdmin = computed(() => isAuthenticated.value && userRole.value === 'admin');
const isUser = computed(() => isAuthenticated.value && userRole.value === 'user');
const isAdminRoute = computed(() => route.path.startsWith('/admin'));

// Methods
const loginAsAdmin = () => {
  localStorage.setItem('isAuthenticated', 'true');
  localStorage.setItem('userRole', 'admin');
  isAuthenticated.value = true;
  userRole.value = 'admin';
  router.push('/admin');
};

const loginAsUser = () => {
  localStorage.setItem('isAuthenticated', 'true');
  localStorage.setItem('userRole', 'user');
  isAuthenticated.value = true;
  userRole.value = 'user';
  router.push('/user');
};

const logout = () => {
  localStorage.removeItem('isAuthenticated');
  localStorage.removeItem('userRole');
  isAuthenticated.value = false;
  userRole.value = '';
  router.push('/');
};

// Lifecycle
onMounted(() => {
  const auth = localStorage.getItem('isAuthenticated');
  const role = localStorage.getItem('userRole');
  
  if (auth === 'true' && role) {
    isAuthenticated.value = true;
    userRole.value = role;
  }
});
</script>

<style>
/* Global styles */
</style> 