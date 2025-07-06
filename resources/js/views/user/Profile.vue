<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold text-gray-800">Hồ sơ cá nhân</h1>
      <button 
        @click="saveProfile"
        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors"
      >
        Lưu thay đổi
      </button>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
      <!-- Profile Info -->
      <div class="md:col-span-2 space-y-6">
        <!-- Basic Information -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-xl font-semibold mb-4">Thông tin cơ bản</h2>
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Họ và tên</label>
              <input 
                v-model="profile.name"
                type="text" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input 
                v-model="profile.email"
                type="email" 
                disabled
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
              <input 
                v-model="profile.phone"
                type="tel" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Ngày sinh</label>
              <input 
                v-model="profile.birthdate"
                type="date" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
          </div>
        </div>

        <!-- Address Information -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-xl font-semibold mb-4">Địa chỉ</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
              <input 
                v-model="profile.address"
                type="text" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div class="grid md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Tỉnh/Thành phố</label>
                <select 
                  v-model="profile.city"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Chọn tỉnh/thành phố</option>
                  <option value="hanoi">Hà Nội</option>
                  <option value="hcm">TP. Hồ Chí Minh</option>
                  <option value="danang">Đà Nẵng</option>
                  <option value="cantho">Cần Thơ</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Quận/Huyện</label>
                <select 
                  v-model="profile.district"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Chọn quận/huyện</option>
                  <option value="district1">Quận 1</option>
                  <option value="district2">Quận 2</option>
                  <option value="district3">Quận 3</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Mã bưu điện</label>
                <input 
                  v-model="profile.postalCode"
                  type="text" 
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Preferences -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-xl font-semibold mb-4">Tùy chọn</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Ngôn ngữ</label>
              <select 
                v-model="profile.language"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="vi">Tiếng Việt</option>
                <option value="en">English</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Múi giờ</label>
              <select 
                v-model="profile.timezone"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh (GMT+7)</option>
                <option value="UTC">UTC (GMT+0)</option>
              </select>
            </div>
            <div>
              <label class="flex items-center">
                <input 
                  v-model="profile.emailNotifications"
                  type="checkbox" 
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                >
                <span class="ml-2 text-sm text-gray-700">Nhận thông báo qua email</span>
              </label>
            </div>
            <div>
              <label class="flex items-center">
                <input 
                  v-model="profile.smsNotifications"
                  type="checkbox" 
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                >
                <span class="ml-2 text-sm text-gray-700">Nhận thông báo qua SMS</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Avatar -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-xl font-semibold mb-4">Ảnh đại diện</h2>
          <div class="text-center">
            <div class="w-32 h-32 mx-auto mb-4 rounded-full bg-gray-200 flex items-center justify-center">
              <img v-if="profile.avatar" :src="profile.avatar" alt="Avatar" class="w-32 h-32 rounded-full object-cover">
              <svg v-else class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <button 
              @click="uploadAvatar"
              class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
            >
              Thay đổi ảnh
            </button>
          </div>
        </div>

        <!-- Account Stats -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-xl font-semibold mb-4">Thống kê tài khoản</h2>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Ngày tham gia:</span>
              <span class="font-medium">{{ formatDate(profile.joinedAt) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Đơn hàng:</span>
              <span class="font-medium">{{ stats.totalOrders }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Điểm tích lũy:</span>
              <span class="font-medium">{{ stats.points }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Cấp độ:</span>
              <span class="font-medium">{{ stats.level }}</span>
            </div>
          </div>
        </div>

        <!-- Change Password -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-xl font-semibold mb-4">Đổi mật khẩu</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Mật khẩu hiện tại</label>
              <input 
                v-model="passwordForm.currentPassword"
                type="password" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Mật khẩu mới</label>
              <input 
                v-model="passwordForm.newPassword"
                type="password" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Xác nhận mật khẩu mới</label>
              <input 
                v-model="passwordForm.confirmPassword"
                type="password" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <button 
              @click="changePassword"
              class="w-full bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors"
            >
              Đổi mật khẩu
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// State
const profile = ref({
  name: 'Nguyễn Văn A',
  email: 'nguyenvana@example.com',
  phone: '0123456789',
  birthdate: '1990-01-01',
  address: '123 Đường ABC, Quận 1',
  city: 'hcm',
  district: 'district1',
  postalCode: '70000',
  language: 'vi',
  timezone: 'Asia/Ho_Chi_Minh',
  emailNotifications: true,
  smsNotifications: false,
  avatar: null,
  joinedAt: '2023-01-15'
});

const stats = ref({
  totalOrders: 15,
  points: 1250,
  level: 'Bạc'
});

const passwordForm = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
});

// Methods
const saveProfile = async () => {
  try {
    // Simulate API call
    console.log('Saving profile:', profile.value);
    alert('Hồ sơ đã được cập nhật thành công!');
  } catch (error) {
    console.error('Error saving profile:', error);
    alert('Có lỗi xảy ra khi cập nhật hồ sơ!');
  }
};

const uploadAvatar = () => {
  // Simulate file upload
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/*';
  input.onchange = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        profile.value.avatar = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  };
  input.click();
};

const changePassword = async () => {
  if (passwordForm.value.newPassword !== passwordForm.value.confirmPassword) {
    alert('Mật khẩu xác nhận không khớp!');
    return;
  }

  try {
    // Simulate API call
    console.log('Changing password:', passwordForm.value);
    alert('Mật khẩu đã được thay đổi thành công!');
    passwordForm.value = {
      currentPassword: '',
      newPassword: '',
      confirmPassword: ''
    };
  } catch (error) {
    console.error('Error changing password:', error);
    alert('Có lỗi xảy ra khi đổi mật khẩu!');
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('vi-VN');
};

const loadProfile = async () => {
  try {
    // Simulate API call to load profile
    // profile.value = await fetch('/api/user/profile').then(res => res.json());
  } catch (error) {
    console.error('Error loading profile:', error);
  }
};

// Lifecycle
onMounted(() => {
  loadProfile();
});
</script> 