<template>
  <Modal v-model="modalVisible" :title="formTitle" size="xl">
    <div class="space-y-6">
      <!-- Tabs -->
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
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
            Biến thể ({{ variants.length }})
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
            Ảnh sản phẩm ({{ images.length }})
          </button>
        </nav>
      </div>

      <!-- Tab content -->
      <div v-if="activeTab === 'basic'" class="space-y-4">
        <!-- Tên sản phẩm -->
        <div>
          <label class="block text-sm font-medium mb-1">Tên sản phẩm <span class="text-red-500">*</span></label>
          <input v-model="formData.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.name || apiErrors.name }" maxlength="255" />
          <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600">{{ validationErrors.name }}</p>
          <p v-else-if="apiErrors.name" class="mt-1 text-sm text-red-600">{{ apiErrors.name }}</p>
        </div>

        <!-- Slug -->
        <div>
          <label class="block text-sm font-medium mb-1">Slug</label>
          <input v-model="formData.slug" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.slug || apiErrors.slug }" maxlength="255" />
          <p v-if="validationErrors.slug" class="mt-1 text-sm text-red-600">{{ validationErrors.slug }}</p>
          <p v-else-if="apiErrors.slug" class="mt-1 text-sm text-red-600">{{ apiErrors.slug }}</p>
        </div>

        <!-- Mô tả ngắn -->
        <div>
          <label class="block text-sm font-medium mb-1">Mô tả ngắn <span class="text-red-500">*</span></label>
          <textarea v-model="formData.short_description" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.short_description || apiErrors.short_description }" maxlength="500" rows="3"></textarea>
          <p v-if="validationErrors.short_description" class="mt-1 text-sm text-red-600">{{ validationErrors.short_description }}</p>
          <p v-else-if="apiErrors.short_description" class="mt-1 text-sm text-red-600">{{ apiErrors.short_description }}</p>
        </div>

        <!-- Mô tả chi tiết -->
        <div>
          <label class="block text-sm font-medium mb-1">Mô tả chi tiết <span class="text-red-500">*</span></label>
          <textarea v-model="formData.description" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.description || apiErrors.description }" rows="5"></textarea>
          <p v-if="validationErrors.description" class="mt-1 text-sm text-red-600">{{ validationErrors.description }}</p>
          <p v-else-if="apiErrors.description" class="mt-1 text-sm text-red-600">{{ apiErrors.description }}</p>
        </div>

        <!-- Giá và giá khuyến mãi -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Giá gốc <span class="text-red-500">*</span></label>
            <input v-model="formData.price" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.price || apiErrors.price }" />
            <p v-if="validationErrors.price" class="mt-1 text-sm text-red-600">{{ validationErrors.price }}</p>
            <p v-else-if="apiErrors.price" class="mt-1 text-sm text-red-600">{{ apiErrors.price }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Giá khuyến mãi</label>
            <input v-model="formData.sale_price" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.sale_price || apiErrors.sale_price }" />
            <p v-if="validationErrors.sale_price" class="mt-1 text-sm text-red-600">{{ validationErrors.sale_price }}</p>
            <p v-else-if="apiErrors.sale_price" class="mt-1 text-sm text-red-600">{{ apiErrors.sale_price }}</p>
          </div>
        </div>

        <!-- Thương hiệu -->
        <div>
          <label class="block text-sm font-medium mb-1">Thương hiệu</label>
          <select v-model="formData.brand_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.brand_id || apiErrors.brand_id }">
            <option value="">Chọn thương hiệu</option>
            <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
          </select>
          <p v-if="validationErrors.brand_id" class="mt-1 text-sm text-red-600">{{ validationErrors.brand_id }}</p>
          <p v-else-if="apiErrors.brand_id" class="mt-1 text-sm text-red-600">{{ apiErrors.brand_id }}</p>
        </div>

        <!-- Danh mục -->
        <div>
          <label class="block text-sm font-medium mb-1">Danh mục</label>
          <select v-model="formData.categories" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.categories || apiErrors.categories }">
            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
          </select>
          <p v-if="validationErrors.categories" class="mt-1 text-sm text-red-600">{{ validationErrors.categories }}</p>
          <p v-else-if="apiErrors.categories" class="mt-1 text-sm text-red-600">{{ apiErrors.categories }}</p>
        </div>

        <!-- Kích thước và trọng lượng -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Trọng lượng (kg) <span class="text-red-500">*</span></label>
            <input v-model="formData.weight" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.weight || apiErrors.weight }" />
            <p v-if="validationErrors.weight" class="mt-1 text-sm text-red-600">{{ validationErrors.weight }}</p>
            <p v-else-if="apiErrors.weight" class="mt-1 text-sm text-red-600">{{ apiErrors.weight }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Chiều dài (cm) <span class="text-red-500">*</span></label>
            <input v-model="formData.length" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.length || apiErrors.length }" />
            <p v-if="validationErrors.length" class="mt-1 text-sm text-red-600">{{ validationErrors.length }}</p>
            <p v-else-if="apiErrors.length" class="mt-1 text-sm text-red-600">{{ apiErrors.length }}</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Chiều rộng (cm) <span class="text-red-500">*</span></label>
            <input v-model="formData.width" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.width || apiErrors.width }" />
            <p v-if="validationErrors.width" class="mt-1 text-sm text-red-600">{{ validationErrors.width }}</p>
            <p v-else-if="apiErrors.width" class="mt-1 text-sm text-red-600">{{ apiErrors.width }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Chiều cao (cm) <span class="text-red-500">*</span></label>
            <input v-model="formData.height" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.height || apiErrors.height }" />
            <p v-if="validationErrors.height" class="mt-1 text-sm text-red-600">{{ validationErrors.height }}</p>
            <p v-else-if="apiErrors.height" class="mt-1 text-sm text-red-600">{{ apiErrors.height }}</p>
          </div>
        </div>

        <!-- Ảnh sản phẩm -->
        <div>
          <label class="block text-sm font-medium mb-1">Ảnh sản phẩm (URL) <span class="text-red-500">*</span></label>
          <input v-model="formData.image" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.image || apiErrors.image }" maxlength="255" />
          <p v-if="validationErrors.image" class="mt-1 text-sm text-red-600">{{ validationErrors.image }}</p>
          <p v-else-if="apiErrors.image" class="mt-1 text-sm text-red-600">{{ apiErrors.image }}</p>
        </div>

        <!-- Trạng thái -->
        <div>
          <label class="block text-sm font-medium mb-1">Trạng thái <span class="text-red-500">*</span></label>
          <select v-model="formData.status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': validationErrors.status || apiErrors.status }">
            <option v-for="(label, value) in statusOptions" :key="value" :value="value">{{ label }}</option>
          </select>
          <p v-if="validationErrors.status" class="mt-1 text-sm text-red-600">{{ validationErrors.status }}</p>
          <p v-else-if="apiErrors.status" class="mt-1 text-sm text-red-600">{{ apiErrors.status }}</p>
        </div>
      </div>

      <!-- Variants tab -->
      <div v-if="activeTab === 'variants'" class="space-y-4">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-medium">Quản lý biến thể sản phẩm</h3>
          <button 
            @click="addVariant" 
            type="button"
            class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none text-sm"
          >
            + Thêm biến thể
          </button>
        </div>

        <!-- Variants list -->
        <div class="space-y-4">
          <div 
            v-for="(variant, index) in variants" 
            :key="index" 
            class="border rounded-lg p-4 bg-gray-50"
          >
            <div class="flex justify-between items-center mb-3">
              <h4 class="font-medium">Biến thể #{{ index + 1 }}</h4>
              <button 
                @click="removeVariant(index)" 
                type="button"
                class="text-red-600 hover:text-red-800"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1">SKU <span class="text-red-500">*</span></label>
                <input v-model="variant.sku" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" maxlength="100" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Barcode</label>
                <input v-model="variant.barcode" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" maxlength="100" />
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-4">
              <div>
                <label class="block text-sm font-medium mb-1">Giá <span class="text-red-500">*</span></label>
                <input v-model="variant.price" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Giá KM</label>
                <input v-model="variant.sale_price" type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Số lượng <span class="text-red-500">*</span></label>
                <input v-model="variant.quantity" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
              </div>
            </div>

            <!-- Attributes -->
            <div class="mt-4">
              <label class="block text-sm font-medium mb-2">Thuộc tính</label>
              <div class="grid grid-cols-2 gap-4">
                <div v-for="attribute in attributes" :key="attribute.id">
                  <label class="block text-sm font-medium mb-1">{{ attribute.name }}</label>
                  <select v-model="variant.attribute_values[attribute.id]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Chọn {{ attribute.name }}</option>
                    <option v-for="value in attribute.values" :key="value.id" :value="value.id">
                      {{ value.value }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <div class="mt-4">
              <label class="block text-sm font-medium mb-1">Ảnh biến thể</label>
              <input v-model="variant.image" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" maxlength="255" />
            </div>
          </div>

          <div v-if="variants.length === 0" class="text-center py-8 text-gray-500">
            <p>Chưa có biến thể nào. Nhấn "Thêm biến thể" để bắt đầu.</p>
          </div>
        </div>
      </div>

      <!-- Images tab -->
      <div v-if="activeTab === 'images'" class="space-y-4">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-medium">Quản lý ảnh sản phẩm</h3>
          <button 
            @click="addImage" 
            type="button"
            class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none text-sm"
          >
            + Thêm ảnh
          </button>
        </div>

        <!-- Images list -->
        <div class="space-y-4">
          <div 
            v-for="(image, index) in images" 
            :key="index" 
            class="border rounded-lg p-4 bg-gray-50"
          >
            <div class="flex justify-between items-center mb-3">
              <h4 class="font-medium">Ảnh #{{ index + 1 }}</h4>
              <button 
                @click="removeImage(index)" 
                type="button"
                class="text-red-600 hover:text-red-800"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1">URL ảnh <span class="text-red-500">*</span></label>
                <input v-model="image.url" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" maxlength="500" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Thứ tự</label>
                <input v-model="image.order" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" min="0" />
              </div>
            </div>

            <div class="mt-4">
              <label class="block text-sm font-medium mb-1">Mô tả ảnh (Alt text)</label>
              <input v-model="image.alt_text" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" maxlength="255" />
            </div>

            <div class="mt-4">
              <label class="block text-sm font-medium mb-1">Chú thích</label>
              <textarea v-model="image.caption" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" rows="2" maxlength="500"></textarea>
            </div>

            <!-- Preview -->
            <div v-if="image.url" class="mt-4">
              <label class="block text-sm font-medium mb-2">Xem trước:</label>
              <img :src="image.url" :alt="image.alt_text || 'Preview'" class="w-32 h-32 object-cover rounded border" @error="handleImageError" />
            </div>
          </div>

          <div v-if="images.length === 0" class="text-center py-8 text-gray-500">
            <p>Chưa có ảnh nào. Nhấn "Thêm ảnh" để bắt đầu.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Buttons -->
    <div class="flex justify-end space-x-3 pt-4 border-t">
      <button type="button" @click="onClose" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">Huỷ</button>
      <button type="button" @click="validateAndSubmit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none" :disabled="isSubmitting">{{ isSubmitting ? 'Đang xử lý...' : (mode === 'edit' ? 'Cập nhật' : 'Thêm mới') }}</button>
    </div>
  </Modal>
</template>

<script setup>
import { ref, computed, reactive, watch, onMounted } from 'vue'
import Modal from '@/components/Core/Modal.vue'
import endpoints from '@/api/endpoints'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  product: Object,
  apiErrors: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])

const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới')

// Tab state
const activeTab = ref('basic')

// Form data
const formData = reactive({
  name: '',
  slug: '',
  short_description: '',
  description: '',
  price: '',
  sale_price: '',
  brand_id: '',
  categories: [],
  weight: '',
  length: '',
  width: '',
  height: '',
  image: '',
  status: ''
})

// Variants and images
const variants = ref([])
const images = ref([])

const validationErrors = reactive({})
const isSubmitting = ref(false)
const statusOptions = ref({})
const brands = ref([])
const categories = ref([])
const attributes = ref([])

onMounted(() => {
  fetchStatusOptions()
  fetchBrands()
  fetchCategories()
  fetchAttributes()
})

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

async function fetchBrands() {
  try {
    const response = await axios.get(endpoints.brands.list)
    brands.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching brands:', error)
    brands.value = []
  }
}

async function fetchCategories() {
  try {
    const response = await axios.get(endpoints.categories.list)
    categories.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching categories:', error)
    categories.value = []
  }
}

async function fetchAttributes() {
  try {
    const response = await axios.get(endpoints.attributes.list)
    attributes.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching attributes:', error)
    attributes.value = []
  }
}

watch(() => props.product, (val) => {
  if (val) {
    formData.name = val.name || ''
    formData.slug = val.slug || ''
    formData.short_description = val.short_description || ''
    formData.description = val.description || ''
    formData.price = val.price || ''
    formData.sale_price = val.sale_price || ''
    formData.brand_id = val.brand_id || ''
    formData.categories = val.categories ? val.categories.map(c => c.id) : []
    formData.weight = val.weight || ''
    formData.length = val.length || ''
    formData.width = val.width || ''
    formData.height = val.height || ''
    formData.image = val.image || ''
    formData.status = val.status || Object.keys(statusOptions.value)[0] || ''
    
    // Load variants and images if editing
    if (val.variants) {
      variants.value = val.variants.map(v => ({
        sku: v.sku || '',
        barcode: v.barcode || '',
        price: v.price || '',
        sale_price: v.sale_price || '',
        quantity: v.quantity || 0,
        image: v.image || '',
        attribute_values: v.attribute_values ? v.attribute_values.reduce((acc, av) => {
          acc[av.attribute_id] = av.id
          return acc
        }, {}) : {}
      }))
    }
    
    if (val.images) {
      images.value = val.images.map(img => ({
        url: img.url || '',
        alt_text: img.alt_text || '',
        caption: img.caption || '',
        order: img.order || 0
      }))
    }
  } else {
    resetForm()
  }
}, { immediate: true })

function resetForm() {
  formData.name = ''
  formData.slug = ''
  formData.short_description = ''
  formData.description = ''
  formData.price = ''
  formData.sale_price = ''
  formData.brand_id = ''
  formData.categories = []
  formData.weight = ''
  formData.length = ''
  formData.width = ''
  formData.height = ''
  formData.image = ''
  formData.status = Object.keys(statusOptions.value)[0] || ''
  variants.value = []
  images.value = []
  clearErrors()
}

function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}

// Variant functions
function addVariant() {
  variants.value.push({
    sku: '',
    barcode: '',
    price: '',
    sale_price: '',
    quantity: 0,
    image: '',
    attribute_values: {}
  })
}

function removeVariant(index) {
  variants.value.splice(index, 1)
}

// Image functions
function addImage() {
  images.value.push({
    url: '',
    alt_text: '',
    caption: '',
    order: images.value.length
  })
}

function removeImage(index) {
  images.value.splice(index, 1)
}

function handleImageError(event) {
  event.target.style.display = 'none'
}

function validateForm() {
  clearErrors()
  
  // Basic validation
  if (!formData.name.trim()) {
    validationErrors.name = 'Tên sản phẩm là bắt buộc'
  } else if (formData.name.length > 255) {
    validationErrors.name = 'Tên sản phẩm không được vượt quá 255 ký tự'
  }

  if (!formData.short_description.trim()) {
    validationErrors.short_description = 'Mô tả ngắn là bắt buộc'
  } else if (formData.short_description.length > 500) {
    validationErrors.short_description = 'Mô tả ngắn không được vượt quá 500 ký tự'
  }

  if (!formData.description.trim()) {
    validationErrors.description = 'Mô tả chi tiết là bắt buộc'
  }

  if (!formData.price || isNaN(formData.price)) {
    validationErrors.price = 'Giá là bắt buộc và phải là số'
  } else if (Number(formData.price) < 0) {
    validationErrors.price = 'Giá không được âm'
  }

  if (formData.sale_price && (isNaN(formData.sale_price) || Number(formData.sale_price) < 0)) {
    validationErrors.sale_price = 'Giá khuyến mãi phải là số và không được âm'
  }

  if (!formData.weight || isNaN(formData.weight)) {
    validationErrors.weight = 'Trọng lượng là bắt buộc và phải là số'
  } else if (Number(formData.weight) < 0) {
    validationErrors.weight = 'Trọng lượng không được âm'
  }

  if (!formData.length || isNaN(formData.length)) {
    validationErrors.length = 'Chiều dài là bắt buộc và phải là số'
  } else if (Number(formData.length) < 0) {
    validationErrors.length = 'Chiều dài không được âm'
  }

  if (!formData.width || isNaN(formData.width)) {
    validationErrors.width = 'Chiều rộng là bắt buộc và phải là số'
  } else if (Number(formData.width) < 0) {
    validationErrors.width = 'Chiều rộng không được âm'
  }

  if (!formData.height || isNaN(formData.height)) {
    validationErrors.height = 'Chiều cao là bắt buộc và phải là số'
  } else if (Number(formData.height) < 0) {
    validationErrors.height = 'Chiều cao không được âm'
  }

  if (!formData.image.trim()) {
    validationErrors.image = 'Ảnh sản phẩm là bắt buộc'
  } else if (formData.image.length > 255) {
    validationErrors.image = 'Đường dẫn ảnh không được vượt quá 255 ký tự'
  }

  // Validate variants
  variants.value.forEach((variant, index) => {
    if (!variant.sku.trim()) {
      validationErrors[`variants.${index}.sku`] = 'SKU là bắt buộc'
    }
    if (!variant.price || isNaN(variant.price)) {
      validationErrors[`variants.${index}.price`] = 'Giá là bắt buộc và phải là số'
    }
    if (!variant.quantity || isNaN(variant.quantity)) {
      validationErrors[`variants.${index}.quantity`] = 'Số lượng là bắt buộc và phải là số'
    }
  })

  // Validate images
  images.value.forEach((image, index) => {
    if (!image.url.trim()) {
      validationErrors[`images.${index}.url`] = 'URL ảnh là bắt buộc'
    }
  })

  return Object.keys(validationErrors).length === 0
}

function validateAndSubmit() {
  if (!validateForm()) return
  isSubmitting.value = true
  try {
    const submitData = new FormData()
    
    // Add basic product data
    Object.entries(formData).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        if (Array.isArray(value)) {
          value.forEach(v => submitData.append(key + '[]', v))
        } else {
          submitData.append(key, value)
        }
      }
    })
    
    // Add variants
    variants.value.forEach((variant, index) => {
      submitData.append(`variants[${index}][sku]`, variant.sku)
      submitData.append(`variants[${index}][barcode]`, variant.barcode || '')
      submitData.append(`variants[${index}][price]`, variant.price)
      submitData.append(`variants[${index}][sale_price]`, variant.sale_price || '')
      submitData.append(`variants[${index}][quantity]`, variant.quantity)
      submitData.append(`variants[${index}][image]`, variant.image || '')
      submitData.append(`variants[${index}][status]`, 'active')
      
      // Add attribute values
      Object.entries(variant.attribute_values).forEach(([attrId, valueId]) => {
        if (valueId) {
          submitData.append(`variants[${index}][attribute_values][${attrId}]`, valueId)
        }
      })
    })
    
    // Add images
    images.value.forEach((image, index) => {
      submitData.append(`images[${index}][url]`, image.url)
      submitData.append(`images[${index}][alt_text]`, image.alt_text || '')
      submitData.append(`images[${index}][caption]`, image.caption || '')
      submitData.append(`images[${index}][order]`, image.order || 0)
    })
    
    emit('submit', submitData)
  } finally {
    isSubmitting.value = false
  }
}

function onClose() {
  emit('cancel')
}
</script> 
