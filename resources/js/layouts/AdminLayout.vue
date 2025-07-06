<template>
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex flex-col">
      <div class="h-16 flex items-center justify-center font-bold text-xl border-b border-gray-700">
        🛍️ Admin Panel
      </div>
      <nav class="flex-1 py-4">
        <router-link to="/admin" class="flex items-center px-6 py-3 hover:bg-gray-700" active-class="bg-gray-900">
          📊 Dashboard
        </router-link>
        <router-link to="/admin/products" class="flex items-center px-6 py-3 hover:bg-gray-700" active-class="bg-gray-900">
          📦 Sản phẩm
        </router-link>
        <router-link to="/admin/posts" class="flex items-center px-6 py-3 hover:bg-gray-700" active-class="bg-gray-900">
          📝 Bài đăng
        </router-link>
        <router-link to="/admin/orders" class="flex items-center px-6 py-3 hover:bg-gray-700" active-class="bg-gray-900">
          🛒 Đơn hàng
        </router-link>
        <router-link to="/admin/users" class="flex items-center px-6 py-3 hover:bg-gray-700" active-class="bg-gray-900">
          👥 Tài khoản
        </router-link>
        <router-link to="/admin/warehouses" class="flex items-center px-6 py-3 hover:bg-gray-700" active-class="bg-gray-900">
          🏢 Kho hàng
        </router-link>
        <router-link to="/admin/shipping" class="flex items-center px-6 py-3 hover:bg-gray-700" active-class="bg-gray-900">
          🚚 Vận chuyển
        </router-link>
        <router-link to="/admin/reports" class="flex items-center px-6 py-3 hover:bg-gray-700" active-class="bg-gray-900">
          📈 Báo cáo
        </router-link>
        <router-link to="/admin/settings" class="flex items-center px-6 py-3 hover:bg-gray-700" active-class="bg-gray-900">
          ⚙️ Cài đặt
        </router-link>
      </nav>
      <div class="p-4 border-t border-gray-700">
        <button 
          @click="logout"
          class="w-full flex items-center px-4 py-2 text-red-400 hover:bg-gray-700 rounded-lg"
        >
          🚪 Đăng xuất
        </button>
      </div>
    </aside>
    <!-- Main content -->
    <main class="flex-1 bg-gray-50 p-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const logout = () => {
  localStorage.removeItem('isAuthenticated');
  localStorage.removeItem('userRole');
  router.push('/');
};
</script> 