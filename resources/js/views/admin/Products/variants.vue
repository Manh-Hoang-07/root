<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold">Quản lý biến thể sản phẩm</h1>
        <p class="text-gray-600 mt-1">{{ product?.name }}</p>
      </div>
      <div class="flex space-x-3">
        <button 
          @click="$router.push(`/admin/products/${productId}/edit`)" 
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none"
        >
          Quay lại sản phẩm
        </button>
        <button 
          @click="openCreateModal" 
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
        >
          Thêm biến thể mới
        </button>
      </div>
    </div>

    <!-- Bảng dữ liệu -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ảnh</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá gốc</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá KM</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thuộc tính</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="variant in variants" :key="variant.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ variant.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <img v-if="variant.image" :src="variant.image" :alt="variant.sku" class="h-10 w-10 rounded-lg object-cover" />
              <div v-else class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                <span class="text-gray-400 text-xs">No img</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ variant.sku }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ variant.barcode || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(variant.price) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <span v-if="variant.sale_price" class="text-red-600 font-medium">{{ formatCurrency(variant.sale_price) }}</span>
              <span v-else class="text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ variant.quantity }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <div v-if="variant.attribute_values && variant.attribute_values.length > 0">
                <span v-for="attr in variant.attribute_values" :key="attr.id" class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                  {{ attr.attribute?.name }}: {{ attr.value }}
                </span>
              </div>
              <span v-else class="text-gray-400">Không có</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="variant.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              >
                {{ getStatusLabel(variant.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <Actions 
                :item="variant"
                @edit="openEditModal"
                @delete="confirmDelete"
              />
            </td>
          </tr>
          <tr v-if="variants.length === 0">
            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
              {{ loading ? 'Đang tải dữ liệu...' : 'Không có biến thể nào' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal thêm mới -->
    <VariantForm
      v-if="showCreateModal"
      :show="showCreateModal"
      :product-id="productId"
      :on-close="closeCreateModal"
      @created="handleVariantCreated"
    />

    <!-- Modal chỉnh sửa -->
    <VariantForm
      v-if="showEditModal"
      :show="showEditModal"
      :variant="selectedVariant"
      :product-id="productId"
      :on-close="closeEditModal"
      @updated="handleVariantUpdated"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa biến thể ${selectedVariant?.sku || ''}?`"
      :on-close="closeDeleteModal"
      @confirm="deleteVariant"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import VariantForm from './variant-form.vue'
import ConfirmModal from '@/components/Core/ConfirmModal.vue'
import Actions from '@/components/Core/Actions.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'
import { formatCurrency } from '@/utils/formatCurrency'

const route = useRoute()
const productId = computed(() => route.params.id)

// State
const variants = ref([])
const product = ref(null)
const selectedVariant = ref(null)
const loading = ref(false)

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

// Fetch data
onMounted(async () => {
  await Promise.all([
    fetchProduct(),
    fetchVariants()
  ])
})

async function fetchProduct() {
  try {
    const response = await axios.get(endpoints.products.update(productId.value))
    product.value = response.data
  } catch (error) {
    
  }
}

async function fetchVariants() {
  loading.value = true
  try {
    const response = await axios.get(`/api/admin/products/${productId.value}/variants`)
    variants.value = response.data.data || []
  } catch (error) {
    
  } finally {
    loading.value = false
  }
}

// Modal handlers
function openCreateModal() {
  showCreateModal.value = true
}
function closeCreateModal() {
  showCreateModal.value = false
}
function openEditModal(variant) {
  selectedVariant.value = variant
  showEditModal.value = true
}
function closeEditModal() {
  showEditModal.value = false
  selectedVariant.value = null
}
function confirmDelete(variant) {
  selectedVariant.value = variant
  showDeleteModal.value = true
}
function closeDeleteModal() {
  showDeleteModal.value = false
  selectedVariant.value = null
}

// Action handlers
async function handleVariantCreated() {
  await fetchVariants()
  closeCreateModal()
}
async function handleVariantUpdated() {
  await fetchVariants()
  closeEditModal()
}
async function deleteVariant() {
  try {
    await axios.delete(`/api/admin/variants/${selectedVariant.value.id}`)
    await fetchVariants()
    closeDeleteModal()
  } catch (error) {
    
  }
}

// Helper functions
function getStatusLabel(status) {
  if (status === 'active') return 'Đang bán'
  if (status === 'inactive') return 'Ngừng bán'
  return status
}
</script> 
