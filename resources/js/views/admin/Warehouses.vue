<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý kho hàng</h1>
      <button 
        @click="showAddModal = true"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
      >
        Thêm kho hàng
      </button>
    </div>

    <!-- Warehouses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="warehouse in warehouses" 
        :key="warehouse.id"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
      >
        <div class="flex justify-between items-start mb-4">
          <h3 class="text-lg font-semibold text-gray-900">{{ warehouse.name }}</h3>
          <span 
            :class="{
              'px-2 py-1 text-xs font-semibold rounded-full': true,
              'bg-green-100 text-green-800': warehouse.status === 'active',
              'bg-red-100 text-red-800': warehouse.status === 'inactive'
            }"
          >
            {{ warehouse.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
          </span>
        </div>
        
        <div class="space-y-2 text-sm text-gray-600 mb-4">
          <div>📍 {{ warehouse.address }}</div>
          <div>🏙️ {{ warehouse.city }}, {{ warehouse.province }}</div>
          <div v-if="warehouse.phone">📞 {{ warehouse.phone }}</div>
          <div v-if="warehouse.email">📧 {{ warehouse.email }}</div>
        </div>

        <div class="flex justify-between items-center">
          <button 
            @click="viewWarehouse(warehouse)"
            class="text-blue-600 hover:text-blue-900 text-sm font-medium"
          >
            Xem chi tiết
          </button>
          <div class="flex space-x-2">
            <button 
              @click="editWarehouse(warehouse)"
              class="text-green-600 hover:text-green-900 text-sm"
            >
              Sửa
            </button>
            <button 
              @click="deleteWarehouse(warehouse.id)"
              class="text-red-600 hover:text-red-900 text-sm"
            >
              Xóa
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 flex items-center justify-center">
      <div class="fixed inset-0 z-40 bg-white bg-opacity-10 backdrop-blur-md"></div>
      <div class="relative z-50 bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ showEditModal ? 'Sửa kho hàng' : 'Thêm kho hàng' }}
          </h3>
          <form @submit.prevent="saveWarehouse">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Tên kho hàng</label>
                <input 
                  v-model="form.name"
                  type="text" 
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                <textarea 
                  v-model="form.address"
                  rows="3"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Thành phố</label>
                  <input 
                    v-model="form.city"
                    type="text" 
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Tỉnh/Thành</label>
                  <input 
                    v-model="form.province"
                    type="text" 
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                <input 
                  v-model="form.phone"
                  type="text" 
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input 
                  v-model="form.email"
                  type="email" 
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
import { ref } from 'vue';

const warehouses = ref([
  {
    id: 1,
    name: 'Kho Hà Nội',
    address: '123 Đường Láng',
    city: 'Hà Nội',
    province: 'Hà Nội',
    phone: '0123456789',
    email: 'hanoi@warehouse.vn',
    status: 'active'
  },
  {
    id: 2,
    name: 'Kho Sài Gòn',
    address: '456 Nguyễn Trãi',
    city: 'TP.HCM',
    province: 'TP.HCM',
    phone: '0987654321',
    email: 'saigon@warehouse.vn',
    status: 'active'
  },
  {
    id: 3,
    name: 'Kho Đà Nẵng',
    address: '789 Lê Duẩn',
    city: 'Đà Nẵng',
    province: 'Đà Nẵng',
    phone: '02363778899',
    email: 'danang@warehouse.vn',
    status: 'inactive'
  }
]);
const showAddModal = ref(false);
const showEditModal = ref(false);
const editingWarehouse = ref(null);

const form = ref({
  name: '',
  address: '',
  city: '',
  province: '',
  phone: '',
  email: '',
  status: 'active'
});

const loadWarehouses = async () => {
  try {
    const response = await fetch('/api/warehouses');
    const data = await response.json();
    warehouses.value = data.data.data || [];
  } catch (error) {
    console.error('Error loading warehouses:', error);
  }
};

const editWarehouse = (warehouse) => {
  editingWarehouse.value = warehouse;
  form.value = { ...warehouse };
  showEditModal.value = true;
};

const viewWarehouse = (warehouse) => {
  // Implement warehouse detail view
  alert(`Xem chi tiết kho: ${warehouse.name}`);
};

const saveWarehouse = async () => {
  try {
    const url = showEditModal.value 
      ? `/api/warehouses/${editingWarehouse.value.id}` 
      : '/api/warehouses';
    
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
      loadWarehouses();
    }
  } catch (error) {
    console.error('Error saving warehouse:', error);
  }
};

const deleteWarehouse = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa kho hàng này?')) return;
  
  try {
    const response = await fetch(`/api/warehouses/${id}`, {
      method: 'DELETE'
    });
    
    if (response.ok) {
      loadWarehouses();
    }
  } catch (error) {
    console.error('Error deleting warehouse:', error);
  }
};

const closeModal = () => {
  showAddModal.value = false;
  showEditModal.value = false;
  editingWarehouse.value = null;
  form.value = {
    name: '',
    address: '',
    city: '',
    province: '',
    phone: '',
    email: '',
    status: 'active'
  };
};

// onMounted(() => {
//   loadWarehouses();
// });
</script> 