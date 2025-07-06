<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <nav class="bg-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-800">Laravel Vue App</h1>
          </div>
          <div class="flex items-center space-x-4">
            <router-link 
              to="/" 
              class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              active-class="text-blue-600"
            >
              Trang chủ
            </router-link>
            <router-link 
              to="/users" 
              class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              active-class="text-blue-600"
            >
              Người dùng
            </router-link>
            <router-link 
              to="/about" 
              class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              active-class="text-blue-600"
            >
              Giới thiệu
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 px-4">
      <router-view />
    </main>
  </div>
</template>

<script setup>
// Component logic here
</script>

<style>
/* Global styles */
</style> 