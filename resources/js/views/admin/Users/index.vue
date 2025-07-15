<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Quản lý người dùng</h1>
        <p class="text-gray-600">Quản lý tài khoản người dùng trong hệ thống</p>
      </div>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg flex items-center space-x-2"
      >
        <PlusIcon class="w-5 h-5" />
        <span>Thêm người dùng</span>
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="relative">
          <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
          <input
            v-model="filters.search"
            type="text"
            placeholder="Tìm kiếm người dùng..."
            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
        </div>

        <select v-model="filters.role" class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
          <option value="">Tất cả vai trò</option>
          <option value="admin">Admin</option>
          <option value="user">User</option>
          <option value="moderator">Moderator</option>
        </select>

        <select v-model="filters.status" class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
          <option value="">Tất cả trạng thái</option>
          <option value="active">Hoạt động</option>
          <option value="inactive">Không hoạt động</option>
          <option value="suspended">Tạm khóa</option>
        </select>

        <button @click="clearFilters" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
          Xóa bộ lọc
        </button>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8 overflow-auto">
      <table class="w-full table-auto text-sm">
        <thead class="bg-gray-50">
          <tr class="text-left">
            <th class="px-4 py-3 whitespace-nowrap">Họ tên</th>
            <th class="px-4 py-3 whitespace-nowrap hidden md:table-cell">Email</th>
            <th class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">Trạng thái</th>
            <th class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">Giới tính</th>
            <th class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">Số điện thoại</th>
            <th class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">Địa chỉ</th>
            <th class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">Ngày tạo</th>
            <th class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">Đăng nhập cuối</th>
            <th class="px-4 py-3 whitespace-nowrap">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="users.length === 0">
            <td colspan="9" class="text-center py-6 text-gray-400">Không có dữ liệu</td>
          </tr>
          <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
            <td class="px-4 py-3 whitespace-nowrap">{{ user.name || '—' }}</td>
            <td class="px-4 py-3 whitespace-nowrap hidden md:table-cell">{{ user.email || '—' }}</td>
            <td class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">{{ user.status || '—' }}</td>
            <td class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">{{ user.gender || '—' }}</td>
            <td class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">{{ user.phone || '—' }}</td>
            <td class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">{{ user.address || '—' }}</td>
            <td class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">{{ user.created_at || '—' }}</td>
            <td class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">{{ user.last_login || '—' }}</td>
            <td class="px-4 py-3 whitespace-nowrap text-right">
              <button @click="editUser(user)" class="text-blue-600 hover:text-blue-800 mr-2" title="Sửa">
                <PencilIcon class="w-5 h-5 inline" />
              </button>
              <button @click="deleteUser(user)" class="text-red-600 hover:text-red-800" title="Xóa">
                <TrashIcon class="w-5 h-5 inline" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showAddModal || showEditModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="closeModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold">
            {{ showEditModal ? 'Chỉnh sửa người dùng' : 'Thêm người dùng mới' }}
          </h3>
        </div>
        <div class="p-6 max-h-[80vh] overflow-y-auto">
          <form @submit.prevent="saveUser" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">Họ tên</label>
              <input v-model="userForm.name" required type="text" class="w-full px-4 py-2 border rounded-xl" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Email</label>
              <input v-model="userForm.email" required type="email" class="w-full px-4 py-2 border rounded-xl" />
            </div>
            <div v-if="!showEditModal">
              <label class="block text-sm font-medium mb-1">Mật khẩu</label>
              <input v-model="userForm.password" required type="password" class="w-full px-4 py-2 border rounded-xl" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Trạng thái</label>
              <select v-model="userForm.status" required class="w-full px-4 py-2 border rounded-xl">
                <option value="">Chọn trạng thái</option>
                <option v-for="s in statusEnums" :key="s.value" :value="s.value">{{ s.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Giới tính</label>
              <select v-model="userForm.gender" class="w-full px-4 py-2 border rounded-xl">
                <option value="">Chọn giới tính</option>
                <option v-for="g in genderEnums" :key="g.value" :value="g.value">{{ g.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Số điện thoại</label>
              <input v-model="userForm.phone" type="text" class="w-full px-4 py-2 border rounded-xl" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Địa chỉ</label>
              <input v-model="userForm.address" type="text" class="w-full px-4 py-2 border rounded-xl" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Ảnh đại diện</label>
              <input type="file" @change="onAvatarChange" accept="image/*" />
            </div>
            <div class="flex justify-end gap-2 pt-4">
              <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 rounded-xl hover:bg-gray-200">Hủy</button>
              <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
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
import { ref, onMounted } from 'vue'
import axios from 'axios'
import endpoints from '@/api/endpoints'
import useApiOptions from '@/composables/useApiOptions'
import { PencilIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const users = ref([])
const filters = ref({ search: '', role: '', status: '' })
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingUser = ref(null)
const { options: statusEnums } = useApiOptions(endpoints.enums('userStatus'))
const { options: genderEnums } = useApiOptions(endpoints.enums('gender'))

const userForm = ref({
  name: '', email: '', password: '', status: '', gender: '', phone: '', address: '', avatar: null
})

const fetchUsers = async () => {
  try {
    const res = await axios.get(endpoints.users)
    users.value = res.data.data || []
  } catch (e) {
    console.error('Lỗi khi lấy người dùng:', e)
    users.value = []
  }
}

const saveUser = async () => {
  const formData = new FormData()
  Object.entries(userForm.value).forEach(([key, value]) => {
    if (key === 'avatar' && !value) return
    formData.append(key, value)
  })

  try {
    if (showEditModal.value) {
      await axios.post(`/api/admin/users/${editingUser.value.id}?_method=PUT`, formData)
    } else {
      await axios.post('/api/admin/users', formData)
    }
    await fetchUsers()
    closeModal()
  } catch (e) {
    console.error('Lỗi khi lưu người dùng:', e)
  }
}

const openAddModal = () => {
  showAddModal.value = true
  showEditModal.value = false
  userForm.value = { name: '', email: '', password: '', status: '', gender: '', phone: '', address: '', avatar: null }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingUser.value = null
  userForm.value = { name: '', email: '', password: '', status: '', gender: '', phone: '', address: '', avatar: null }
}

const onAvatarChange = (e) => {
  const file = e.target.files[0]
  if (file) userForm.value.avatar = file
}

const clearFilters = () => {
  filters.value = { search: '', role: '', status: '' }
}

const editUser = (user) => {
  showEditModal.value = true
  showAddModal.value = false
  editingUser.value = user
  userForm.value = {
    name: user.name || '',
    email: user.email || '',
    password: '',
    status: user.status || '',
    gender: user.gender || '',
    phone: user.phone || '',
    address: user.address || '',
    avatar: null
  }
}
const deleteUser = async (user) => {
  if (confirm(`Bạn có chắc chắn muốn xóa người dùng ${user.name}?`)) {
    try {
      await axios.delete(`/api/admin/users/${user.id}`)
      await fetchUsers()
    } catch (e) {
      alert('Xóa thất bại!')
    }
  }
}

onMounted(fetchUsers)
</script>

<style scoped>
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