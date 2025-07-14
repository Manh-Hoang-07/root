<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Quản lý vận chuyển</h1>
          <p class="text-gray-600">Quản lý khu vực vận chuyển và phí giao hàng</p>
        </div>
        <button 
          @click="openAddModal"
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
        >
          <span>Thêm khu vực</span>
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
            placeholder="Tìm kiếm khu vực..."
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

    <!-- Zones Grid/List -->
    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
      <div 
        v-for="zone in paginatedZones" 
        :key="zone.id"
        class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transform hover:scale-105 hover:shadow-2xl transition-all duration-300 flex flex-col justify-between min-h-[240px]"
      >
        <div class="flex items-center justify-between p-6 pb-2">
          <h3 class="text-lg font-semibold text-gray-900">{{ zone.name }}</h3>
          <span 
            :class="zone.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
            class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm"
          >
            {{ zone.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
          </span>
        </div>
        <div class="px-6 flex-1">
          <div class="space-y-2 text-sm text-gray-700 mb-3">
            <div class="flex items-center gap-2"><span class="inline-block w-5 h-5 text-yellow-500">💰</span> <span>Phí cơ bản: <span class="font-medium">{{ formatPrice(zone.base_fee) }}</span></span></div>
            <div class="flex items-center gap-2"><span class="inline-block w-5 h-5 text-indigo-500">⚖️</span> <span>Phí theo kg: <span class="font-medium">{{ formatPrice(zone.weight_fee) }}</span>/kg</span></div>
            <div class="flex items-center gap-2"><span class="inline-block w-5 h-5 text-rose-500">📅</span> <span>Thời gian: <span class="font-medium">{{ zone.estimated_days }} ngày</span></span></div>
            <div v-if="zone.provinces?.length">
              <div class="font-medium text-xs text-gray-500 mt-2">Tỉnh/Thành:</div>
              <div class="text-xs text-gray-600 line-clamp-2">{{ zone.provinces.join(', ') }}</div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-between px-6 pb-4 pt-2 mt-auto">
          <button 
            @click="calculateShipping(zone)"
            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium px-3 py-1 rounded-lg hover:bg-indigo-50 transition-all duration-200"
          >
            Tính phí
          </button>
          <div class="flex items-center space-x-2">
            <button 
              @click="editZone(zone)"
              class="text-blue-600 hover:text-blue-800 px-3 py-1 rounded-lg text-sm font-medium hover:bg-blue-50 transition-all duration-200"
            >
              Sửa
            </button>
            <button 
              @click="deleteZone(zone.id)"
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
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khu vực</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phí cơ bản</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phí theo kg</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tỉnh/Thành</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr 
              v-for="zone in paginatedZones" 
              :key="zone.id"
              class="hover:bg-gray-50 transition-colors duration-200"
            >
              <td class="px-6 py-4 whitespace-nowrap">{{ zone.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ formatPrice(zone.base_fee) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ formatPrice(zone.weight_fee) }}/kg</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ zone.estimated_days }} ngày</td>
              <td class="px-6 py-4 whitespace-nowrap text-xs">{{ zone.provinces?.join(', ') }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="zone.status === 'active' ? 'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs' : 'bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs'">
                  {{ zone.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                  <button @click="editZone(zone)" class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-all duration-200" title="Sửa">
                    Sửa
                  </button>
                  <button @click="deleteZone(zone.id)" class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-all duration-200" title="Xóa">
                    Xóa
                  </button>
                  <button @click="calculateShipping(zone)" class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-all duration-200" title="Tính phí">
                    Tính phí
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
          Hiển thị {{ startIndex + 1 }} đến {{ endIndex }} trong tổng số {{ filteredZones.length }} khu vực
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

    <!-- Modal Thêm/Sửa Khu Vực -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 flex items-center justify-center z-50">
      <div class="fixed inset-0 z-40 bg-black bg-opacity-30 backdrop-blur-sm"></div>
      <div class="relative z-50 bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="mb-4">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ showEditModal ? 'Sửa khu vực vận chuyển' : 'Thêm khu vực vận chuyển' }}
          </h3>
        </div>
        <form @submit.prevent="saveZone">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tên khu vực</label>
              <input v-model="form.name" type="text" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phí cơ bản</label>
              <input v-model="form.base_fee" type="number" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phí theo kg</label>
              <input v-model="form.weight_fee" type="number" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Thời gian vận chuyển (ngày)</label>
              <input v-model="form.estimated_days" type="number" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành (phân cách bởi dấu phẩy)</label>
              <input v-model="form.provinces" type="text" placeholder="VD: Hà Nội, TP.HCM" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
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

    <!-- Modal Tính phí -->
    <div v-if="showCalculateModal" class="fixed inset-0 flex items-center justify-center z-50">
      <div class="fixed inset-0 z-40 bg-black bg-opacity-30 backdrop-blur-sm"></div>
      <div class="relative z-50 bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Tính phí vận chuyển</h3>
        </div>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Trọng lượng (kg)</label>
            <input v-model="calculateForm.weight" type="number" step="0.1" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
          </div>
          <div v-if="shippingResult" class="bg-blue-50 p-4 rounded-lg">
            <div class="text-lg font-semibold text-blue-900">Phí vận chuyển: {{ formatPrice(shippingResult.shipping_fee) }}</div>
            <div class="text-sm text-blue-700">Thời gian: {{ shippingResult.estimated_days }} ngày</div>
          </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="closeModal" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Đóng</button>
          <button @click="calculateShippingFee" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Tính phí</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { TruckIcon, ChartBarIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';

// Stats data giữ nguyên
const stats = ref([
  { title: 'Tổng khu vực', value: '3', icon: TruckIcon, bgColor: 'bg-gradient-to-br from-blue-500 to-blue-600' },
  { title: 'Đang hoạt động', value: '2', icon: CheckCircleIcon, bgColor: 'bg-gradient-to-br from-green-500 to-green-600' },
  { title: 'Ngừng hoạt động', value: '1', icon: XCircleIcon, bgColor: 'bg-gradient-to-br from-red-500 to-red-600' },
  { title: 'Tổng phí trung bình', value: '25,000đ', icon: ChartBarIcon, bgColor: 'bg-gradient-to-br from-purple-500 to-purple-600' }
]);

const shippingZones = ref([
  {
    id: 1,
    name: 'Hà Nội',
    base_fee: 20000,
    weight_fee: 5000,
    estimated_days: 2,
    status: 'active',
    provinces: ['Ba Đình', 'Cầu Giấy', 'Đống Đa']
  },
  {
    id: 2,
    name: 'TP.HCM',
    base_fee: 25000,
    weight_fee: 6000,
    estimated_days: 3,
    status: 'active',
    provinces: ['Quận 1', 'Quận 3', 'Quận 7']
  },
  {
    id: 3,
    name: 'Đà Nẵng',
    base_fee: 30000,
    weight_fee: 7000,
    estimated_days: 4,
    status: 'inactive',
    provinces: ['Hải Châu', 'Thanh Khê']
  }
]);

const showAddModal = ref(false);
const showEditModal = ref(false);
const showCalculateModal = ref(false);
const editingZone = ref(null);
const shippingResult = ref(null);

const form = ref({
  name: '',
  base_fee: '',
  weight_fee: '',
  estimated_days: '',
  status: 'active',
  provinces: ''
});

const calculateForm = ref({
  weight: ''
});

const filters = ref({ search: '', status: '', province: '' });
const viewMode = ref('grid');
const currentPage = ref(1);
const itemsPerPage = ref(6);

const filteredZones = computed(() => {
  let filtered = shippingZones.value;
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase();
    filtered = filtered.filter(zone => zone.name.toLowerCase().includes(search));
  }
  if (filters.value.status) {
    filtered = filtered.filter(zone => zone.status === filters.value.status);
  }
  if (filters.value.province) {
    filtered = filtered.filter(zone => (zone.provinces || []).join(', ').toLowerCase().includes(filters.value.province.toLowerCase()));
  }
  return filtered;
});
const totalPages = computed(() => Math.ceil(filteredZones.value.length / itemsPerPage.value));
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, filteredZones.value.length));
const paginatedZones = computed(() => filteredZones.value.slice(startIndex.value, endIndex.value));
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
const formatPrice = (amount) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);

// --- Xử lý thao tác ---
const openAddModal = () => {
  showAddModal.value = true;
  showEditModal.value = false;
  form.value = { name: '', base_fee: '', weight_fee: '', estimated_days: '', status: 'active', provinces: '' };
};
const editZone = (zone) => {
  editingZone.value = zone;
  showEditModal.value = true;
  showAddModal.value = false;
  form.value = {
    name: zone.name,
    base_fee: zone.base_fee,
    weight_fee: zone.weight_fee,
    estimated_days: zone.estimated_days,
    status: zone.status,
    provinces: (zone.provinces || []).join(', ')
  };
};
const saveZone = () => {
  if (!form.value.name || !form.value.base_fee || !form.value.weight_fee || !form.value.estimated_days) return;
  if (showEditModal.value && editingZone.value) {
    // Update
    editingZone.value.name = form.value.name;
    editingZone.value.base_fee = Number(form.value.base_fee);
    editingZone.value.weight_fee = Number(form.value.weight_fee);
    editingZone.value.estimated_days = Number(form.value.estimated_days);
    editingZone.value.status = form.value.status;
    editingZone.value.provinces = form.value.provinces.split(',').map(s => s.trim()).filter(Boolean);
  } else {
    // Add
    shippingZones.value.unshift({
      id: Date.now(),
      name: form.value.name,
      base_fee: Number(form.value.base_fee),
      weight_fee: Number(form.value.weight_fee),
      estimated_days: Number(form.value.estimated_days),
      status: form.value.status,
      provinces: form.value.provinces.split(',').map(s => s.trim()).filter(Boolean)
    });
  }
  closeModal();
};
const deleteZone = (id) => {
  if (confirm('Bạn có chắc chắn muốn xóa khu vực này?')) {
    const idx = shippingZones.value.findIndex(z => z.id === id);
    if (idx > -1) shippingZones.value.splice(idx, 1);
  }
};
const calculateShipping = (zone) => {
  editingZone.value = zone;
  showCalculateModal.value = true;
  shippingResult.value = null;
  calculateForm.value = { weight: '' };
};
const calculateShippingFee = () => {
  if (calculateForm.value.weight && editingZone.value) {
    const weight = parseFloat(calculateForm.value.weight);
    const shippingFee = editingZone.value.base_fee + (weight * editingZone.value.weight_fee);
    shippingResult.value = {
      shipping_fee: shippingFee,
      estimated_days: editingZone.value.estimated_days
    };
  }
};
const closeModal = () => {
  showAddModal.value = false;
  showEditModal.value = false;
  showCalculateModal.value = false;
  editingZone.value = null;
  form.value = { name: '', base_fee: '', weight_fee: '', estimated_days: '', status: 'active', provinces: '' };
  calculateForm.value = { weight: '' };
  shippingResult.value = null;
};
</script>

<style scoped>
/* ... giữ lại style cũ, bổ sung nếu cần ... */
</style> 