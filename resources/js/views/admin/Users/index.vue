<template>
  <TableLayout :loading="loading" :pagination="pagination" :has-data="users.length > 0" @page-change="fetchUsers">
    <template #header>
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
    </template>
    <template #filter>
      <Filter :filters="filters" @update:filters="onUpdateFilters" @clear="clearFilters" :status-enums="statusEnums" />
    </template>
    <template #table>
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
              <td class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">
                <div class="relative inline-block text-left">
                  <button
                    @click="toggleStatusMenu(user.id)"
                    :class="[
                      'px-2 py-1 rounded-full text-xs font-semibold focus:outline-none',
                      user.status === 'active' ? 'bg-green-100 text-green-700' :
                      user.status === 'inactive' ? 'bg-gray-100 text-gray-700' :
                      'bg-yellow-100 text-yellow-700'
                    ]"
                  >
                    {{ statusEnums.find(s => s.value === user.status)?.name || user.status }}
                    <svg class="inline w-3 h-3 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                  </button>
                  <div v-if="openStatusMenuId === user.id" class="absolute z-20 right-0 mt-1 w-32 bg-white border rounded shadow">
                    <div
                      v-for="s in statusEnums"
                      :key="s.value"
                      @click="setStatus(user, s.value)"
                      class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-xs"
                      :class="{
                        'text-green-600': s.value === 'active',
                        'text-gray-500': s.value === 'inactive',
                        'text-yellow-500': s.value === 'suspended',
                        'font-bold': user.status === s.value
                      }"
                    >
                      {{ s.name }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">
                {{ genderEnums.find(g => g.value === user.gender)?.name || user.gender || '—' }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">{{ user.phone || '—' }}</td>
              <td class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">{{ user.address || '—' }}</td>
              <td class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">{{ user.created_at || '—' }}</td>
              <td class="px-4 py-3 whitespace-nowrap hidden 2xl:table-cell">{{ user.last_login || '—' }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-right">
                <button @click="editUser(user)" class="text-blue-600 hover:text-blue-800 mr-2" title="Sửa">
                  <PencilIcon class="w-5 h-5 inline" />
                </button>
                <button @click="deleteUser(user)" class="text-red-600 hover:text-red-800 mr-2" title="Xóa">
                  <TrashIcon class="w-5 h-5 inline" />
                </button>
                <button @click="openChangePassword(user)" class="text-yellow-600 hover:text-yellow-800 mr-2" title="Đổi mật khẩu">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75A4.5 4.5 0 008 6.75v3.75m8.25 0h-8.5m8.5 0a2.25 2.25 0 01-2.25 2.25h-4.5A2.25 2.25 0 017.25 10.5m8.5 0v3.75A4.5 4.5 0 018 14.25v-3.75" />
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </TableLayout>

    <!-- Modal -->
    <div v-if="showAddModal || showEditModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold">
            {{ showEditModal ? 'Chỉnh sửa người dùng' : 'Thêm người dùng mới' }}
          </h3>
        </div>
        <div class="p-6 max-h-[80vh] overflow-y-auto">
          <Form
            :user="showEditModal ? editingUser : null"
            :mode="showEditModal ? 'edit' : 'create'"
            :status-enums="statusEnums"
            :gender-enums="genderEnums"
            @submit="saveUser"
            @cancel="closeModal"
          />
        </div>
      </div>
    </div>
    <ChangePassword :show="showChangePassword" :user="changingUser" :onClose="closeChangePassword" @changed="fetchUsers" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import endpoints from '@/api/endpoints'
import useApiOptions from '@/composables/useApiOptions'
import { PencilIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import Filter from './filter.vue'
import Form from './form.vue'
import ChangePassword from './changePassword.vue'
import TableLayout from '@/components/TableLayout.vue'

const users = ref([])
const filters = ref({ search: '', role: '', status: '' })
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingUser = ref(null)
const showChangePassword = ref(false)
const changingUser = ref(null)
const { options: statusEnums } = useApiOptions(endpoints.enums('userStatus'))
const { options: genderEnums } = useApiOptions(endpoints.enums('gender'))

const userForm = ref({
  username: '', email: '', phone: '', password: '', status: '', email_verified_at: null, phone_verified_at: null, last_login_at: null,
  name: '', avatar: null, birthday: null, gender: '', address: '', about: ''
})

const loading = ref(false)
const pagination = ref({
  currentPage: 1,
  totalPages: 0,
  totalItems: 0,
  itemsPerPage: 10
})

const fetchUsers = async () => {
  loading.value = true
  try {
    const params = { ...filters.value, page: pagination.value.currentPage, per_page: pagination.value.itemsPerPage }
    const res = await axios.get(endpoints.users.list, { params })
    users.value = res.data.data || []
    pagination.value.totalItems = res.data.total
    pagination.value.totalPages = res.data.last_page
  } catch (e) {
    console.error('Lỗi khi lấy người dùng:', e)
    users.value = []
    pagination.value.totalItems = 0
    pagination.value.totalPages = 0
  } finally {
    loading.value = false
  }
}

const onUpdateFilters = (newFilters) => {
  filters.value = newFilters
  pagination.value.currentPage = 1 // Reset to first page when filters change
  fetchUsers()
}

const saveUser = async (formData) => {
  try {
    if (showEditModal.value) {
      await axios.post(`${endpoints.users.update(editingUser.value.id)}?_method=PUT`, formData)
    } else {
      await axios.post(endpoints.users.create, formData)
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
  userForm.value = {
    username: '', email: '', phone: '', password: '', status: '', email_verified_at: null, phone_verified_at: null, last_login_at: null,
    name: '', avatar: null, birthday: null, gender: '', address: '', about: ''
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingUser.value = null
  userForm.value = {
    username: '', email: '', phone: '', password: '', status: '', email_verified_at: null, phone_verified_at: null, last_login_at: null,
    name: '', avatar: null, birthday: null, gender: '', address: '', about: ''
  }
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
    username: user.username || '',
    email: user.email || '',
    phone: user.phone || '',
    password: '',
    status: user.status || '',
    email_verified_at: user.email_verified_at || null,
    phone_verified_at: user.phone_verified_at || null,
    last_login_at: user.last_login_at || null,
    name: user.profile?.name || '',
    avatar: null,
    birthday: user.profile?.birthday || null,
    gender: user.profile?.gender || '',
    address: user.profile?.address || '',
    about: user.profile?.about || ''
  }
}
const deleteUser = async (user) => {
  if (confirm(`Bạn có chắc chắn muốn xóa người dùng ${user.name}?`)) {
    try {
      await axios.delete(endpoints.users.delete(user.id))
      await fetchUsers()
    } catch (e) {
      alert('Xóa thất bại!')
    }
  }
}

function openChangePassword(user) {
  changingUser.value = user
  showChangePassword.value = true
}
function closeChangePassword() {
  showChangePassword.value = false
  changingUser.value = null
}
async function changeStatus(user) {
  try {
    await axios.patch(endpoints.users.changeStatus(user.id), { status: user.status })
    await fetchUsers()
  } catch (e) {
    alert('Đổi trạng thái thất bại!')
  }
}

const openStatusMenuId = ref(null)
function toggleStatusMenu(userId) {
  openStatusMenuId.value = openStatusMenuId.value === userId ? null : userId
}
async function setStatus(user, status) {
  if (user.status === status) {
    openStatusMenuId.value = null
    return
  }
  try {
    await axios.patch(endpoints.users.changeStatus(user.id), { status })
    await fetchUsers()
  } catch (e) {
    alert('Đổi trạng thái thất bại!')
  }
  openStatusMenuId.value = null
}
document.addEventListener('click', (e) => {
  if (!e.target.closest('.inline-block.text-left')) {
    openStatusMenuId.value = null
  }
})

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