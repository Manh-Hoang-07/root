<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Quản lý kho hàng</h1>
          <p class="text-gray-600">Quản lý danh sách kho và thông tin liên hệ</p>
        </div>
        <button 
          @click="showAddModal = true"
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
        >
          <span>Thêm kho hàng</span>
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div 
        v-for="stat in stats" 
        :key="stat.title"
        class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300 border border-gray-100"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">{{ stat.title }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stat.value }}</p>
          </div>
          <div 
            class="w-12 h-12 rounded-xl flex items-center justify-center"
            :class="stat.bgColor"
          >
            <component :is="stat.icon" class="w-6 h-6 text-white" />
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Search -->
        <div class="relative lg:col-span-2">
          <input 
            v-model="filters.search"
            type="text" 
            placeholder="Tìm kiếm kho hàng..."
            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
          />
        </div>
        <!-- Status Filter -->
        <select 
          v-model="filters.status"
          class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
        >
          <option value="">Tất cả trạng thái</option>
          <option value="active">Hoạt động</option>
          <option value="inactive">Không hoạt động</option>
        </select>
        <!-- Province Filter -->
        <input 
          v-model="filters.province"
          type="text"
          placeholder="Tỉnh/Thành..."
          class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
        />
      </div>
      <div class="flex justify-between items-center mt-4">
        <button 
          @click="clearFilters"
          class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 font-medium"
        >
          Xóa bộ lọc
        </button>
        <div class="flex items-center space-x-2">
          <button 
            @click="viewMode = 'grid'"
            :class="[
              'p-2 rounded-lg transition-all duration-200',
              viewMode === 'grid' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            ]"
          >
            Lưới
          </button>
          <button 
            @click="viewMode = 'list'"
            :class="[
              'p-2 rounded-lg transition-all duration-200',
              viewMode === 'list' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            ]"
          >
            Danh sách
          </button>
        </div>
      </div>
    </div>

    <!-- Warehouses Grid/List -->
    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
      <div 
        v-for="warehouse in paginatedWarehouses" 
        :key="warehouse.id"
        class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transform hover:scale-105 hover:shadow-2xl transition-all duration-300 flex flex-col justify-between min-h-[220px]"
      >
        <div class="flex items-center justify-between p-6 pb-2">
          <h3 class="text-lg font-semibold text-gray-900">{{ warehouse.name }}</h3>
          <span 
            :class="warehouse.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
            class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm"
          >
            {{ warehouse.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
          </span>
        </div>
        <div class="px-6 flex-1">
          <div class="space-y-2 text-sm text-gray-700 mb-3">
            <div class="flex items-center gap-2"><span class="inline-block w-5 h-5 text-indigo-500">🏢</span> <span>{{ warehouse.address }}</span></div>
            <div class="flex items-center gap-2"><span class="inline-block w-5 h-5 text-blue-500">🏙️</span> <span>{{ warehouse.city }}, {{ warehouse.province }}</span></div>
            <div v-if="warehouse.phone" class="flex items-center gap-2"><span class="inline-block w-5 h-5 text-emerald-500">📞</span> <span>{{ warehouse.phone }}</span></div>
            <div v-if="warehouse.email" class="flex items-center gap-2"><span class="inline-block w-5 h-5 text-purple-500">📧</span> <span>{{ warehouse.email }}</span></div>
          </div>
        </div>
        <div class="flex items-center justify-between px-6 pb-4 pt-2 mt-auto">
          <button 
            @click="viewWarehouse(warehouse)"
            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium px-3 py-1 rounded-lg hover:bg-indigo-50 transition-all duration-200"
          >
            Xem chi tiết
          </button>
          <div class="flex items-center space-x-2">
            <button 
              @click="editWarehouse(warehouse)"
              class="text-blue-600 hover:text-blue-800 px-3 py-1 rounded-lg text-sm font-medium hover:bg-blue-50 transition-all duration-200"
            >
              Sửa
            </button>
            <button 
              @click="deleteWarehouse(warehouse.id)"
              class="text-red-600 hover:text-red-800 px-3 py-1 rounded-lg text-sm font-medium hover:bg-red-50 transition-all duration-200"
            >
              Xóa
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên kho</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa chỉ</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thành phố</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tỉnh/Thành</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Điện thoại</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr 
              v-for="warehouse in paginatedWarehouses" 
              :key="warehouse.id"
              class="hover:bg-gray-50 transition-colors duration-200"
            >
              <td class="px-6 py-4 whitespace-nowrap">{{ warehouse.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-xs">{{ warehouse.address }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ warehouse.city }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ warehouse.province }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ warehouse.phone }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ warehouse.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="warehouse.status === 'active' ? 'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs' : 'bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs'">
                  {{ warehouse.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                  <button @click="editWarehouse(warehouse)" class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-all duration-200" title="Sửa">
                    Sửa
                  </button>
                  <button @click="deleteWarehouse(warehouse.id)" class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-all duration-200" title="Xóa">
                    Xóa
                  </button>
                  <button @click="viewWarehouse(warehouse)" class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-all duration-200" title="Xem chi tiết">
                    Xem
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="bg-white rounded-2xl shadow-lg px-6 py-4 border border-gray-100" v-if="totalPages > 1">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Hiển thị {{ startIndex + 1 }} đến {{ endIndex }} trong tổng số {{ filteredWarehouses.length }} kho hàng
        </div>
        <div class="flex items-center space-x-2">
          <button 
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
          >
            Trước
          </button>
          <div class="flex items-center space-x-1">
            <button 
              v-for="page in visiblePages" 
              :key="page"
              @click="goToPage(page)"
              :class="[
                'px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                page === currentPage 
                  ? 'bg-indigo-600 text-white' 
                  : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
          </div>
          <button 
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
          >
            Sau
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Thêm/Sửa Kho Hàng -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 flex items-center justify-center z-50">
      <div class="fixed inset-0 z-40 bg-black bg-opacity-30 backdrop-blur-sm"></div>
      <div class="relative z-50 bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="mb-4">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ showEditModal ? 'Sửa kho hàng' : 'Thêm kho hàng' }}
          </h3>
        </div>
        <form @submit.prevent="saveWarehouse">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tên kho hàng</label>
              <input v-model="form.name" type="text" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
              <textarea v-model="form.address" rows="2" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thành phố</label>
                <input v-model="form.city" type="text" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành</label>
                <input v-model="form.province" type="text" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
              <input v-model="form.phone" type="text" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="form.email" type="email" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
              <select v-model="form.status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="active">Hoạt động</option>
                <option value="inactive">Không hoạt động</option>
              </select>
            </div>
          </div>
          <div class="flex justify-end space-x-3 mt-6">
            <button type="button" @click="closeModal" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Hủy</button>
            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700">{{ showEditModal ? 'Cập nhật' : 'Thêm' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { BuildingStorefrontIcon, ChartBarIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';

// Stats data
const stats = ref([
  { title: 'Tổng kho', value: '3', icon: BuildingStorefrontIcon, bgColor: 'bg-gradient-to-br from-blue-500 to-blue-600' },
  { title: 'Đang hoạt động', value: '2', icon: CheckCircleIcon, bgColor: 'bg-gradient-to-br from-green-500 to-green-600' },
  { title: 'Ngừng hoạt động', value: '1', icon: XCircleIcon, bgColor: 'bg-gradient-to-br from-red-500 to-red-600' },
  { title: 'Tổng liên hệ', value: '3', icon: ChartBarIcon, bgColor: 'bg-gradient-to-br from-purple-500 to-purple-600' }
]);

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

const filters = ref({ search: '', status: '', province: '' });
const viewMode = ref('grid');
const currentPage = ref(1);
const itemsPerPage = ref(6);

const filteredWarehouses = computed(() => {
  let filtered = warehouses.value;
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase();
    filtered = filtered.filter(wh => wh.name.toLowerCase().includes(search));
  }
  if (filters.value.status) {
    filtered = filtered.filter(wh => wh.status === filters.value.status);
  }
  if (filters.value.province) {
    filtered = filtered.filter(wh => (wh.province || '').toLowerCase().includes(filters.value.province.toLowerCase()));
  }
  return filtered;
});
const totalPages = computed(() => Math.ceil(filteredWarehouses.value.length / itemsPerPage.value));
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, filteredWarehouses.value.length));
const paginatedWarehouses = computed(() => filteredWarehouses.value.slice(startIndex.value, endIndex.value));
const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
  let end = Math.min(totalPages.value, start + maxVisible - 1);
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1);
  }
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  return pages;
});
const clearFilters = () => {
  filters.value = { search: '', status: '', province: '' };
  currentPage.value = 1;
};
const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };
const goToPage = (page) => { currentPage.value = page; };

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

<style scoped>
/* ... giữ lại style cũ, bổ sung nếu cần ... */
</style> 