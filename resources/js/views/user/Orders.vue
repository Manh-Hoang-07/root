<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold text-gray-800">Đơn hàng của tôi</h1>
      <button 
        @click="$router.push('/user')"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
      >
        Quay lại Dashboard
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow-md">
      <div class="flex flex-wrap gap-4">
        <select 
          v-model="filterStatus"
          class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">Tất cả trạng thái</option>
          <option value="pending">Chờ xử lý</option>
          <option value="processing">Đang xử lý</option>
          <option value="shipped">Đã gửi hàng</option>
          <option value="completed">Hoàn thành</option>
          <option value="cancelled">Đã hủy</option>
        </select>
        
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Tìm kiếm đơn hàng..."
          class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        >
        
        <button 
          @click="clearFilters"
          class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors"
        >
          Xóa bộ lọc
        </button>
      </div>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
      <div v-for="order in filteredOrders" :key="order.id" class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Order Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="text-lg font-semibold">Đơn hàng #{{ order.id }}</h3>
              <p class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</p>
            </div>
            <div class="text-right">
              <p class="text-lg font-bold text-blue-600">{{ formatCurrency(order.total) }}</p>
              <span :class="getStatusClass(order.status)" class="px-3 py-1 text-sm font-semibold rounded-full">
                {{ getStatusText(order.status) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="p-6">
          <div class="space-y-4">
            <div v-for="item in order.items" :key="item.id" class="flex items-center space-x-4">
              <img :src="item.image" :alt="item.name" class="w-16 h-16 object-cover rounded-lg">
              <div class="flex-1">
                <h4 class="font-medium">{{ item.name }}</h4>
                <p class="text-sm text-gray-600">Số lượng: {{ item.quantity }}</p>
                <p class="text-sm text-gray-600">{{ formatCurrency(item.price) }}</p>
              </div>
              <div class="text-right">
                <p class="font-medium">{{ formatCurrency(item.price * item.quantity) }}</p>
              </div>
            </div>
          </div>

          <!-- Order Actions -->
          <div class="mt-6 pt-4 border-t border-gray-200 flex justify-between items-center">
            <div class="text-sm text-gray-600">
              <p>Địa chỉ giao hàng: {{ order.shippingAddress }}</p>
              <p>Phương thức thanh toán: {{ order.paymentMethod }}</p>
            </div>
            <div class="flex space-x-2">
              <button 
                @click="viewOrderDetails(order)"
                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
              >
                Chi tiết
              </button>
              <button 
                v-if="order.status === 'pending'"
                @click="cancelOrder(order.id)"
                class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors"
              >
                Hủy đơn
              </button>
              <button 
                v-if="order.status === 'shipped'"
                @click="trackOrder(order.id)"
                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors"
              >
                Theo dõi
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredOrders.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Không có đơn hàng</h3>
      <p class="mt-1 text-sm text-gray-500">Bạn chưa có đơn hàng nào.</p>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="flex justify-center">
      <nav class="flex items-center space-x-2">
        <button 
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Trước
        </button>
        
        <button 
          v-for="page in visiblePages" 
          :key="page"
          @click="currentPage = page"
          :class="currentPage === page ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-gray-50'"
          class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium"
        >
          {{ page }}
        </button>
        
        <button 
          @click="currentPage++"
          :disabled="currentPage === totalPages"
          class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Sau
        </button>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

// State
const orders = ref([]);
const filterStatus = ref('');
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(5);

// Computed
const filteredOrders = computed(() => {
  let filtered = orders.value;

  if (filterStatus.value) {
    filtered = filtered.filter(order => order.status === filterStatus.value);
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(order => 
      order.id.toLowerCase().includes(query) ||
      order.items.some(item => item.name.toLowerCase().includes(query))
    );
  }

  return filtered;
});

const totalPages = computed(() => Math.ceil(filteredOrders.value.length / itemsPerPage.value));

const visiblePages = computed(() => {
  const pages = [];
  const start = Math.max(1, currentPage.value - 2);
  const end = Math.min(totalPages.value, currentPage.value + 2);
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

// Methods
const fetchOrders = async () => {
  try {
    // Simulate API call
    orders.value = [
      {
        id: 'ORD001',
        created_at: '2024-01-15',
        total: 500000,
        status: 'completed',
        shippingAddress: '123 Đường ABC, Quận 1, TP.HCM',
        paymentMethod: 'Thanh toán online',
        items: [
          { id: 1, name: 'Sản phẩm A', quantity: 2, price: 150000, image: 'https://via.placeholder.com/64' },
          { id: 2, name: 'Sản phẩm B', quantity: 1, price: 200000, image: 'https://via.placeholder.com/64' }
        ]
      },
      {
        id: 'ORD002',
        created_at: '2024-01-16',
        total: 300000,
        status: 'processing',
        shippingAddress: '456 Đường XYZ, Quận 2, TP.HCM',
        paymentMethod: 'Thanh toán khi nhận hàng',
        items: [
          { id: 3, name: 'Sản phẩm C', quantity: 1, price: 300000, image: 'https://via.placeholder.com/64' }
        ]
      },
      {
        id: 'ORD003',
        created_at: '2024-01-17',
        total: 800000,
        status: 'shipped',
        shippingAddress: '789 Đường DEF, Quận 3, TP.HCM',
        paymentMethod: 'Thanh toán online',
        items: [
          { id: 4, name: 'Sản phẩm D', quantity: 1, price: 400000, image: 'https://via.placeholder.com/64' },
          { id: 5, name: 'Sản phẩm E', quantity: 2, price: 200000, image: 'https://via.placeholder.com/64' }
        ]
      }
    ];
  } catch (error) {
    console.error('Error fetching orders:', error);
  }
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('vi-VN');
};

const getStatusClass = (status) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'processing': 'bg-blue-100 text-blue-800',
    'shipped': 'bg-purple-100 text-purple-800',
    'completed': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
  const texts = {
    'pending': 'Chờ xử lý',
    'processing': 'Đang xử lý',
    'shipped': 'Đã gửi hàng',
    'completed': 'Hoàn thành',
    'cancelled': 'Đã hủy'
  };
  return texts[status] || 'Không xác định';
};

const clearFilters = () => {
  filterStatus.value = '';
  searchQuery.value = '';
  currentPage.value = 1;
};

const viewOrderDetails = (order) => {
  // Navigate to order details page
  console.log('Viewing order details:', order);
};

const cancelOrder = async (orderId) => {
  if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
    try {
      // Simulate API call
      console.log('Cancelling order:', orderId);
      alert('Đơn hàng đã được hủy thành công!');
      await fetchOrders(); // Refresh orders
    } catch (error) {
      console.error('Error cancelling order:', error);
      alert('Có lỗi xảy ra khi hủy đơn hàng!');
    }
  }
};

const trackOrder = (orderId) => {
  // Navigate to tracking page
  console.log('Tracking order:', orderId);
};

// Lifecycle
onMounted(() => {
  fetchOrders();
});
</script> 