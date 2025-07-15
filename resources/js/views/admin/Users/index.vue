<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
  <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Quản lý người dùng</h1>
          <p class="text-gray-600">Quản lý tài khoản người dùng trong hệ thống</p>
        </div>
      <button 
        @click="showAddModal = true"
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
        >
          <PlusIcon class="w-5 h-5" />
          <span>Thêm người dùng</span>
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
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Search -->
        <div class="relative">
          <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
          <input 
            v-model="filters.search"
            type="text" 
            placeholder="Tìm kiếm người dùng..."
            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
          />
        </div>

        <!-- Role Filter -->
          <select 
          v-model="filters.role"
          class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
          >
          <option value="">Tất cả vai trò</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
          <option value="moderator">Moderator</option>
          </select>

        <!-- Status Filter -->
          <select 
          v-model="filters.status"
          class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
          >
          <option value="">Tất cả trạng thái</option>
            <option value="active">Hoạt động</option>
            <option value="inactive">Không hoạt động</option>
          <option value="suspended">Tạm khóa</option>
          </select>

        <!-- Clear Filters -->
          <button 
          @click="clearFilters"
          class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium"
          >
          Xóa bộ lọc
          </button>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ tên</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giới tính</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số điện thoại</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa chỉ</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đăng nhập cuối</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tài khoản liên kết</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading" class="animate-pulse">
              <td colspan="10" class="px-6 py-4 whitespace-nowrap text-center">Đang tải dữ liệu...</td>
            </tr>
            <tr v-else-if="error" class="bg-red-50 text-red-800">
              <td colspan="10" class="px-6 py-4 whitespace-nowrap text-center">{{ error }}</td>
            </tr>
            <tr v-else-if="users.length === 0" class="bg-yellow-50 text-yellow-800">
              <td colspan="10" class="px-6 py-4 whitespace-nowrap text-center">Không có dữ liệu</td>
            </tr>
            <tr v-for="user in paginatedUsers" :key="user.id" class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(user.status)">
                  {{ getStatusLabel(user.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">{{ getGenderLabel(user.profile?.gender) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ user.profile?.phone || '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ user.profile?.address || '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(user.created_at) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(user.last_login) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex flex-col space-y-1">
                  <span v-for="sa in user.social_accounts" :key="sa.id" class="text-xs bg-gray-50 rounded px-2 py-1 inline-block">
                    {{ sa.provider }} <span class="text-gray-400">({{ sa.provider_id }})</span>
                  </span>
                  <span v-if="!user.social_accounts || user.social_accounts.length === 0" class="text-gray-400">Không có</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <button @click="viewUser(user)" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-lg hover:bg-indigo-50 transition-all duration-200" title="Xem chi tiết">
                    <EyeIcon class="w-4 h-4" />
                  </button>
                  <button @click="editUser(user)" class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-all duration-200" title="Chỉnh sửa">
                    <PencilIcon class="w-4 h-4" />
                  </button>
                  <button @click="deleteUser(user)" class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-all duration-200" title="Xóa">
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
      <div class="bg-white px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Hiển thị {{ startIndex + 1 }} đến {{ endIndex }} trong tổng số {{ filteredUsers.length }} người dùng
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
    </div>

    <!-- Add/Edit User Modal -->
    <div 
      v-if="showAddModal || showEditModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            {{ showEditModal ? 'Chỉnh sửa người dùng' : 'Thêm người dùng mới' }}
          </h3>
        </div>
        <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto">
          <form @submit.prevent="saveUser" class="space-y-6">
          <div class="grid grid-cols-1 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên</label>
              <input v-model="userForm.name" type="text" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input v-model="userForm.email" type="email" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div v-if="!showEditModal">
              <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu</label>
              <input v-model="userForm.password" type="password" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
              <select v-model="userForm.status" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option v-for="s in statusEnums" :key="s.value" :value="s.value">{{ s.label }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Giới tính</label>
              <select v-model="userForm.gender" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option v-for="g in genderEnums" :key="g.value" :value="g.value">{{ g.label }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
              <input v-model="userForm.phone" type="text" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ</label>
              <input v-model="userForm.address" type="text" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh đại diện</label>
              <input type="file" @change="onAvatarChange" accept="image/*" />
            </div>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="closeModal" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all duration-200">Hủy</button>
            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200">{{ showEditModal ? 'Cập nhật' : 'Thêm' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { 
  PlusIcon,
  MagnifyingGlassIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  UsersIcon,
  UserPlusIcon,
  UserMinusIcon,
  ShieldCheckIcon,
  ChevronUpIcon,
  ChevronDownIcon
} from '@heroicons/vue/24/outline'
import endpoints from '@/api/endpoints'
import axios from 'axios'

// Stats data
const stats = ref([
  {
    title: 'Tổng người dùng',
    value: '1,234',
    change: 12.5,
    icon: UsersIcon,
    bgColor: 'bg-gradient-to-br from-blue-500 to-blue-600'
  },
  {
    title: 'Người dùng mới',
    value: '89',
    change: 8.2,
    icon: UserPlusIcon,
    bgColor: 'bg-gradient-to-br from-green-500 to-green-600'
  },
  {
    title: 'Đã khóa',
    value: '23',
    change: -5.1,
    icon: UserMinusIcon,
    bgColor: 'bg-gradient-to-br from-red-500 to-red-600'
  },
  {
    title: 'Admin',
    value: '12',
    change: 0,
    icon: ShieldCheckIcon,
    bgColor: 'bg-gradient-to-br from-purple-500 to-purple-600'
  }
])

// Table columns
const columns = ref([
  { key: 'name', label: 'Người dùng' },
  { key: 'role', label: 'Vai trò' },
  { key: 'status', label: 'Trạng thái' },
  { key: 'created_at', label: 'Ngày tạo' },
  { key: 'last_login', label: 'Đăng nhập cuối' }
])

const users = ref([])
const loading = ref(false)
const error = ref(null)

const fetchUsers = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get(endpoints.users)
    users.value = response.data.data || []
  } catch (err) {
    error.value = 'Không thể tải dữ liệu người dùng'
    users.value = []
  } finally {
    loading.value = false
  }
}

// Filters
const filters = ref({
  search: '',
  role: '',
  status: ''
})

// Sorting
const sortKey = ref('created_at')
const sortOrder = ref('desc')

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Modal states
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingUser = ref(null)

// User form
const statusEnums = ref([])
const genderEnums = ref([])
const userForm = ref({
  name: '',
  email: '',
  password: '',
  status: '',
  gender: '',
  phone: '',
  address: '',
  avatar: null
})
const onAvatarChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    userForm.value.avatar = file
  }
}
const fetchEnums = async () => {
  try {
    const [statusRes, genderRes] = await Promise.all([
      axios.get('/api/enums/user-status'),
      axios.get('/api/enums/gender')
    ])
    statusEnums.value = statusRes.data
    genderEnums.value = genderRes.data
  } catch (e) {}
}

// Computed properties
const filteredUsers = computed(() => {
  let filtered = users.value

  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    filtered = filtered.filter(user => 
      user.name.toLowerCase().includes(search) ||
      user.email.toLowerCase().includes(search)
    )
  }

  if (filters.value.role) {
    filtered = filtered.filter(user => user.role === filters.value.role)
  }

  if (filters.value.status) {
    filtered = filtered.filter(user => user.status === filters.value.status)
  }

  // Sort
  filtered.sort((a, b) => {
    const aValue = a[sortKey.value]
    const bValue = b[sortKey.value]
    
    if (sortOrder.value === 'asc') {
      return aValue > bValue ? 1 : -1
    } else {
      return aValue < bValue ? 1 : -1
    }
  })

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / itemsPerPage.value))

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage.value, filteredUsers.value.length))

const paginatedUsers = computed(() => {
  return filteredUsers.value.slice(startIndex.value, endIndex.value)
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
const formatDate = (dateString) => {
  return new Intl.DateTimeFormat('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(dateString))
}

const getRoleLabel = (role) => {
  const labels = {
    admin: 'Admin',
    moderator: 'Moderator',
    user: 'User'
  }
  return labels[role] || role
}

const getRoleClass = (role) => {
  const classes = {
    admin: 'bg-purple-100 text-purple-800',
    moderator: 'bg-blue-100 text-blue-800',
    user: 'bg-gray-100 text-gray-800'
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    active: 'Hoạt động',
    inactive: 'Không hoạt động',
    suspended: 'Tạm khóa'
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-gray-100 text-gray-800',
    suspended: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getGenderLabel = (gender) => {
  const labels = {
    male: 'Nam',
    female: 'Nữ',
    other: 'Khác'
  }
  return labels[gender] || gender
}

const clearFilters = () => {
  filters.value = {
    search: '',
    role: '',
    status: ''
  }
  currentPage.value = 1
}

const sortBy = (key) => {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortOrder.value = 'asc'
  }
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

const viewUser = (user) => {
  console.log('View user:', user)
  // Implement view user logic
}

const editUser = (user) => {
  editingUser.value = user
  userForm.value = {
    name: user.name,
    email: user.email,
    password: '',
    status: user.status,
    gender: user.profile?.gender,
    phone: user.profile?.phone,
    address: user.profile?.address,
    avatar: null // No avatar editing in this modal
  }
  showEditModal.value = true
}

const deleteUser = (user) => {
  if (confirm(`Bạn có chắc chắn muốn xóa người dùng "${user.name}"?`)) {
    const index = users.value.findIndex(u => u.id === user.id)
    if (index > -1) {
      users.value.splice(index, 1)
      console.log('Deleted user:', user)
    }
  }
}

const saveUser = async () => {
  const formData = new FormData()
  formData.append('name', userForm.value.name)
  formData.append('email', userForm.value.email)
  if (!showEditModal.value) formData.append('password', userForm.value.password)
  formData.append('status', userForm.value.status)
  formData.append('gender', userForm.value.gender)
  formData.append('phone', userForm.value.phone)
  formData.append('address', userForm.value.address)
  if (userForm.value.avatar) formData.append('avatar', userForm.value.avatar)
  try {
    if (showEditModal.value) {
      await axios.post(`/api/users/${editingUser.value.id}`, formData)
    } else {
      await axios.post('/api/users', formData)
    }
    await fetchUsers()
    closeModal()
  } catch (e) {}
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingUser.value = null
  userForm.value = {
    name: '',
    email: '',
    password: '',
    status: '',
    gender: '',
    phone: '',
    address: '',
    avatar: null
  }
}

onMounted(() => {
  fetchUsers()
  fetchEnums()
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
</style> 