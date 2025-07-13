<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý vận chuyển</h1>
      <button 
        @click="showAddModal = true"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
      >
        Thêm khu vực vận chuyển
      </button>
    </div>

    <!-- Shipping Zones -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="zone in shippingZones" 
        :key="zone.id"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
      >
        <div class="flex justify-between items-start mb-4">
          <h3 class="text-lg font-semibold text-gray-900">{{ zone.name }}</h3>
          <span 
            :class="{
              'px-2 py-1 text-xs font-semibold rounded-full': true,
              'bg-green-100 text-green-800': zone.status === 'active',
              'bg-red-100 text-red-800': zone.status === 'inactive'
            }"
          >
            {{ zone.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
          </span>
        </div>
        
        <div class="space-y-2 text-sm text-gray-600 mb-4">
          <div>💰 Phí cơ bản: {{ formatPrice(zone.base_fee) }}</div>
          <div>⚖️ Phí theo kg: {{ formatPrice(zone.weight_fee) }}/kg</div>
          <div>📅 Thời gian: {{ zone.estimated_days }} ngày</div>
          <div v-if="zone.provinces?.length">
            <div class="font-medium">Tỉnh/Thành:</div>
            <div class="text-xs">{{ zone.provinces.join(', ') }}</div>
          </div>
        </div>

        <div class="flex justify-between items-center">
          <button 
            @click="calculateShipping(zone)"
            class="text-blue-600 hover:text-blue-900 text-sm font-medium"
          >
            Tính phí
          </button>
          <div class="flex space-x-2">
            <button 
              @click="editZone(zone)"
              class="text-green-600 hover:text-green-900 text-sm"
            >
              Sửa
            </button>
            <button 
              @click="deleteZone(zone.id)"
              class="text-red-600 hover:text-red-900 text-sm"
            >
              Xóa
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Calculate Shipping Modal -->
    <div v-if="showCalculateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Tính phí vận chuyển</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Tỉnh/Thành</label>
              <select 
                v-model="calculateForm.province"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Chọn tỉnh/thành</option>
                <option value="Hà Nội">Hà Nội</option>
                <option value="TP.HCM">TP.HCM</option>
                <option value="Đà Nẵng">Đà Nẵng</option>
                <option value="Cần Thơ">Cần Thơ</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Quận/Huyện</label>
              <select 
                v-model="calculateForm.district"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Chọn quận/huyện</option>
                <option value="Cầu Giấy">Cầu Giấy</option>
                <option value="Đống Đa">Đống Đa</option>
                <option value="Quận 1">Quận 1</option>
                <option value="Quận 3">Quận 3</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Trọng lượng (kg)</label>
              <input 
                v-model="calculateForm.weight"
                type="number" 
                step="0.1"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div v-if="shippingResult" class="bg-blue-50 p-4 rounded-lg">
              <div class="text-lg font-semibold text-blue-900">
                Phí vận chuyển: {{ formatPrice(shippingResult.shipping_fee) }}
              </div>
              <div class="text-sm text-blue-700">
                Thời gian: {{ shippingResult.estimated_days }} ngày
              </div>
            </div>
          </div>
          <div class="flex justify-end space-x-3 mt-6">
            <button 
              @click="showCalculateModal = false"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Đóng
            </button>
            <button 
              @click="calculateShippingFee"
              class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
            >
              Tính phí
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
            {{ showEditModal ? 'Sửa khu vực vận chuyển' : 'Thêm khu vực vận chuyển' }}
          </h3>
          <form @submit.prevent="saveZone">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Tên khu vực</label>
                <input 
                  v-model="form.name"
                  type="text" 
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Phí cơ bản</label>
                <input 
                  v-model="form.base_fee"
                  type="number" 
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Phí theo kg</label>
                <input 
                  v-model="form.weight_fee"
                  type="number" 
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Thời gian vận chuyển (ngày)</label>
                <input 
                  v-model="form.estimated_days"
                  type="number" 
                  required
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
  estimated_days: 1,
  status: 'active'
});

const calculateForm = ref({
  province: '',
  district: '',
  weight: ''
});

const loadShippingZones = async () => {
  try {
    const response = await fetch('/api/shipping/zones');
    const data = await response.json();
    shippingZones.value = data.data.data || [];
  } catch (error) {
    console.error('Error loading shipping zones:', error);
  }
};

const editZone = (zone) => {
  editingZone.value = zone;
  form.value = { ...zone };
  showEditModal.value = true;
};

const calculateShipping = (zone) => {
  editingZone.value = zone;
  showCalculateModal.value = true;
  shippingResult.value = null;
};

const calculateShippingFee = async () => {
  try {
    const response = await fetch('/api/shipping/calculate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        shipping_address: {
          province: calculateForm.value.province,
          district: calculateForm.value.district
        },
        items: [
          {
            product_id: 1,
            quantity: 1
          }
        ]
      })
    });
    
    const data = await response.json();
    if (data.success) {
      shippingResult.value = data.data;
    }
  } catch (error) {
    console.error('Error calculating shipping:', error);
  }
};

const saveZone = async () => {
  try {
    const url = showEditModal.value 
      ? `/api/shipping/zones/${editingZone.value.id}` 
      : '/api/shipping/zones';
    
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
      loadShippingZones();
    }
  } catch (error) {
    console.error('Error saving shipping zone:', error);
  }
};

const deleteZone = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa khu vực vận chuyển này?')) return;
  
  try {
    const response = await fetch(`/api/shipping/zones/${id}`, {
      method: 'DELETE'
    });
    
    if (response.ok) {
      loadShippingZones();
    }
  } catch (error) {
    console.error('Error deleting shipping zone:', error);
  }
};

const closeModal = () => {
  showAddModal.value = false;
  showEditModal.value = false;
  showCalculateModal.value = false;
  editingZone.value = null;
  shippingResult.value = null;
  form.value = {
    name: '',
    base_fee: '',
    weight_fee: '',
    estimated_days: 1,
    status: 'active'
  };
  calculateForm.value = {
    province: '',
    district: '',
    weight: ''
  };
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price);
};

// onMounted(() => {
//   loadShippingZones();
// });
</script> 