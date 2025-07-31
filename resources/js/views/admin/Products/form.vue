<template>
  <Modal v-model="modalVisible" :title="formTitle" size="7xl">
    <FormWrapper
      :default-values="defaultValues"
      :rules="validationRules"
      :api-errors="apiErrors"
      :submit-text="props.mode === 'edit' ? 'Cập nhật' : 'Tạo sản phẩm'"
      @submit="handleSubmit"
      @cancel="onClose"
      ref="formWrapperRef"
    >
      <template #default="{ form, errors, clearError, isSubmitting }">
        <div class="space-y-8">
          <!-- Thông tin cơ bản -->
          <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Tên sản phẩm -->
              <div class="md:col-span-2">
                <FormField
                  v-model="form.name"
                  label="Tên sản phẩm"
                  name="name"
                  :error="errors.name"
                  required
                  autocomplete="off"
                  @update:model-value="clearError('name')"
                />
              </div>



              <!-- Trạng thái -->
              <FormField
                v-model="form.status"
                label="Trạng thái"
                name="status"
                type="select"
                :options="statusOptions"
                :error="errors.status"
                @update:model-value="clearError('status')"
              />

              <!-- Mô tả ngắn -->
              <div class="md:col-span-2">
                <FormField
                  v-model="form.short_description"
                  label="Mô tả ngắn"
                  name="short_description"
                  type="textarea"
                  :error="errors.short_description"
                  required
                  autocomplete="off"
                  @update:model-value="clearError('short_description')"
                />
              </div>

              <!-- Mô tả chi tiết -->
              <div class="md:col-span-2">
                <FormField
                  v-model="form.description"
                  label="Mô tả chi tiết"
                  name="description"
                  type="textarea"
                  :error="errors.description"
                  required
                  :rows="5"
                  @update:model-value="clearError('description')"
                />
              </div>

              <!-- Giá và giá khuyến mãi -->
              <FormField
                v-model="form.price"
                label="Giá gốc"
                name="price"
                type="number"
                :error="errors.price"
                required
                :min="0"
                :step="0.01"
                @update:model-value="clearError('price')"
              />

              <FormField
                v-model="form.sale_price"
                label="Giá khuyến mãi"
                name="sale_price"
                type="number"
                :error="errors.sale_price"
                :min="0"
                :step="0.01"
                @update:model-value="clearError('sale_price')"
              />

              <!-- Thương hiệu -->
              <FormField
                v-model="form.brand_id"
                label="Thương hiệu"
                name="brand_id"
                type="select"
                :options="brandOptions"
                :error="errors.brand_id"
                placeholder="Chọn thương hiệu"
                @update:model-value="clearError('brand_id')"
              />

              <!-- Danh mục -->
              <MultipleSelect
                  v-model="form.categories" 
                :options="categoryOptions"
                label="Danh mục"
                placeholder="Chọn danh mục"
                :error="errors.categories"
              />

              <!-- Kích thước và trọng lượng -->
              <FormField
                v-model="form.weight"
                label="Trọng lượng (kg)"
                name="weight"
                type="number"
                :error="errors.weight"
                :min="0"
                :step="0.01"
                @update:model-value="clearError('weight')"
              />

              <FormField
                v-model="form.length"
                label="Chiều dài (cm)"
                name="length"
                type="number"
                :error="errors.length"
                :min="0"
                :step="0.01"
                @update:model-value="clearError('length')"
              />

              <FormField
                v-model="form.width"
                label="Chiều rộng (cm)"
                name="width"
                type="number"
                :error="errors.width"
                :min="0"
                :step="0.01"
                @update:model-value="clearError('width')"
              />

              <FormField
                v-model="form.height"
                label="Chiều cao (cm)"
                name="height"
                type="number"
                :error="errors.height"
                :min="0"
                :step="0.01"
                @update:model-value="clearError('height')"
              />
            </div>
          </div>

          <!-- Ảnh sản phẩm -->
          <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ảnh sản phẩm</h3>
            
            <!-- Ảnh chính -->
            <div class="mb-6">
              <label class="block text-sm font-medium mb-2">Ảnh chính sản phẩm</label>
              <ImageUploader
                v-model="form.image"
                :default-url="form.image"
                @update:model-value="(url) => { form.image = url; clearError('image') }"
              />
            </div>

            <!-- Ảnh phụ -->
            <div>
              <label class="block text-sm font-medium mb-2">Ảnh phụ sản phẩm</label>
              <div class="flex items-center space-x-4 mb-4">
                <div class="flex-1">
                  <input 
                    type="file" 
                    @change="handleMultipleImagesUpload" 
                    accept="image/*" 
                    multiple 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :disabled="multipleImagesUploading"
                  />
                  <p v-if="multipleImagesUploading" class="mt-1 text-sm text-blue-600">
                    Đang tải ảnh lên... ({{ uploadingCount }}/{{ totalUploading }})
                  </p>
                </div>
                <button 
                  @click="clearAllImages" 
                  type="button"
                  class="px-3 py-2 text-sm text-red-600 hover:text-red-800"
                >
                  Xóa tất cả
                </button>
              </div>
              
              <!-- Hiển thị ảnh phụ -->
              <div v-if="images.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div v-for="(image, index) in images" :key="index" class="relative group">
                  <img :src="image.url" :alt="`Ảnh phụ ${index + 1}`" class="w-full h-24 object-cover rounded border" @error="handleImageError" />
                  <button 
                    @click="removeImage(index)" 
                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity"
                  >
                    ×
                  </button>
                </div>
              </div>
            </div>
          </div>



          <!-- Thuộc tính sản phẩm chính -->
          <div class="bg-gray-50 p-6 rounded-lg">
            <!-- Bảng thuộc tính sản phẩm -->
            <div class="mb-6">
              
              <!-- Table -->
              <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                  <h3 class="text-sm font-medium text-gray-700">Thuộc tính sản phẩm</h3>
                  <button 
                    @click="addProductAttributeRow" 
                    type="button"
                    class="px-3 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none text-sm"
                  >
                    + Thêm thuộc tính
                  </button>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Thuộc tính
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Giá trị
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                        Thao tác
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(item, index) in productAttributeItems" :key="index" class="hover:bg-gray-50">
                      <td class="px-4 py-3">
                        <select 
                          v-model="item.attribute_id"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                          @change="onAttributeChange(index)"
                        >
                          <option value="">-- Chọn thuộc tính --</option>
                          <option 
                            v-for="attribute in productAttributes" 
                            :key="attribute.id" 
                            :value="attribute.id"
                          >
                            {{ attribute.name }}
                          </option>
                        </select>
                      </td>
                      <td class="px-4 py-3">
                        <!-- Nếu có giá trị định sẵn -->
                        <select 
                          v-if="getProductAttributeValues(item.attribute_id).length > 0"
                          v-model="item.value"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        >
                          <option value="">-- Chọn giá trị --</option>
                          <option 
                            v-for="value in getProductAttributeValues(item.attribute_id)" 
                            :key="value.id" 
                            :value="value.value"
                          >
                            {{ value.value }}
                          </option>
                        </select>
                        
                        <!-- Nếu không có giá trị định sẵn -->
                        <input 
                          v-else
                          v-model="item.value"
                          type="text"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                          :placeholder="`Nhập ${getProductAttributeName(item.attribute_id).toLowerCase()}`"
                        />
                      </td>
                      <td class="px-4 py-3">
                        <button 
                          @click="removeProductAttributeRow(index)" 
                          type="button"
                          class="text-red-600 hover:text-red-800 text-sm font-medium"
                          title="Xóa thuộc tính"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </td>
                    </tr>
                    
                    <!-- Empty state -->
                    <tr v-if="productAttributeItems.length === 0">
                      <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                        <p>Chưa có thuộc tính nào.</p>
                        <p class="text-sm mt-1">Click "Thêm thuộc tính" để bắt đầu.</p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <!-- Thông báo khi chưa có thuộc tính -->
              <div v-if="productAttributes.length === 0" class="text-center py-4 text-gray-500 text-sm bg-gray-50 rounded-lg mt-4">
                <p class="mb-2">Chưa có thuộc tính nào để chọn.</p>
                <p class="text-xs">Vui lòng tạo thuộc tính trong menu "Thuộc tính" trước.</p>
              </div>
            </div>
          </div>

          <!-- Biến thể sản phẩm -->
          <div class="bg-gray-50 p-6 rounded-lg">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Biến thể sản phẩm</h3>
              <button 
                @click="addVariant" 
                type="button"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none text-sm"
              >
                + Thêm biến thể
              </button>
            </div>

            <!-- Danh sách biến thể -->
            <div v-if="variants.length > 0" class="space-y-4">
              <div v-for="(variant, index) in variants" :key="index" class="border rounded-lg p-4 bg-white">
                <div class="flex items-center justify-between mb-4">
                  <h4 class="text-sm font-medium text-gray-900">Biến thể {{ index + 1 }}</h4>
                  <button 
                    @click="removeVariant(index)" 
                    type="button"
                    class="text-red-600 hover:text-red-800 text-sm"
                  >
                    Xóa
                  </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <FormField v-model="variant.sku" label="SKU" name="sku" type="text" class="text-sm" />
                  <FormField v-model="variant.barcode" label="Barcode" name="barcode" type="text" class="text-sm" />
                  <FormField v-model="variant.price" label="Giá" name="price" type="number" step="0.01" class="text-sm" />
                  <FormField v-model="variant.sale_price" label="Giá khuyến mãi" name="sale_price" type="number" step="0.01" class="text-sm" />
                  <FormField v-model="variant.quantity" label="Số lượng" name="quantity" type="number" class="text-sm" />
                  <FormField v-model="variant.status" label="Trạng thái" name="status" type="select" :options="statusOptions" class="text-sm" />
                  <div>
                    <label class="block text-sm font-medium mb-1">Ảnh biến thể</label>
                    <div class="flex items-center space-x-2">
                      <input type="file" @change="(event) => handleVariantImageUpload(event, index)" accept="image/*" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm" :disabled="variant.uploading" />
                      <img v-if="variant.image" :src="variant.image" alt="Ảnh biến thể" class="w-12 h-12 object-cover rounded border" @error="handleImageError" />
                    </div>
                  </div>
                </div>

                <!-- Thuộc tính biến thể -->
                <div class="mt-4">
                  <div class="flex items-center justify-between mb-3">
                    <h5 class="text-sm font-medium text-gray-700">Thuộc tính biến thể:</h5>
                    <button 
                      @click="addVariantAttributeRow(index)" 
                      type="button"
                      class="px-2 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700 focus:outline-none"
                    >
                      + Thêm thuộc tính
                    </button>
                  </div>
                  
                  <!-- Table thuộc tính biến thể -->
                  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thuộc tính
                          </th>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Giá trị
                          </th>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                            Thao tác
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(attrItem, attrIndex) in variant.attribute_items" :key="attrIndex" class="hover:bg-gray-50">
                          <td class="px-3 py-2">
                            <select 
                              v-model="attrItem.attribute_id"
                              class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                              @change="onVariantAttributeChange(index, attrIndex)"
                            >
                              <option value="">-- Chọn thuộc tính --</option>
                              <option 
                                v-for="attribute in attributes" 
                                :key="attribute.id" 
                                :value="attribute.id"
                              >
                                {{ attribute.name }}
                              </option>
                            </select>
                          </td>
                          <td class="px-3 py-2">
                            <!-- Nếu có giá trị định sẵn -->
                      <select 
                              v-if="getAttributeValues(attrItem.attribute_id).length > 0"
                              v-model="attrItem.value_id"
                              class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            >
                              <option value="">-- Chọn giá trị --</option>
                              <option 
                                v-for="value in getAttributeValues(attrItem.attribute_id)" 
                                :key="value.id" 
                                :value="value.id"
                              >
                          {{ value.value }}
                        </option>
                      </select>
                            
                            <!-- Nếu không có giá trị định sẵn -->
                            <input 
                              v-else
                              v-model="attrItem.value"
                              type="text"
                              class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                              :placeholder="`Nhập ${getAttributeName(attrItem.attribute_id).toLowerCase()}`"
                            />
                          </td>
                          <td class="px-3 py-2">
                            <button 
                              @click="removeVariantAttributeRow(index, attrIndex)" 
                              type="button"
                              class="text-red-600 hover:text-red-800 text-xs"
                              title="Xóa thuộc tính"
                            >
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                              </svg>
                            </button>
                          </td>
                        </tr>
                        
                        <!-- Empty state -->
                        <tr v-if="!variant.attribute_items || variant.attribute_items.length === 0">
                          <td colspan="3" class="px-3 py-4 text-center text-gray-500 text-xs">
                            <p>Chưa có thuộc tính nào.</p>
                            <p class="mt-1">Click "Thêm thuộc tính" để bắt đầu.</p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8 text-gray-500">
              <p>Chưa có biến thể nào. Hãy thêm biến thể để tạo sản phẩm có nhiều phiên bản.</p>
            </div>
          </div>
        </div>
      </template>
    </FormWrapper>
  </Modal>
</template>

<script setup>
import { ref, computed, reactive, watch, onMounted } from 'vue'
import Modal from '@/components/Core/Modal.vue'
import FormWrapper from '@/components/Core/FormWrapper.vue'
import FormField from '@/components/Core/FormField.vue'
import MultipleSelect from '@/components/Core/MultipleSelect.vue'
import endpoints from '@/api/endpoints'
import apiClient from '@/api/apiClient'
import { useFormDefaults } from '@/utils/useFormDefaults'
import ImageUploader from '@/components/Core/ImageUploader.vue'

const props = defineProps({
  show: Boolean,
  product: Object,
  apiErrors: { type: Object, default: () => ({}) },
  mode: String
})
const emit = defineEmits(['submit', 'cancel'])
const formWrapperRef = ref(null)

const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const formTitle = computed(() => props.mode === 'edit' ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới')

// Form state
const variants = ref([])
const images = ref([])

// Upload states
const multipleImagesUploading = ref(false)
const uploadingCount = ref(0)
const totalUploading = ref(0)

// Reactive data
const statusOptions = ref([])
const brands = ref([])
const categories = ref([])
const attributes = ref([])
const selectedAttributes = ref([])
const productAttributes = ref([])
const productAttributeItems = ref([])

// Default values cho form
const defaultValues = useFormDefaults(props, 'product', {
  name: '',
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
  status: '',
  attributes: {}
})

// Validation rules
const validationRules = computed(() => ({
  name: [
    { required: 'Tên sản phẩm là bắt buộc.' },
    { max: [255, 'Tên sản phẩm không được vượt quá 255 ký tự.'] }
  ],
  short_description: [
    { required: 'Mô tả ngắn là bắt buộc.' },
    { max: [500, 'Mô tả ngắn không được vượt quá 500 ký tự.'] }
  ],
  description: [
    { required: 'Mô tả chi tiết là bắt buộc.' }
  ],
  price: [
    { required: 'Giá gốc là bắt buộc.' },
    { min: [0, 'Giá gốc phải lớn hơn hoặc bằng 0.'] }
  ],
  sale_price: [
    { min: [0, 'Giá khuyến mãi phải lớn hơn hoặc bằng 0.'] }
  ]
}))

// Options cho select fields
const brandOptions = computed(() => [
  { value: '', label: 'Chọn thương hiệu' },
  ...brands.value.map(brand => ({
    value: brand.id,
    label: brand.name
  }))
])

const categoryOptions = computed(() => 
  categories.value.map(category => ({
    value: category.id,
    label: category.name
  }))
)



onMounted(() => {
  fetchStatusOptions()
  fetchBrands()
  fetchCategories()
  fetchAttributes()
  fetchProductAttributes()
})

async function fetchStatusOptions() {
  try {
    const response = await apiClient.get(endpoints.enums('BasicStatus'))
    console.log('Raw API response:', response)
    console.log('Response data type:', typeof response.data)
    console.log('Response data:', response.data)
    
    // Kiểm tra response.data có phải array không
    let dataArray = response.data
    if (!Array.isArray(dataArray)) {
      // Nếu không phải array, thử response.data.data
      if (response.data && response.data.data && Array.isArray(response.data.data)) {
        dataArray = response.data.data
      } else {
        // Fallback nếu không có data
        throw new Error('Invalid data format')
      }
    }
    
    // Convert API response to FormField format
    statusOptions.value = dataArray.map(opt => ({
      value: opt.value || opt.id,
      label: opt.label || opt.name
    }))
    console.log('Status options loaded:', statusOptions.value)
  } catch (error) {
    console.error('Error fetching status options:', error)
    // Fallback với format đúng cho FormField
    statusOptions.value = [
      { value: 'active', label: 'Hoạt động' },
      { value: 'inactive', label: 'Không hoạt động' }
    ]
    console.log('Using fallback status options:', statusOptions.value)
  }
}

async function fetchBrands() {
  try {
    const response = await apiClient.get(endpoints.brands.list)
    brands.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching brands:', error)
    brands.value = []
  }
}

async function fetchCategories() {
  try {
    const response = await apiClient.get(endpoints.categories.list)
    categories.value = response.data.data || []
    console.log('Fetched categories:', categories.value)
  } catch (error) {
    console.error('Error fetching categories:', error)
    categories.value = []
  }
}

async function fetchAttributes() {
  try {
    console.log('Fetching attributes...')
    const response = await apiClient.get(`${endpoints.attributes.list}?relations=values`)
    console.log('Attributes response:', response.data)
    attributes.value = response.data.data || []
    console.log('Attributes loaded:', attributes.value.length)
  } catch (error) {
    console.error('Error fetching attributes:', error)
    console.error('Error details:', error.response?.data)
    attributes.value = []
  }
}

async function fetchProductAttributes() {
  try {
    console.log('Fetching product attributes...')
    const response = await apiClient.get(`${endpoints.attributes.list}?relations=values`)
    console.log('Product attributes response:', response.data)
    productAttributes.value = response.data.data || []
    console.log('Product attributes loaded:', productAttributes.value.length)
  } catch (error) {
    console.error('Error fetching product attributes:', error)
    console.error('Error details:', error.response?.data)
    productAttributes.value = []
  }
}

// Helper functions for attributes
function getAttributeName(attributeId) {
  const attribute = attributes.value.find(attr => attr.id == attributeId)
  return attribute ? attribute.name : ''
}

function getAttributeValues(attributeId) {
  const attribute = attributes.value.find(attr => attr.id == attributeId)
  return attribute ? attribute.attribute_values || [] : []
}

// Product attribute functions
function getProductAttributeName(attributeId) {
  const attribute = productAttributes.value.find(attr => attr.id == attributeId)
  return attribute ? attribute.name : ''
}

function getProductAttributeValues(attributeId) {
  const attribute = productAttributes.value.find(attr => attr.id == attributeId)
  return attribute ? attribute.attribute_values || [] : []
}

function addProductAttributeRow() {
  productAttributeItems.value.push({
    attribute_id: '',
    value: ''
  })
}

function removeProductAttributeRow(index) {
  productAttributeItems.value.splice(index, 1)
}

function onAttributeChange(index) {
  // Reset value khi thay đổi attribute
  productAttributeItems.value[index].value = ''
}

// Variant attribute functions
function addVariantAttributeRow(variantIndex) {
  if (!variants.value[variantIndex].attribute_items) {
    variants.value[variantIndex].attribute_items = []
  }
  variants.value[variantIndex].attribute_items.push({
    attribute_id: '',
    value_id: '',
    value: ''
  })
}

function removeVariantAttributeRow(variantIndex, attrIndex) {
  variants.value[variantIndex].attribute_items.splice(attrIndex, 1)
}

function onVariantAttributeChange(variantIndex, attrIndex) {
  // Reset value khi thay đổi attribute
  const attrItem = variants.value[variantIndex].attribute_items[attrIndex]
  attrItem.value_id = ''
  attrItem.value = ''
}



// Watch selectedAttributes to update all variants
watch(selectedAttributes, (newSelected) => {
  // Reset attribute_values for all variants when selected attributes change
  variants.value.forEach(variant => {
    // Keep only selected attributes
    const newAttributeValues = {}
    newSelected.forEach(attrId => {
      if (variant.attribute_values[attrId]) {
        newAttributeValues[attrId] = variant.attribute_values[attrId]
      }
    })
    variant.attribute_values = newAttributeValues
  })
})

watch(() => props.product, (val) => {
  if (val) {
    // Load variants and images if editing
    if (val.variants) {
      variants.value = val.variants.map(v => ({
        sku: v.sku || '',
        barcode: v.barcode || '',
        price: v.price || '',
        sale_price: v.sale_price || '',
        quantity: v.quantity || 0,
        image: v.image || '',
        status: v.status || 'active', // Ensure status is present
        attribute_values: v.attribute_values ? v.attribute_values.reduce((acc, av) => {
          acc[av.attribute_id] = av.id
          return acc
        }, {}) : {},
        attribute_items: v.attribute_values ? v.attribute_values.map(av => ({
          attribute_id: av.attribute_id,
          value_id: av.id,
          value: ''
        })) : []
      }))
      
      // Set selected attributes based on existing variants
      const usedAttributeIds = new Set()
      val.variants.forEach(v => {
        if (v.attribute_values) {
          v.attribute_values.forEach(av => {
            usedAttributeIds.add(av.attribute_id)
          })
        }
      })
      selectedAttributes.value = Array.from(usedAttributeIds)
    }
    
    if (val.images) {
      images.value = val.images.map(img => ({
        url: img.url || '',
        uploading: false
      }))
    }
    
    // Load product attributes if editing
    if (val.attributes) {
      // Chuyển đổi attributes object thành productAttributeItems
      Object.keys(val.attributes).forEach(key => {
        const attribute = productAttributes.value.find(attr => attr.name === key)
        if (attribute) {
          productAttributeItems.value.push({
            attribute_id: attribute.id,
            value: val.attributes[key]
          })
        }
      })
    }
  } else {
    resetForm()
  }
}, { immediate: true })

function resetForm() {
  variants.value = []
  images.value = []
  selectedAttributes.value = []
  productAttributeItems.value = []
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
    status: 'active', // Default status
    attribute_values: {},
    attribute_items: [],
    uploading: false
  })
}

function removeVariant(index) {
  variants.value.splice(index, 1)
}

// Image functions
function removeImage(index) {
  images.value.splice(index, 1)
}

function handleImageError(event) {
  event.target.style.display = 'none'
}

// Upload functions
async function handleVariantImageUpload(event, index) {
  const file = event.target.files[0]
  if (!file) return
  
  variants.value[index].uploading = true
  try {
    const formData = new FormData()
    formData.append('image', file)
    
    const response = await apiClient.post('/api/admin/images/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    // Sử dụng URL thay vì dataUrl để tránh base64
    variants.value[index].image = response.data.url || response.data.full_url
  } catch (error) {
    console.error('Upload error:', error)
    alert('Lỗi khi tải ảnh lên')
  } finally {
    variants.value[index].uploading = false
    event.target.value = '' // Reset input
  }
}

async function handleMultipleImagesUpload(event) {
  const files = Array.from(event.target.files)
  if (files.length === 0) return
  
  multipleImagesUploading.value = true
  totalUploading.value = files.length
  uploadingCount.value = 0
  
  try {
    for (const file of files) {
      const uploadFormData = new FormData()
      uploadFormData.append('image', file)
      
      const response = await apiClient.post('/api/admin/images/upload', uploadFormData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      console.log('Upload response:', response.data)
      
      // Sử dụng URL thay vì dataUrl để tránh base64
      images.value.push({
        url: response.data.url || response.data.full_url,
        uploading: false
      })
      
      uploadingCount.value++
    }
  } catch (error) {
    console.error('Upload error:', error)
    alert('Lỗi khi tải ảnh lên')
  } finally {
    multipleImagesUploading.value = false
    uploadingCount.value = 0
    totalUploading.value = 0
    event.target.value = '' // Reset input
  }
}

function clearAllImages() {
  images.value = []
}

function handleSubmit(formData) {
  // Debug: Kiểm tra categories
  console.log('Form data before submit:', formData)
  console.log('Categories type:', typeof formData.categories)
  console.log('Categories value:', formData.categories)
  
  // Chuyển đổi productAttributeItems thành attributes object
  const cleanAttributes = {}
  productAttributeItems.value.forEach(item => {
    if (item.value && item.value.trim() !== '') {
      const attributeName = getProductAttributeName(item.attribute_id)
      cleanAttributes[attributeName] = item.value
    }
  })
  
  // Thêm variants và images vào formData
  const submitData = {
    ...formData,
    attributes: Object.keys(cleanAttributes).length > 0 ? cleanAttributes : null,
    categories: Array.isArray(formData.categories) ? formData.categories : [],
    variants: variants.value.map(variant => {
      const cleanVariant = { ...variant }
      // Loại bỏ các field không cần thiết
      delete cleanVariant.uploading
      // Nếu image empty thì không gửi
      if (!cleanVariant.image || cleanVariant.image.trim() === '') {
        delete cleanVariant.image
      }
      
      // Chuyển đổi attribute_items thành attribute_values
      if (cleanVariant.attribute_items && cleanVariant.attribute_items.length > 0) {
        cleanVariant.attribute_values = {}
        cleanVariant.attribute_items.forEach(item => {
          if (item.attribute_id && (item.value_id || item.value)) {
            cleanVariant.attribute_values[item.attribute_id] = item.value_id || item.value
          }
        })
      }
      
      // Xóa attribute_items vì không cần gửi lên server
      delete cleanVariant.attribute_items
      
      return cleanVariant
    }),
    images: images.value.map(img => ({ url: img.url }))
  }
  
  console.log('Submit data:', submitData)
  
  emit('submit', submitData)
}

function onClose() {
  emit('cancel')
}
</script> 
