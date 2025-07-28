<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold">Chỉnh sửa sản phẩm</h1>
        <p class="text-gray-600 mt-1">{{ product?.name }}</p>
      </div>
      <div class="flex space-x-3">
        <button 
          @click="$router.push('/admin/products')" 
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none"
        >
          Quay lại danh sách
        </button>
        <button 
          @click="saveProduct" 
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
          :disabled="saving"
        >
          {{ saving ? 'Đang lưu...' : 'Lưu thay đổi' }}
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
      <nav class="flex space-x-8">
        <button 
          @click="activeTab = 'basic'"
          :class="[
            'py-2 px-1 border-b-2 font-medium text-sm',
            activeTab === 'basic' 
              ? 'border-indigo-500 text-indigo-600' 
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          ]"
        >
          Thông tin cơ bản
        </button>
        <button 
          @click="activeTab = 'variants'"
          :class="[
            'py-2 px-1 border-b-2 font-medium text-sm',
            activeTab === 'variants' 
              ? 'border-indigo-500 text-indigo-600' 
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          ]"
        >
          Biến thể ({{ variantsCount }})
        </button>
        <button 
          @click="activeTab = 'images'"
          :class="[
            'py-2 px-1 border-b-2 font-medium text-sm',
            activeTab === 'images' 
              ? 'border-indigo-500 text-indigo-600' 
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          ]"
        >
          Ảnh sản phẩm ({{ imagesCount }})
        </button>
      </nav>
    </div>

    <!-- Tab content -->
    <div v-if="activeTab === 'basic'" class="bg-white rounded-lg shadow p-6">
      <ProductForm 
        :show="true"
        :product="product"
        :api-errors="apiErrors"
        :status-options="statusOptions"
        @submit="handleSubmit" 
        @cancel="() => {}" 
      />
    </div>

    <div v-else-if="activeTab === 'variants'" class="bg-white rounded-lg shadow">
      <div class="p-6 border-b">
        <div class="flex justify-between items-center">
          <h2 class="text-lg font-medium">Quản lý biến thể sản phẩm</h2>
          <button 
            @click="openCreateVariantModal" 
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
          >
            Thêm biến thể mới
          </button>
        </div>
      </div>
      
      <!-- Variants table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ảnh</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="variant in variants" :key="variant.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ variant.sku }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <img v-if="variant.image" :src="variant.image" :alt="variant.sku" class="h-10 w-10 rounded-lg object-cover" />
                <div v-else class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                  <span class="text-gray-400 text-xs">No img</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(variant.price) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ variant.quantity }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                  :class="variant.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ getStatusLabel(variant.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button @click="openEditVariantModal(variant)" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</button>
                <button @click="deleteVariant(variant)" class="text-red-600 hover:text-red-900">Xóa</button>
              </td>
            </tr>
            <tr v-if="variants.length === 0">
              <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                Không có biến thể nào
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-else-if="activeTab === 'images'" class="bg-white rounded-lg shadow">
      <div class="p-6 border-b">
        <div class="flex justify-between items-center">
          <h2 class="text-lg font-medium">Quản lý ảnh sản phẩm</h2>
          <button 
            @click="openCreateImageModal" 
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none"
          >
            Thêm ảnh mới
          </button>
        </div>
      </div>
      
      <!-- Images grid -->
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div 
            v-for="image in images" 
            :key="image.id" 
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200"
          >
            <div class="relative">
              <img 
                :src="image.url" 
                :alt="image.alt_text || 'Product image'" 
                class="w-full h-48 object-cover"
              />
              <div class="absolute top-2 right-2 flex space-x-1">
                <button 
                  @click="openEditImageModal(image)"
                  class="p-1 bg-blue-500 text-white rounded-full hover:bg-blue-600 focus:outline-none"
                  title="Chỉnh sửa"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button 
                  @click="deleteImage(image)"
                  class="p-1 bg-red-500 text-white rounded-full hover:bg-red-600 focus:outline-none"
                  title="Xóa"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
            <div class="p-4">
              <h3 class="font-medium text-gray-900 mb-1">{{ image.alt_text || 'Không có mô tả' }}</h3>
              <p class="text-sm text-gray-500">{{ image.caption || 'Không có chú thích' }}</p>
            </div>
          </div>
        </div>
        
        <div v-if="images.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Không có ảnh nào</h3>
          <p class="mt-1 text-sm text-gray-500">Bắt đầu bằng cách thêm ảnh mới cho sản phẩm.</p>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <VariantForm
      v-if="showVariantModal"
      :show="showVariantModal"
      :variant="selectedVariant"
      :product-id="productId"
      :on-close="closeVariantModal"
      @created="handleVariantCreated"
      @updated="handleVariantUpdated"
    />

    <ImageForm
      v-if="showImageModal"
      :show="showImageModal"
      :image="selectedImage"
      :product-id="productId"
      :on-close="closeImageModal"
      @created="handleImageCreated"
      @updated="handleImageUpdated"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import ProductForm from './form.vue'
import VariantForm from './variant-form.vue'
import ImageForm from './image-form.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'
import { formatCurrency } from '@/utils/formatCurrency'

const route = useRoute()
const productId = computed(() => route.params.id)

// State
const activeTab = ref('basic')
const product = ref(null)
const variants = ref([])
const images = ref([])
const saving = ref(false)
const apiErrors = reactive({})
const statusOptions = ref({})

// Modal state
const showVariantModal = ref(false)
const showImageModal = ref(false)
const selectedVariant = ref(null)
const selectedImage = ref(null)

// Computed
const variantsCount = computed(() => variants.value.length)
const imagesCount = computed(() => images.value.length)

// Fetch data
onMounted(async () => {
  await Promise.all([
    fetchProduct(),
    fetchVariants(),
    fetchImages(),
    fetchStatusOptions()
  ])
})

async function fetchProduct() {
  try {
    const response = await axios.get(endpoints.products.update(productId.value))
    product.value = response.data
  } catch (error) {
    console.error('Error fetching product:', error)
  }
}

async function fetchVariants() {
  try {
    const response = await axios.get(`/api/admin/products/${productId.value}/variants`)
    variants.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching variants:', error)
  }
}

async function fetchImages() {
  try {
    const response = await axios.get(`/api/admin/products/${productId.value}/images`)
    images.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching images:', error)
  }
}

async function fetchStatusOptions() {
  try {
    const response = await axios.get(endpoints.enums('ProductStatus'))
    statusOptions.value = response.data
  } catch (error) {
    statusOptions.value = {
      active: 'Đang bán',
      inactive: 'Ngừng bán'
    }
  }
}

// Product form handlers
async function handleSubmit(formData) {
  saving.value = true
  try {
    Object.keys(apiErrors).forEach(key => delete apiErrors[key])
    const dataWithMethod = {
      ...formData,
      _method: 'PUT'
    }
    await axios.post(endpoints.products.update(productId.value), dataWithMethod)
    await fetchProduct()
  } catch (error) {
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors
      for (const field in errors) {
        if (Array.isArray(errors[field])) {
          apiErrors[field] = errors[field][0]
        } else {
          apiErrors[field] = errors[field]
        }
      }
    }
  } finally {
    saving.value = false
  }
}

async function saveProduct() {
  // Trigger form submit
  const form = document.querySelector('form')
  if (form) {
    form.dispatchEvent(new Event('submit', { bubbles: true }))
  }
}

// Variant handlers
function openCreateVariantModal() {
  selectedVariant.value = null
  showVariantModal.value = true
}

function openEditVariantModal(variant) {
  selectedVariant.value = variant
  showVariantModal.value = true
}

function closeVariantModal() {
  showVariantModal.value = false
  selectedVariant.value = null
}

async function handleVariantCreated() {
  await fetchVariants()
  closeVariantModal()
}

async function handleVariantUpdated() {
  await fetchVariants()
  closeVariantModal()
}

async function deleteVariant(variant) {
  if (confirm(`Bạn có chắc chắn muốn xóa biến thể ${variant.sku}?`)) {
    try {
      await axios.delete(`/api/admin/variants/${variant.id}`)
      await fetchVariants()
    } catch (error) {
      console.error('Error deleting variant:', error)
    }
  }
}

// Image handlers
function openCreateImageModal() {
  selectedImage.value = null
  showImageModal.value = true
}

function openEditImageModal(image) {
  selectedImage.value = image
  showImageModal.value = true
}

function closeImageModal() {
  showImageModal.value = false
  selectedImage.value = null
}

async function handleImageCreated() {
  await fetchImages()
  closeImageModal()
}

async function handleImageUpdated() {
  await fetchImages()
  closeImageModal()
}

async function deleteImage(image) {
  if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
    try {
      await axios.delete(`/api/admin/images/${image.id}`)
      await fetchImages()
    } catch (error) {
      console.error('Error deleting image:', error)
    }
  }
}

// Helper functions
function getStatusLabel(status) {
  if (status === 'active') return 'Đang bán'
  if (status === 'inactive') return 'Ngừng bán'
  return status
}
</script> 