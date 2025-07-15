<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Banner -->
    <div class="relative h-64 md:h-96 flex items-center justify-center bg-gradient-to-r from-indigo-600 to-purple-600 mb-10">
      <div class="text-center text-white z-10">
        <h1 class="text-4xl md:text-6xl font-bold mb-4 drop-shadow-lg">Chào mừng đến với ShopOnline!</h1>
        <p class="text-lg md:text-2xl mb-6 drop-shadow">Nơi mua sắm công nghệ, điện tử, phụ kiện và nhiều hơn nữa!</p>
        <button class="bg-white text-indigo-700 font-semibold px-6 py-3 rounded-xl shadow hover:bg-indigo-50 transition-all">Khám phá ngay</button>
      </div>
      <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
    </div>

    <!-- Danh mục nổi bật -->
    <div class="max-w-6xl mx-auto px-4 mb-12">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Danh mục nổi bật</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div v-for="cat in categories" :key="cat.name" class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center hover:scale-105 transition-transform cursor-pointer">
          <div class="w-16 h-16 rounded-full flex items-center justify-center mb-3" :class="cat.bg">
            <component :is="cat.icon" class="w-8 h-8 text-white" />
          </div>
          <div class="text-lg font-semibold text-gray-800">{{ cat.name }}</div>
        </div>
      </div>
    </div>

    <!-- Sản phẩm nổi bật -->
    <div class="max-w-6xl mx-auto px-4 mb-12">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Sản phẩm nổi bật</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <div v-for="product in products" :key="product.id" class="bg-white rounded-2xl shadow-lg p-4 flex flex-col hover:scale-105 transition-transform cursor-pointer">
          <img :src="product.image" :alt="product.name" class="w-full h-40 object-cover rounded-xl mb-3" />
          <div class="font-semibold text-gray-900 text-lg mb-1">{{ product.name }}</div>
          <div class="text-indigo-600 font-bold text-xl mb-2">{{ formatCurrency(product.price) }}</div>
          <div class="text-gray-500 text-sm mb-2">{{ product.brand }}</div>
          <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">Xem chi tiết</button>
        </div>
      </div>
    </div>

    <!-- Tin tức mới nhất -->
    <div class="max-w-6xl mx-auto px-4 mb-16">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Tin tức mới nhất</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="post in posts" :key="post.id" class="bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col hover:scale-105 transition-transform cursor-pointer">
          <img :src="post.image" :alt="post.title" class="w-full h-40 object-cover" />
          <div class="p-4 flex-1 flex flex-col">
            <div class="font-semibold text-gray-900 text-lg mb-2">{{ post.title }}</div>
            <div class="text-gray-500 text-sm mb-3 line-clamp-2">{{ post.excerpt }}</div>
            <div class="mt-auto text-indigo-600 text-sm font-medium">{{ formatDate(post.created_at) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { ShoppingBagIcon, DevicePhoneMobileIcon, CpuChipIcon, SpeakerWaveIcon } from '@heroicons/vue/24/outline'

const categories = ref([
  { name: 'Điện thoại', icon: DevicePhoneMobileIcon, bg: 'bg-gradient-to-br from-blue-500 to-indigo-500' },
  { name: 'Laptop', icon: CpuChipIcon, bg: 'bg-gradient-to-br from-purple-500 to-pink-500' },
  { name: 'Phụ kiện', icon: SpeakerWaveIcon, bg: 'bg-gradient-to-br from-green-500 to-emerald-500' },
  { name: 'Khác', icon: ShoppingBagIcon, bg: 'bg-gradient-to-br from-yellow-500 to-orange-500' },
])

const products = ref([
  {
    id: 1,
    name: 'iPhone 15 Pro Max 256GB',
    image: 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400',
    price: 29990000,
    brand: 'Apple'
  },
  {
    id: 2,
    name: 'Samsung Galaxy S24 Ultra',
    image: 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400',
    price: 24990000,
    brand: 'Samsung'
  },
  {
    id: 3,
    name: 'MacBook Pro 14" M3 Pro',
    image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400',
    price: 45990000,
    brand: 'Apple'
  },
  {
    id: 4,
    name: 'AirPods Pro 2nd Gen',
    image: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400',
    price: 5990000,
    brand: 'Apple'
  }
])

const posts = ref([
  {
    id: 1,
    title: 'iPhone 15 Pro Max ra mắt với nhiều nâng cấp',
    excerpt: 'Apple vừa chính thức giới thiệu iPhone 15 Pro Max với chip A17 Pro, camera 48MP, thiết kế titan...',
    image: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400',
    created_at: '2024-07-01'
  },
  {
    id: 2,
    title: 'Top 5 laptop đáng mua nhất 2024',
    excerpt: 'Bạn đang tìm kiếm một chiếc laptop mạnh mẽ, bền bỉ và giá hợp lý? Đây là 5 lựa chọn tốt nhất năm nay...',
    image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400',
    created_at: '2024-06-25'
  },
  {
    id: 3,
    title: 'Phụ kiện công nghệ hot tháng 7',
    excerpt: 'Tổng hợp các phụ kiện công nghệ mới, hữu ích cho dân văn phòng, học sinh, sinh viên...',
    image: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400',
    created_at: '2024-06-20'
  }
])

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const formatDate = (dateString) => {
  return new Intl.DateTimeFormat('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(new Date(dateString))
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 