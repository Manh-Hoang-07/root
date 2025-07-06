<template>
  <div class="space-y-8">
    <!-- Header -->
    <div class="text-center">
      <h1 class="text-4xl font-bold text-gray-800 mb-4">Về ứng dụng</h1>
      <p class="text-xl text-gray-600">Laravel + Vue.js SPA Application</p>
    </div>

    <!-- Tech Stack -->
    <div class="bg-white p-8 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6">Công nghệ sử dụng</h2>
      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <h3 class="text-lg font-semibold mb-3 text-blue-600">Backend</h3>
          <ul class="space-y-2">
            <li class="flex items-center">
              <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
              Laravel 11 - PHP Framework
            </li>
            <li class="flex items-center">
              <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
              MySQL Database
            </li>
            <li class="flex items-center">
              <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
              Eloquent ORM
            </li>
            <li class="flex items-center">
              <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
              RESTful API
            </li>
          </ul>
        </div>
        <div>
          <h3 class="text-lg font-semibold mb-3 text-green-600">Frontend</h3>
          <ul class="space-y-2">
            <li class="flex items-center">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
              Vue.js 3 - Progressive Framework
            </li>
            <li class="flex items-center">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
              Composition API
            </li>
            <li class="flex items-center">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
              Vue Router 4
            </li>
            <li class="flex items-center">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
              Pinia State Management
            </li>
            <li class="flex items-center">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
              Tailwind CSS
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Features -->
    <div class="bg-white p-8 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6">Tính năng</h2>
      <div class="grid md:grid-cols-3 gap-6">
        <div class="text-center p-4">
          <div class="text-4xl mb-4">🎯</div>
          <h3 class="text-lg font-semibold mb-2">SPA Architecture</h3>
          <p class="text-gray-600">Single Page Application với routing mượt mà</p>
        </div>
        <div class="text-center p-4">
          <div class="text-4xl mb-4">⚡</div>
          <h3 class="text-lg font-semibold mb-2">Fast Development</h3>
          <p class="text-gray-600">Hot reload và developer experience tuyệt vời</p>
        </div>
        <div class="text-center p-4">
          <div class="text-4xl mb-4">🎨</div>
          <h3 class="text-lg font-semibold mb-2">Modern UI</h3>
          <p class="text-gray-600">Giao diện hiện đại với Tailwind CSS</p>
        </div>
      </div>
    </div>

    <!-- Project Structure -->
    <div class="bg-white p-8 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6">Cấu trúc project</h2>
      <div class="bg-gray-50 p-4 rounded-lg font-mono text-sm">
        <div class="space-y-1">
          <div><span class="text-blue-600">resources/</span></div>
          <div class="ml-4"><span class="text-green-600">js/</span></div>
          <div class="ml-8"><span class="text-purple-600">App.vue</span> - Component chính</div>
          <div class="ml-8"><span class="text-purple-600">app.js</span> - Entry point</div>
          <div class="ml-8"><span class="text-green-600">views/</span> - Các trang</div>
          <div class="ml-12"><span class="text-purple-600">Home.vue</span> - Trang chủ</div>
          <div class="ml-12"><span class="text-purple-600">Users.vue</span> - Quản lý người dùng</div>
          <div class="ml-12"><span class="text-purple-600">About.vue</span> - Trang giới thiệu</div>
          <div class="ml-8"><span class="text-green-600">router/</span> - Vue Router</div>
          <div class="ml-12"><span class="text-purple-600">index.js</span> - Cấu hình routes</div>
          <div class="ml-8"><span class="text-green-600">stores/</span> - Pinia stores</div>
        </div>
      </div>
    </div>

    <!-- Getting Started -->
    <div class="bg-white p-8 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6">Bắt đầu</h2>
      <div class="space-y-4">
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="font-semibold mb-2">1. Cài đặt dependencies</h3>
          <code class="bg-gray-800 text-green-400 px-2 py-1 rounded">npm install</code>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="font-semibold mb-2">2. Chạy development server</h3>
          <code class="bg-gray-800 text-green-400 px-2 py-1 rounded">npm run dev</code>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
          <h3 class="font-semibold mb-2">3. Build cho production</h3>
          <code class="bg-gray-800 text-green-400 px-2 py-1 rounded">npm run build</code>
        </div>
      </div>
    </div>

    <!-- Contact -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8 rounded-lg text-white text-center">
      <h2 class="text-2xl font-bold mb-4">Liên hệ</h2>
      <p class="mb-4">Có câu hỏi hoặc muốn đóng góp? Hãy liên hệ với chúng tôi!</p>
      <div class="space-x-4">
        <button class="bg-white text-blue-600 px-6 py-2 rounded-lg hover:bg-gray-100 transition-colors">
          GitHub
        </button>
        <button class="bg-white text-blue-600 px-6 py-2 rounded-lg hover:bg-gray-100 transition-colors">
          Email
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Component logic here
</script> 