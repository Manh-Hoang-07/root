<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý đơn hàng</h1>
      <div class="flex space-x-2">
        <button 
          @click="loadOrders"
          class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
        >
          Làm mới
        </button>
        <button 
          @click="showStats = !showStats"
          class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors"
        >
          Thống kê
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div v-if="showStats" class="bg-white p-6 rounded-lg shadow mb-6">
      <h2 class="text-lg font-semibold mb-4">Thống kê đơn hàng</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 p-4 rounded-lg">
          <div class="text-2xl font-bold text-blue-600">{{ stats.total_orders || 0 }}</div>
          <div class="text-sm text-gray-600">Tổng đơn hàng</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
          <div class="text-2xl font-bold text-green-600">{{ formatPrice(stats.total_revenue || 0) }}</div>
          <div class="text-sm text-gray-600">Tổng doanh thu</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
          <div class="text-2xl font-bold text-yellow-600">{{ stats.pending_orders || 0 }}</div>
          <div class="text-sm text-gray-600">Đơn chờ xử lý</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
          <div class="text-2xl font-bold text-purple-600">{{ stats.delivered_orders || 0 }}</div>
          <div class="text-sm text-gray-600">Đơn đã giao</div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input 
          v-model="filters.search"
          type="text" 
          placeholder="Tìm kiếm mã đơn hàng..."
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <select 
          v-model="filters.status"
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tất cả trạng thái</option>
          <option value="pending">Chờ xử lý</option>
          <option value="confirmed">Đã xác nhận</option>
          <option value="processing">Đang xử lý</option>
          <option value="shipping">Đang giao</option>
          <option value="delivered">Đã giao</option>
          <option value="cancelled">Đã hủy</option>
        </select>
        <select 
          v-model="filters.payment_status"
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tất cả thanh toán</option>
          <option value="pending">Chờ thanh toán</option>
          <option value="paid">Đã thanh toán</option>
          <option value="failed">Thanh toán thất bại</option>
        </select>
        <input 
          v-model="filters.date_from"
          type="date" 
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <button 
          @click="loadOrders"
          class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
        >
          Lọc
        </button>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn hàng</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thanh toán</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ order.order_number }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ order.user?.name }}</div>
                <div class="text-sm text-gray-500">{{ order.user?.email }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatPrice(order.final_amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="{
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                    'bg-yellow-100 text-yellow-800': order.status === 'pending',
                    'bg-blue-100 text-blue-800': order.status === 'confirmed',
                    'bg-purple-100 text-purple-800': order.status === 'processing',
                    'bg-indigo-100 text-indigo-800': order.status === 'shipping',
                    'bg-green-100 text-green-800': order.status === 'delivered',
                    'bg-red-100 text-red-800': order.status === 'cancelled'
                  }"
                >
                  {{ getStatusText(order.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="{
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                    'bg-yellow-100 text-yellow-800': order.payment_status === 'pending',
                    'bg-green-100 text-green-800': order.payment_status === 'paid',
                    'bg-red-100 text-red-800': order.payment_status === 'failed'
                  }"
                >
                  {{ getPaymentStatusText(order.payment_status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(order.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button 
                  @click="viewOrder(order)"
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  Xem
                </button>
                <button 
                  @click="updateStatus(order)"
                  class="text-green-600 hover:text-green-900"
                >
                  Cập nhật
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Order Detail Modal -->
    <div v-if="showOrderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">
              Chi tiết đơn hàng: {{ selectedOrder?.order_number }}
            </h3>
            <button 
              @click="showOrderModal = false"
              class="text-gray-400 hover:text-gray-600"
            >
              ✕
            </button>
          </div>
          
          <div v-if="selectedOrder" class="space-y-6">
            <!-- Order Info -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <h4 class="font-semibold mb-2">Thông tin đơn hàng</h4>
                <div class="space-y-1 text-sm">
                  <div><strong>Mã đơn:</strong> {{ selectedOrder.order_number }}</div>
                  <div><strong>Ngày tạo:</strong> {{ formatDate(selectedOrder.created_at) }}</div>
                  <div><strong>Trạng thái:</strong> {{ getStatusText(selectedOrder.status) }}</div>
                  <div><strong>Thanh toán:</strong> {{ getPaymentStatusText(selectedOrder.payment_status) }}</div>
                </div>
              </div>
              <div>
                <h4 class="font-semibold mb-2">Thông tin khách hàng</h4>
                <div class="space-y-1 text-sm">
                  <div><strong>Tên:</strong> {{ selectedOrder.user?.name }}</div>
                  <div><strong>Email:</strong> {{ selectedOrder.user?.email }}</div>
                  <div><strong>Địa chỉ:</strong> {{ selectedOrder.shipping_address?.address }}</div>
                </div>
              </div>
            </div>

            <!-- Order Items -->
            <div>
              <h4 class="font-semibold mb-2">Sản phẩm</h4>
              <div class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Sản phẩm</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Giá</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Số lượng</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Thành tiền</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in selectedOrder.items" :key="item.id">
                      <td class="px-4 py-2 text-sm">{{ item.product_name }}</td>
                      <td class="px-4 py-2 text-sm">{{ formatPrice(item.product_price) }}</td>
                      <td class="px-4 py-2 text-sm">{{ item.quantity }}</td>
                      <td class="px-4 py-2 text-sm">{{ formatPrice(item.subtotal) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Order Summary -->
            <div class="border-t pt-4">
              <div class="flex justify-end">
                <div class="w-64 space-y-2">
                  <div class="flex justify-between">
                    <span>Tổng tiền hàng:</span>
                    <span>{{ formatPrice(selectedOrder.total_amount) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Phí vận chuyển:</span>
                    <span>{{ formatPrice(selectedOrder.shipping_fee) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Giảm giá:</span>
                    <span>-{{ formatPrice(selectedOrder.discount_amount) }}</span>
                  </div>
                  <div class="flex justify-between font-semibold border-t pt-2">
                    <span>Tổng cộng:</span>
                    <span>{{ formatPrice(selectedOrder.final_amount) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showAddModal || showEditModal" class="fixed inset-0 flex items-center justify-center">
      <div class="fixed inset-0 z-40 bg-white bg-opacity-10 backdrop-blur-md"></div>
      <div class="relative z-50 bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <!-- Nội dung form thêm/sửa -->
        <!-- ... giữ nguyên phần form ... -->
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const orders = ref([]);
const stats = ref({});
const showStats = ref(false);
const showOrderModal = ref(false);
const selectedOrder = ref(null);

const filters = ref({
  search: '',
  status: '',
  payment_status: '',
  date_from: ''
});

const loadOrders = async () => {
  try {
    const params = new URLSearchParams();
    if (filters.value.search) params.append('search', filters.value.search);
    if (filters.value.status) params.append('status', filters.value.status);
    if (filters.value.payment_status) params.append('payment_status', filters.value.payment_status);
    if (filters.value.date_from) params.append('date_from', filters.value.date_from);
    
    const response = await fetch(`/api/orders?${params}`);
    const data = await response.json();
    orders.value = data.data.data || [];
  } catch (error) {
    console.error('Error loading orders:', error);
  }
};

const loadStats = async () => {
  try {
    const response = await fetch('/api/orders/stats');
    const data = await response.json();
    stats.value = data.data.stats || {};
  } catch (error) {
    console.error('Error loading stats:', error);
  }
};

const viewOrder = async (order) => {
  try {
    const response = await fetch(`/api/orders/${order.id}`);
    const data = await response.json();
    selectedOrder.value = data.data;
    showOrderModal.value = true;
  } catch (error) {
    console.error('Error loading order details:', error);
  }
};

const updateStatus = (order) => {
  // Implement status update logic
  alert('Chức năng cập nhật trạng thái đơn hàng');
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN');
};

const getStatusText = (status) => {
  const statusMap = {
    'pending': 'Chờ xử lý',
    'confirmed': 'Đã xác nhận',
    'processing': 'Đang xử lý',
    'shipping': 'Đang giao',
    'delivered': 'Đã giao',
    'cancelled': 'Đã hủy'
  };
  return statusMap[status] || status;
};

const getPaymentStatusText = (status) => {
  const statusMap = {
    'pending': 'Chờ thanh toán',
    'paid': 'Đã thanh toán',
    'failed': 'Thanh toán thất bại'
  };
  return statusMap[status] || status;
};

onMounted(() => {
  loadOrders();
  loadStats();
});
</script> 