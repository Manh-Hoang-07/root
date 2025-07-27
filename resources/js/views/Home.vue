<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
          <h1 class="text-4xl md:text-6xl font-bold mb-6">
            Chào mừng đến với Shop Online
          </h1>
          <p class="text-xl md:text-2xl mb-8 text-blue-100">
            Khám phá hàng ngàn sản phẩm chất lượng với giá tốt nhất
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
              Mua sắm ngay
            </button>
            <button class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
              Tìm hiểu thêm
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Sản phẩm nổi bật</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div 
            v-for="product in featuredProducts" 
            :key="product.id"
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
          >
            <img 
              :src="product.image" 
              :alt="product.name"
              class="w-full h-48 object-cover"
            >
            <div class="p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ product.name }}</h3>
              <p class="text-gray-600 text-sm mb-4">{{ product.description }}</p>
              <div class="flex justify-between items-center">
                <span class="text-xl font-bold text-blue-600">{{ formatPrice(product.price) }}</span>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                  Thêm vào giỏ
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Danh mục sản phẩm</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div 
            v-for="category in categories" 
            :key="category.id"
            class="text-center group cursor-pointer"
          >
            <div class="bg-gray-100 rounded-lg p-6 group-hover:bg-blue-100 transition-colors">
              <span class="text-4xl mb-4 block">{{ category.icon }}</span>
              <h3 class="text-lg font-semibold text-gray-900">{{ category.name }}</h3>
              <p class="text-gray-600 text-sm">{{ category.count }} sản phẩm</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Latest News -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Tin tức mới nhất</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div 
            v-for="post in latestPosts" 
            :key="post.id"
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
          >
            <img 
              :src="post.image" 
              :alt="post.title"
              class="w-full h-48 object-cover"
            >
            <div class="p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ post.title }}</h3>
              <p class="text-gray-600 text-sm mb-4">{{ post.excerpt }}</p>
              <div class="flex justify-between items-center">
                <span class="text-gray-500 text-sm">{{ formatDate(post.created_at) }}</span>
                <button class="text-blue-600 hover:text-blue-800 font-medium">
                  Đọc thêm
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 class="text-lg font-semibold mb-4">Shop Online</h3>
            <p class="text-gray-300">
              Chuyên cung cấp các sản phẩm chất lượng cao với giá cả hợp lý.
            </p>
          </div>
          <div>
            <h4 class="text-lg font-semibold mb-4">Liên kết</h4>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-300 hover:text-white">Trang chủ</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white">Sản phẩm</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white">Tin tức</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white">Liên hệ</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-lg font-semibold mb-4">Hỗ trợ</h4>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-300 hover:text-white">Hướng dẫn mua hàng</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white">Chính sách đổi trả</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white">Vận chuyển</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white">Bảo hành</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-lg font-semibold mb-4">Liên hệ</h4>
            <ul class="space-y-2 text-gray-300">
              <li>📞 1900-1234</li>
              <li>📧 info@shoponline.com</li>
              <li>📍 123 Đường ABC, Quận 1, TP.HCM</li>
            </ul>
          </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
          <p>&copy; 2024 Shop Online. Tất cả quyền được bảo lưu.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const isAuthenticated = ref(false);

const featuredProducts = ref([
  {
    id: 1,
    name: 'iPhone 15 Pro',
    description: 'Điện thoại thông minh mới nhất từ Apple',
    price: 25000000,
    image: 'https://via.placeholder.com/300x200'
  },
  {
    id: 2,
    name: 'MacBook Air M2',
    description: 'Laptop mỏng nhẹ với hiệu năng mạnh mẽ',
    price: 35000000,
    image: 'https://via.placeholder.com/300x200'
  },
  {
    id: 3,
    name: 'AirPods Pro',
    description: 'Tai nghe không dây với chống ồn chủ động',
    price: 6500000,
    image: 'https://via.placeholder.com/300x200'
  },
  {
    id: 4,
    name: 'iPad Pro',
    description: 'Máy tính bảng cao cấp cho công việc sáng tạo',
    price: 28000000,
    image: 'https://via.placeholder.com/300x200'
  }
]);

const categories = ref([
  { id: 1, name: 'Điện thoại', icon: '📱', count: 150 },
  { id: 2, name: 'Laptop', icon: '💻', count: 89 },
  { id: 3, name: 'Phụ kiện', icon: '🎧', count: 234 },
  { id: 4, name: 'Đồng hồ', icon: '⌚', count: 67 }
]);

const latestPosts = ref([
  {
    id: 1,
    title: 'iPhone 15 Pro ra mắt với nhiều tính năng mới',
    excerpt: 'Apple vừa ra mắt iPhone 15 Pro với chip A17 Pro mạnh mẽ...',
    image: 'https://via.placeholder.com/300x200',
    created_at: '2024-01-15'
  },
  {
    id: 2,
    title: 'MacBook Air M2 - Lựa chọn hoàn hảo cho sinh viên',
    excerpt: 'MacBook Air M2 với hiệu năng ấn tượng và thời lượng pin dài...',
    image: 'https://via.placeholder.com/300x200',
    created_at: '2024-01-14'
  },
  {
    id: 3,
    title: 'Xu hướng tai nghe không dây 2024',
    excerpt: 'Khám phá những xu hướng tai nghe không dây mới nhất...',
    image: 'https://via.placeholder.com/300x200',
    created_at: '2024-01-13'
  }
]);

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN');
};

const logout = () => {
  localStorage.removeItem('isAuthenticated');
  localStorage.removeItem('userRole');
  isAuthenticated.value = false;
  router.push('/');
};

onMounted(() => {
  isAuthenticated.value = localStorage.getItem('isAuthenticated') === 'true';
});
</script> 
