<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý sản phẩm</h1>
      <button 
        @click="showAddModal = true"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
      >
        Thêm sản phẩm
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input 
          v-model="filters.search"
          type="text" 
          placeholder="Tìm kiếm sản phẩm..."
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <select 
          v-model="filters.status"
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tất cả trạng thái</option>
          <option value="active">Hoạt động</option>
          <option value="inactive">Không hoạt động</option>
          <option value="out_of_stock">Hết hàng</option>
        </select>
        <select 
          v-model="filters.brand"
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tất cả thương hiệu</option>
          <option value="Apple">Apple</option>
          <option value="Samsung">Samsung</option>
          <option value="Dell">Dell</option>
          <option value="Sony">Sony</option>
        </select>
        <button 
          @click="loadProducts"
          class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
        >
          Lọc
        </button>
      </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tồn kho</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <img 
                      :src="product.images?.[0] || '/placeholder.jpg'" 
                      :alt="product.name"
                      class="h-10 w-10 rounded-lg object-cover"
                    >
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                    <div class="text-sm text-gray-500">{{ product.brand }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.sku }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatPrice(product.price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ product.available_quantity }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="{
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                    'bg-green-100 text-green-800': product.status === 'active',
                    'bg-red-100 text-red-800': product.status === 'inactive',
                    'bg-yellow-100 text-yellow-800': product.status === 'out_of_stock'
                  }"
                >
                  {{ getStatusText(product.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button 
                  @click="editProduct(product)"
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  Sửa
                </button>
                <button 
                  @click="deleteProduct(product.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Xóa
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ showEditModal ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}
          </h3>
          <form @submit.prevent="saveProduct">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
                <input 
                  v-model="form.name"
                  type="text" 
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">SKU</label>
                <input 
                  v-model="form.sku"
                  type="text" 
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Giá</label>
                <input 
                  v-model="form.price"
                  type="number" 
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Thương hiệu</label>
                <input 
                  v-model="form.brand"
                  type="text" 
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select 
                  v-model="form.status"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="active">Hoạt động</option>
                  <option value="inactive">Không hoạt động</option>
                  <option value="out_of_stock">Hết hàng</option>
                </select>
              </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
              <button 
                type="button"
                @click="closeModal"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
              >
                Hủy
              </button>
              <button 
                type="submit"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
              >
                {{ showEditModal ? 'Cập nhật' : 'Thêm' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const products = ref([]);
const showAddModal = ref(false);
const showEditModal = ref(false);
const editingProduct = ref(null);

const filters = ref({
  search: '',
  status: '',
  brand: ''
});

const form = ref({
  name: '',
  sku: '',
  price: '',
  brand: '',
  status: 'active'
});

const loadProducts = async () => {
  try {
    const params = new URLSearchParams();
    if (filters.value.search) params.append('search', filters.value.search);
    if (filters.value.status) params.append('status', filters.value.status);
    if (filters.value.brand) params.append('brand', filters.value.brand);
    
    const response = await fetch(`/api/products?${params}`);
    const data = await response.json();
    products.value = data.data.data || [];
  } catch (error) {
    console.error('Error loading products:', error);
  }
};

const editProduct = (product) => {
  editingProduct.value = product;
  form.value = { ...product };
  showEditModal.value = true;
};

const saveProduct = async () => {
  try {
    const url = showEditModal.value 
      ? `/api/products/${editingProduct.value.id}` 
      : '/api/products';
    
    const method = showEditModal.value ? 'PUT' : 'POST';
    
    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(form.value)
    });
    
    if (response.ok) {
      closeModal();
      loadProducts();
    }
  } catch (error) {
    console.error('Error saving product:', error);
  }
};

const deleteProduct = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
  
  try {
    const response = await fetch(`/api/products/${id}`, {
      method: 'DELETE'
    });
    
    if (response.ok) {
      loadProducts();
    }
  } catch (error) {
    console.error('Error deleting product:', error);
  }
};

const closeModal = () => {
  showAddModal.value = false;
  showEditModal.value = false;
  editingProduct.value = null;
  form.value = {
    name: '',
    sku: '',
    price: '',
    brand: '',
    status: 'active'
  };
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price);
};

const getStatusText = (status) => {
  const statusMap = {
    'active': 'Hoạt động',
    'inactive': 'Không hoạt động',
    'out_of_stock': 'Hết hàng'
  };
  return statusMap[status] || status;
};

onMounted(() => {
  loadProducts();
});
</script> 