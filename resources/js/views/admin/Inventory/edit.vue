<template>
  <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Chỉnh sửa tồn kho</h3>
          <button
            @click="onClose"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Sản phẩm -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Sản phẩm <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.product_id"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Chọn sản phẩm</option>
              <option v-for="product in products" :key="product.id" :value="product.id">
                {{ product.name }} ({{ product.code }})
              </option>
            </select>
            <p v-if="errors.product_id" class="mt-1 text-sm text-red-600">{{ errors.product_id[0] }}</p>
          </div>

          <!-- Kho hàng -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Kho hàng <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.warehouse_id"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Chọn kho hàng</option>
              <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
                {{ warehouse.name }}
              </option>
            </select>
            <p v-if="errors.warehouse_id" class="mt-1 text-sm text-red-600">{{ errors.warehouse_id[0] }}</p>
          </div>

          <!-- Số lượng -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Số lượng <span class="text-red-500">*</span>
            </label>
            <input
              v-model.number="form.quantity"
              type="number"
              min="0"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Nhập số lượng"
            />
            <p v-if="errors.quantity" class="mt-1 text-sm text-red-600">{{ errors.quantity[0] }}</p>
          </div>

          <!-- Mã lô hàng -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Mã lô hàng
            </label>
            <input
              v-model="form.batch_no"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Nhập mã lô hàng"
            />
            <p v-if="errors.batch_no" class="mt-1 text-sm text-red-600">{{ errors.batch_no[0] }}</p>
          </div>

          <!-- Số lô sản xuất -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Số lô sản xuất
            </label>
            <input
              v-model="form.lot_no"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Nhập số lô sản xuất"
            />
            <p v-if="errors.lot_no" class="mt-1 text-sm text-red-600">{{ errors.lot_no[0] }}</p>
          </div>

          <!-- Hạn sử dụng -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Hạn sử dụng
            </label>
            <input
              v-model="form.expiry_date"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p v-if="errors.expiry_date" class="mt-1 text-sm text-red-600">{{ errors.expiry_date[0] }}</p>
          </div>

          <!-- Giá vốn -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Giá vốn
            </label>
            <input
              v-model.number="form.cost_price"
              type="number"
              min="0"
              step="0.01"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Nhập giá vốn"
            />
            <p v-if="errors.cost_price" class="mt-1 text-sm text-red-600">{{ errors.cost_price[0] }}</p>
          </div>

          <!-- Số lượng đã giữ chỗ -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Số lượng đã giữ chỗ
            </label>
            <input
              v-model.number="form.reserved_quantity"
              type="number"
              min="0"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Nhập số lượng đã giữ chỗ"
            />
            <p v-if="errors.reserved_quantity" class="mt-1 text-sm text-red-600">{{ errors.reserved_quantity[0] }}</p>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="onClose"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              Hủy
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
            >
              {{ loading ? 'Đang cập nhật...' : 'Cập nhật' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import apiClient from '@/api/apiClient'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  inventory: {
    type: Object,
    default: null
  },
  products: {
    type: Array,
    default: () => []
  },
  warehouses: {
    type: Array,
    default: () => []
  },
  onClose: {
    type: Function,
    required: true
  }
})

const emit = defineEmits(['updated'])

const loading = ref(false)
const errors = ref({})

const form = reactive({
  product_id: '',
  warehouse_id: '',
  quantity: 0,
  batch_no: '',
  lot_no: '',
  expiry_date: '',
  cost_price: null,
  reserved_quantity: 0
})

// Watch inventory prop để cập nhật form
watch(() => props.inventory, (newInventory) => {
  if (newInventory) {
    form.product_id = newInventory.product_id || ''
    form.warehouse_id = newInventory.warehouse_id || ''
    form.quantity = newInventory.quantity || 0
    form.batch_no = newInventory.batch_no || ''
    form.lot_no = newInventory.lot_no || ''
    form.expiry_date = newInventory.expiry_date || ''
    form.cost_price = newInventory.cost_price || null
    form.reserved_quantity = newInventory.reserved_quantity || 0
  }
}, { immediate: true })

async function handleSubmit() {
  try {
    loading.value = true
    errors.value = {}

    const response = await apiClient.put(`/api/admin/inventory/${props.inventory.id}`, form)
    
    if (response.data.success) {
      emit('updated')
      onClose()
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      console.error('Error updating inventory:', error)
    }
  } finally {
    loading.value = false
  }
}

function onClose() {
  if (props.onClose) {
    props.onClose()
  }
}
</script> 