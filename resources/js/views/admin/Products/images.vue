<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold">Quản lý ảnh sản phẩm</h1>
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
          Thêm ảnh mới
        </button>
      </div>
    </div>

    <!-- Grid ảnh -->
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
              @click="openEditModal(image)"
              class="p-1 bg-blue-500 text-white rounded-full hover:bg-blue-600 focus:outline-none"
              title="Chỉnh sửa"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </button>
            <button 
              @click="confirmDelete(image)"
              class="p-1 bg-red-500 text-white rounded-full hover:bg-red-600 focus:outline-none"
              title="Xóa"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
          <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2">
            <div class="text-xs">Thứ tự: {{ image.order }}</div>
          </div>
        </div>
        <div class="p-4">
          <h3 class="font-medium text-gray-900 mb-1">{{ image.alt_text || 'Không có mô tả' }}</h3>
          <p class="text-sm text-gray-500">{{ image.caption || 'Không có chú thích' }}</p>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-if="images.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Không có ảnh nào</h3>
      <p class="mt-1 text-sm text-gray-500">Bắt đầu bằng cách thêm ảnh mới cho sản phẩm.</p>
      <div class="mt-6">
        <button 
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none"
        >
          Thêm ảnh đầu tiên
        </button>
      </div>
    </div>

    <!-- Modal thêm mới -->
    <ImageForm
      v-if="showCreateModal"
      :show="showCreateModal"
      :product-id="productId"
      :on-close="closeCreateModal"
      @created="handleImageCreated"
    />

    <!-- Modal chỉnh sửa -->
    <ImageForm
      v-if="showEditModal"
      :show="showEditModal"
      :image="selectedImage"
      :product-id="productId"
      :on-close="closeEditModal"
      @updated="handleImageUpdated"
    />

    <!-- Modal xác nhận xóa -->
    <ConfirmModal
      v-if="showDeleteModal"
      :show="showDeleteModal"
      title="Xác nhận xóa"
      :message="`Bạn có chắc chắn muốn xóa ảnh này?`"
      :on-close="closeDeleteModal"
      @confirm="deleteImage"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import ImageForm from './image-form.vue'
import ConfirmModal from '@/components/Core/ConfirmModal.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'

const route = useRoute()
const productId = computed(() => route.params.id)

// State
const images = ref([])
const product = ref(null)
const selectedImage = ref(null)
const loading = ref(false)

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

// Fetch data
onMounted(async () => {
  await Promise.all([
    fetchProduct(),
    fetchImages()
  ])
})

async function fetchProduct() {
  try {
    const response = await axios.get(endpoints.products.update(productId.value))
    product.value = response.data
  } catch (error) {
    
  }
}

async function fetchImages() {
  loading.value = true
  try {
    const response = await axios.get(`/api/admin/products/${productId.value}/images`)
    images.value = response.data.data || []
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
function openEditModal(image) {
  selectedImage.value = image
  showEditModal.value = true
}
function closeEditModal() {
  showEditModal.value = false
  selectedImage.value = null
}
function confirmDelete(image) {
  selectedImage.value = image
  showDeleteModal.value = true
}
function closeDeleteModal() {
  showDeleteModal.value = false
  selectedImage.value = null
}

// Action handlers
async function handleImageCreated() {
  await fetchImages()
  closeCreateModal()
}
async function handleImageUpdated() {
  await fetchImages()
  closeEditModal()
}
async function deleteImage() {
  try {
    await axios.delete(`/api/admin/images/${selectedImage.value.id}`)
    await fetchImages()
    closeDeleteModal()
  } catch (error) {
    
  }
}
</script> 
