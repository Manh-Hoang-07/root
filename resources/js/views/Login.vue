<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center">Đăng nhập</h2>
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input v-model="email" type="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
          <input v-model="password" type="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>
        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Đăng nhập</button>
      </form>
      <div class="mt-4 text-center">
        <span class="text-gray-500">Chưa có tài khoản?</span>
        <router-link to="/register" class="text-blue-600 hover:underline ml-1">Đăng ký</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const auth = useAuthStore();

const email = ref('');
const password = ref('');
const error = ref('');

const handleLogin = async () => {
  error.value = '';
  const res = await auth.login({ email: email.value, password: password.value });
  if (res.success) {
    if (auth.userRole === 'admin') {
      router.push('/admin');
    } else {
      router.push('/');
    }
  } else {
    error.value = res.message || 'Đăng nhập thất bại';
  }
};
</script> 