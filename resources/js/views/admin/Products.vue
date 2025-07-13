<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
  <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Quản lý sản phẩm</h1>
          <p class="text-gray-600">Quản lý danh mục sản phẩm và kho hàng</p>
        </div>
      <button 
        @click="showAddModal = true"
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
        >
          <PlusIcon class="w-5 h-5" />
          <span>Thêm sản phẩm</span>
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
            <div class="flex items-center mt-2">
              <span 
                :class="[
                  'text-sm font-medium',
                  stat.change >= 0 ? 'text-green-600' : 'text-red-600'
                ]"
              >
                {{ stat.change >= 0 ? '+' : '' }}{{ stat.change }}%
              </span>
              <span class="text-gray-500 text-sm ml-1">so với tháng trước</span>
            </div>
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
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Search -->
        <div class="relative lg:col-span-2">
          <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
        <input 
          v-model="filters.search"
          type="text" 
          placeholder="Tìm kiếm sản phẩm..."
            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
          />
        </div>

        <!-- Status Filter -->
        <select 
          v-model="filters.status"
          class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
        >
          <option value="">Tất cả trạng thái</option>
          <option value="active">Đang bán</option>
          <option value="inactive">Ngừng bán</option>
          <option value="draft">Bản nháp</option>
        </select>

        <!-- Brand Filter -->
        <select 
          v-model="filters.brand"
          class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
        >
          <option value="">Tất cả thương hiệu</option>
          <option value="apple">Apple</option>
          <option value="samsung">Samsung</option>
          <option value="xiaomi">Xiaomi</option>
          <option value="oppo">OPPO</option>
        </select>

        <!-- Category Filter -->
        <select 
          v-model="filters.category"
          class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
        >
          <option value="">Tất cả danh mục</option>
          <option value="smartphone">Điện thoại</option>
          <option value="laptop">Laptop</option>
          <option value="tablet">Máy tính bảng</option>
          <option value="accessory">Phụ kiện</option>
        </select>
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
            <Squares2X2Icon class="w-5 h-5" />
          </button>
          <button 
            @click="viewMode = 'list'"
            :class="[
              'p-2 rounded-lg transition-all duration-200',
              viewMode === 'list' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            ]"
          >
            <ListBulletIcon class="w-5 h-5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Products Grid/List -->
    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
      <div 
        v-for="product in paginatedProducts" 
        :key="product.id"
        class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transform hover:scale-105 transition-all duration-300"
      >
        <!-- Product Image -->
        <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200">
          <img 
            :src="product.image" 
            :alt="product.name"
            class="w-full h-full object-cover"
          />
          <div class="absolute top-3 right-3">
            <span 
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="getStatusClass(product.status)"
            >
              {{ getStatusLabel(product.status) }}
            </span>
          </div>
          <div class="absolute top-3 left-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              {{ product.category }}
            </span>
          </div>
        </div>

        <!-- Product Info -->
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ product.name }}</h3>
          <p class="text-sm text-gray-600 mb-3">{{ product.sku }}</p>
          
          <div class="flex items-center justify-between mb-4">
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(product.price) }}</p>
              <p v-if="product.original_price > product.price" class="text-sm text-gray-500 line-through">
                {{ formatCurrency(product.original_price) }}
              </p>
            </div>
            <div class="text-right">
              <p class="text-sm font-medium text-gray-900">{{ product.stock }} cái</p>
              <p class="text-xs text-gray-500">Tồn kho</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center space-x-2">
            <button 
              @click="viewProduct(product)"
              class="flex-1 bg-indigo-50 text-indigo-600 py-2 px-3 rounded-lg hover:bg-indigo-100 transition-all duration-200 text-sm font-medium"
            >
              <EyeIcon class="w-4 h-4 inline mr-1" />
              Xem
            </button>
            <button 
              @click="editProduct(product)"
              class="flex-1 bg-blue-50 text-blue-600 py-2 px-3 rounded-lg hover:bg-blue-100 transition-all duration-200 text-sm font-medium"
            >
              <PencilIcon class="w-4 h-4 inline mr-1" />
              Sửa
            </button>
            <button 
              @click="duplicateProduct(product)"
              class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-all duration-200"
              title="Nhân bản"
            >
              <DocumentDuplicateIcon class="w-4 h-4" />
            </button>
            <button 
              @click="deleteProduct(product)"
              class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all duration-200"
              title="Xóa"
            >
              <TrashIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Products List View -->
    <div v-else class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tồn kho</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr 
              v-for="product in paginatedProducts" 
              :key="product.id"
              class="hover:bg-gray-50 transition-colors duration-200"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <img 
                    :src="product.image" 
                      :alt="product.name"
                    class="w-12 h-12 rounded-lg object-cover mr-4"
                  />
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                    <div class="text-sm text-gray-500">{{ product.brand }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.sku }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ formatCurrency(product.price) }}</div>
                <div v-if="product.original_price > product.price" class="text-xs text-gray-500 line-through">
                  {{ formatCurrency(product.original_price) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ product.stock }}</div>
                <div class="text-xs text-gray-500">cái</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getStatusClass(product.status)"
                >
                  {{ getStatusLabel(product.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <button 
                    @click="viewProduct(product)"
                    class="text-indigo-600 hover:text-indigo-900 p-2 rounded-lg hover:bg-indigo-50 transition-all duration-200"
                    title="Xem chi tiết"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </button>
                <button 
                  @click="editProduct(product)"
                    class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-all duration-200"
                    title="Chỉnh sửa"
                  >
                    <PencilIcon class="w-4 h-4" />
                  </button>
                  <button 
                    @click="duplicateProduct(product)"
                    class="text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-50 transition-all duration-200"
                    title="Nhân bản"
                  >
                    <DocumentDuplicateIcon class="w-4 h-4" />
                </button>
                <button 
                    @click="deleteProduct(product)"
                    class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-all duration-200"
                    title="Xóa"
                >
                    <TrashIcon class="w-4 h-4" />
                </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="bg-white rounded-2xl shadow-lg px-6 py-4 border border-gray-100">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Hiển thị {{ startIndex + 1 }} đến {{ endIndex }} trong tổng số {{ filteredProducts.length }} sản phẩm
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

    <!-- Add/Edit Product Modal -->
    <div 
      v-if="showAddModal || showEditModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ showEditModal ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới' }}
          </h3>
        </div>
        
        <form @submit.prevent="saveProduct" class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Tên sản phẩm</label>
                <input 
                v-model="productForm.name"
                  type="text" 
                  required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
              />
              </div>
            
              <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                <input 
                v-model="productForm.sku"
                  type="text" 
                  required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Thương hiệu</label>
              <select 
                v-model="productForm.brand"
                required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
              >
                <option value="">Chọn thương hiệu</option>
                <option value="apple">Apple</option>
                <option value="samsung">Samsung</option>
                <option value="xiaomi">Xiaomi</option>
                <option value="oppo">OPPO</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
              <select 
                v-model="productForm.category"
                required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
              >
                <option value="">Chọn danh mục</option>
                <option value="smartphone">Điện thoại</option>
                <option value="laptop">Laptop</option>
                <option value="tablet">Máy tính bảng</option>
                <option value="accessory">Phụ kiện</option>
              </select>
              </div>
            
              <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Giá bán</label>
                <input 
                v-model="productForm.price"
                  type="number" 
                  required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Giá gốc</label>
              <input 
                v-model="productForm.original_price"
                type="number"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
              />
              </div>
            
              <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Số lượng tồn kho</label>
                <input 
                v-model="productForm.stock"
                type="number"
                required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
              />
              </div>
            
              <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select 
                v-model="productForm.status"
                required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
              >
                <option value="active">Đang bán</option>
                <option value="inactive">Ngừng bán</option>
                <option value="draft">Bản nháp</option>
                </select>
              </div>
            </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh URL</label>
            <input 
              v-model="productForm.image"
              type="url"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
            <textarea 
              v-model="productForm.description"
              rows="3"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
            ></textarea>
          </div>
        </form>
        
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
              <button 
                @click="closeModal"
            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all duration-200"
              >
                Hủy
              </button>
              <button 
            @click="saveProduct"
            class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200"
              >
                {{ showEditModal ? 'Cập nhật' : 'Thêm' }}
              </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { 
  PlusIcon, 
  MagnifyingGlassIcon,
  Squares2X2Icon,
  ListBulletIcon,
  EyeIcon,
  PencilIcon,
  DocumentDuplicateIcon,
  TrashIcon,
  CubeIcon,
  ShoppingBagIcon,
  ExclamationTriangleIcon,
  CurrencyDollarIcon
} from '@heroicons/vue/24/outline'

// Stats data
const stats = ref([
  {
    title: 'Tổng sản phẩm',
    value: '2,456',
    change: 15.2,
    icon: CubeIcon,
    bgColor: 'bg-gradient-to-br from-blue-500 to-blue-600'
  },
  {
    title: 'Đang bán',
    value: '2,123',
    change: 8.7,
    icon: ShoppingBagIcon,
    bgColor: 'bg-gradient-to-br from-green-500 to-green-600'
  },
  {
    title: 'Hết hàng',
    value: '45',
    change: -12.3,
    icon: ExclamationTriangleIcon,
    bgColor: 'bg-gradient-to-br from-red-500 to-red-600'
  },
  {
    title: 'Tổng giá trị',
    value: '45.2 tỷ',
    change: 22.1,
    icon: CurrencyDollarIcon,
    bgColor: 'bg-gradient-to-br from-purple-500 to-purple-600'
  }
])

// Mock products data
const products = ref([
  {
    id: 1,
    name: 'iPhone 15 Pro Max 256GB',
    sku: 'IP15PM-256',
    brand: 'Apple',
    category: 'smartphone',
    price: 29990000,
    original_price: 32990000,
    stock: 25,
    status: 'active',
    image: 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400',
    description: 'iPhone 15 Pro Max với chip A17 Pro mạnh mẽ, camera 48MP'
  },
  {
    id: 2,
    name: 'Samsung Galaxy S24 Ultra',
    sku: 'SGS24U-512',
    brand: 'Samsung',
    category: 'smartphone',
    price: 24990000,
    original_price: 26990000,
    stock: 18,
    status: 'active',
    image: 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400',
    description: 'Galaxy S24 Ultra với S Pen tích hợp, camera 200MP'
  },
  {
    id: 3,
    name: 'MacBook Pro 14" M3 Pro',
    sku: 'MBP14-M3P',
    brand: 'Apple',
    category: 'laptop',
    price: 45990000,
    original_price: 45990000,
    stock: 12,
    status: 'active',
    image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400',
    description: 'MacBook Pro 14 inch với chip M3 Pro, màn hình Liquid Retina XDR'
  },
  {
    id: 4,
    name: 'iPad Pro 12.9" M2',
    sku: 'IPAD12-M2',
    brand: 'Apple',
    category: 'tablet',
    price: 28990000,
    original_price: 30990000,
    stock: 8,
    status: 'active',
    image: 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400',
    description: 'iPad Pro 12.9 inch với chip M2, hỗ trợ Apple Pencil'
  },
  {
    id: 5,
    name: 'AirPods Pro 2nd Gen',
    sku: 'APP2-USB',
    brand: 'Apple',
    category: 'accessory',
    price: 5990000,
    original_price: 6990000,
    stock: 0,
    status: 'inactive',
    image: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400',
    description: 'AirPods Pro thế hệ 2 với chống ồn chủ động, âm thanh không gian'
  },
  {
    id: 6,
    name: 'Xiaomi 14 Ultra',
    sku: 'XM14U-256',
    brand: 'Xiaomi',
    category: 'smartphone',
    price: 19990000,
    original_price: 19990000,
    stock: 15,
    status: 'active',
    image: 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=400',
    description: 'Xiaomi 14 Ultra với camera Leica, chip Snapdragon 8 Gen 3'
  }
])

// Filters
const filters = ref({
  search: '',
  status: '',
  brand: '',
  category: ''
})

// View mode
const viewMode = ref('grid')

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(8)

// Modal states
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingProduct = ref(null)

// Form data
const productForm = ref({
  name: '',
  sku: '',
  brand: '',
  category: '',
  price: '',
  original_price: '',
  stock: '',
  status: 'active',
  image: '',
  description: ''
})

// Computed properties
const filteredProducts = computed(() => {
  let filtered = products.value

  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    filtered = filtered.filter(product => 
      product.name.toLowerCase().includes(search) ||
      product.sku.toLowerCase().includes(search) ||
      product.brand.toLowerCase().includes(search)
    )
  }

  if (filters.value.status) {
    filtered = filtered.filter(product => product.status === filters.value.status)
  }

  if (filters.value.brand) {
    filtered = filtered.filter(product => product.brand === filters.value.brand)
  }

  if (filters.value.category) {
    filtered = filtered.filter(product => product.category === filters.value.category)
  }

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / itemsPerPage.value))

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, filteredProducts.value.length))

const paginatedProducts = computed(() => {
  return filteredProducts.value.slice(startIndex.value, endIndex.value)
})

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const getStatusLabel = (status) => {
  const labels = {
    active: 'Đang bán',
    inactive: 'Ngừng bán',
    draft: 'Bản nháp'
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-red-100 text-red-800',
    draft: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const clearFilters = () => {
  filters.value = {
    search: '',
    status: '',
  brand: '',
    category: ''
  }
  currentPage.value = 1
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const goToPage = (page) => {
  currentPage.value = page
}

const viewProduct = (product) => {
  console.log('View product:', product)
  // Implement view product logic
}

const editProduct = (product) => {
  editingProduct.value = product
  productForm.value = {
    name: product.name,
    sku: product.sku,
    brand: product.brand,
    category: product.category,
    price: product.price,
    original_price: product.original_price,
    stock: product.stock,
    status: product.status,
    image: product.image,
    description: product.description
  }
  showEditModal.value = true
}

const duplicateProduct = (product) => {
  const newProduct = {
    ...product,
    id: products.value.length + 1,
    name: `${product.name} (Bản sao)`,
    sku: `${product.sku}-COPY`,
    stock: 0,
    status: 'draft'
  }
  products.value.push(newProduct)
  console.log('Duplicated product:', newProduct)
}

const deleteProduct = (product) => {
  if (confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${product.name}"?`)) {
    console.log('Delete product:', product)
    const index = products.value.findIndex(p => p.id === product.id)
    if (index > -1) {
      products.value.splice(index, 1)
    }
  }
}

const saveProduct = () => {
  if (showEditModal.value) {
    // Update existing product
    const index = products.value.findIndex(p => p.id === editingProduct.value.id)
    if (index > -1) {
      products.value[index] = {
        ...products.value[index],
        ...productForm.value
      }
    }
    console.log('Updated product:', productForm.value)
  } else {
    // Add new product
    const newProduct = {
      id: products.value.length + 1,
      ...productForm.value
    }
    products.value.push(newProduct)
    console.log('Added product:', newProduct)
  }
  
  closeModal()
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingProduct.value = null
  productForm.value = {
    name: '',
    sku: '',
    brand: '',
    category: '',
    price: '',
    original_price: '',
    stock: '',
    status: 'active',
    image: '',
    description: ''
  }
}

onMounted(() => {
  console.log('Products page mounted')
})
</script> 

<style scoped>
/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 