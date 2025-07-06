<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Cài đặt hệ thống</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- General Settings -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cài đặt chung</h3>
        <form @submit.prevent="saveGeneralSettings">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Tên website</label>
              <input 
                v-model="generalSettings.site_name"
                type="text" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Mô tả website</label>
              <textarea 
                v-model="generalSettings.site_description"
                rows="3"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              ></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email liên hệ</label>
              <input 
                v-model="generalSettings.contact_email"
                type="email" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
              <input 
                v-model="generalSettings.contact_phone"
                type="text" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
              <textarea 
                v-model="generalSettings.address"
                rows="2"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              ></textarea>
            </div>
            <button 
              type="submit"
              class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
            >
              Lưu cài đặt chung
            </button>
          </div>
        </form>
      </div>

      <!-- Order Settings -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cài đặt đơn hàng</h3>
        <form @submit.prevent="saveOrderSettings">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Tự động xác nhận đơn hàng</label>
              <select 
                v-model="orderSettings.auto_confirm"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="1">Có</option>
                <option value="0">Không</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Thời gian hủy đơn hàng (phút)</label>
              <input 
                v-model="orderSettings.cancel_time"
                type="number" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Phí vận chuyển tối thiểu</label>
              <input 
                v-model="orderSettings.min_shipping_fee"
                type="number" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Miễn phí vận chuyển từ</label>
              <input 
                v-model="orderSettings.free_shipping_threshold"
                type="number" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <button 
              type="submit"
              class="w-full bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors"
            >
              Lưu cài đặt đơn hàng
            </button>
          </div>
        </form>
      </div>

      <!-- Email Settings -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cài đặt email</h3>
        <form @submit.prevent="saveEmailSettings">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">SMTP Host</label>
              <input 
                v-model="emailSettings.smtp_host"
                type="text" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">SMTP Port</label>
              <input 
                v-model="emailSettings.smtp_port"
                type="number" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email gửi</label>
              <input 
                v-model="emailSettings.from_email"
                type="email" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Tên người gửi</label>
              <input 
                v-model="emailSettings.from_name"
                type="text" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <button 
              type="submit"
              class="w-full bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors"
            >
              Lưu cài đặt email
            </button>
          </div>
        </form>
      </div>

      <!-- Security Settings -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bảo mật</h3>
        <form @submit.prevent="saveSecuritySettings">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Thời gian session (phút)</label>
              <input 
                v-model="securitySettings.session_timeout"
                type="number" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Số lần đăng nhập sai tối đa</label>
              <input 
                v-model="securitySettings.max_login_attempts"
                type="number" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Thời gian khóa tài khoản (phút)</label>
              <input 
                v-model="securitySettings.lockout_duration"
                type="number" 
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div class="flex items-center">
              <input 
                v-model="securitySettings.require_2fa"
                type="checkbox" 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              >
              <label class="ml-2 block text-sm text-gray-900">
                Yêu cầu xác thực 2 yếu tố cho admin
              </label>
            </div>
            <button 
              type="submit"
              class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors"
            >
              Lưu cài đặt bảo mật
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const generalSettings = ref({
  site_name: '',
  site_description: '',
  contact_email: '',
  contact_phone: '',
  address: ''
});

const orderSettings = ref({
  auto_confirm: '0',
  cancel_time: 30,
  min_shipping_fee: 20000,
  free_shipping_threshold: 500000
});

const emailSettings = ref({
  smtp_host: '',
  smtp_port: 587,
  from_email: '',
  from_name: ''
});

const securitySettings = ref({
  session_timeout: 120,
  max_login_attempts: 5,
  lockout_duration: 30,
  require_2fa: false
});

const loadSettings = async () => {
  try {
    const response = await fetch('/api/admin/settings');
    const data = await response.json();
    if (data.success) {
      generalSettings.value = data.data.general || {};
      orderSettings.value = data.data.order || {};
      emailSettings.value = data.data.email || {};
      securitySettings.value = data.data.security || {};
    }
  } catch (error) {
    console.error('Error loading settings:', error);
  }
};

const saveGeneralSettings = async () => {
  try {
    const response = await fetch('/api/admin/settings/general', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(generalSettings.value)
    });
    
    if (response.ok) {
      alert('Đã lưu cài đặt chung');
    }
  } catch (error) {
    console.error('Error saving general settings:', error);
  }
};

const saveOrderSettings = async () => {
  try {
    const response = await fetch('/api/admin/settings/order', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(orderSettings.value)
    });
    
    if (response.ok) {
      alert('Đã lưu cài đặt đơn hàng');
    }
  } catch (error) {
    console.error('Error saving order settings:', error);
  }
};

const saveEmailSettings = async () => {
  try {
    const response = await fetch('/api/admin/settings/email', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(emailSettings.value)
    });
    
    if (response.ok) {
      alert('Đã lưu cài đặt email');
    }
  } catch (error) {
    console.error('Error saving email settings:', error);
  }
};

const saveSecuritySettings = async () => {
  try {
    const response = await fetch('/api/admin/settings/security', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(securitySettings.value)
    });
    
    if (response.ok) {
      alert('Đã lưu cài đặt bảo mật');
    }
  } catch (error) {
    console.error('Error saving security settings:', error);
  }
};

onMounted(() => {
  loadSettings();
});
</script> 