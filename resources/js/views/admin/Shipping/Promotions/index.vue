<template>
  <CustomSection title="Shipping Promotions" description="Quản lý khuyến mãi vận chuyển">
    <!-- Active Promotions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <!-- Card khuyến mãi mẫu -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Free Shipping</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between"><span>Min Order:</span><span class="font-medium">500,000đ</span></div>
          <div class="flex justify-between"><span>Discount:</span><span class="font-medium text-green-600">100%</span></div>
          <div class="flex justify-between"><span>Valid Until:</span><span class="font-medium">31/12/2024</span></div>
          <div class="flex justify-between"><span>Usage:</span><span class="font-medium">1,234 times</span></div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openEditPromotion('Free Shipping')">Edit</button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openTogglePromotion('Free Shipping', true)">Disable</button>
        </div>
      </div>
      <!-- Card khuyến mãi khác ... -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">VIP Discount</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between"><span>Customer Group:</span><span class="font-medium">VIP</span></div>
          <div class="flex justify-between"><span>Discount:</span><span class="font-medium text-green-600">50%</span></div>
          <div class="flex justify-between"><span>Valid Until:</span><span class="font-medium">31/12/2024</span></div>
          <div class="flex justify-between"><span>Usage:</span><span class="font-medium">567 times</span></div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openEditPromotion('VIP Discount')">Edit</button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openTogglePromotion('VIP Discount', true)">Disable</button>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">First Order</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Limited</span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between"><span>Target:</span><span class="font-medium">New Customers</span></div>
          <div class="flex justify-between"><span>Discount:</span><span class="font-medium text-green-600">20,000đ</span></div>
          <div class="flex justify-between"><span>Valid Until:</span><span class="font-medium">31/12/2024</span></div>
          <div class="flex justify-between"><span>Usage:</span><span class="font-medium">89 times</span></div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openEditPromotion('First Order')">Edit</button>
          <button class="px-3 py-1.5 bg-green-500 text-white rounded-lg text-sm hover:bg-green-600 transition" @click="openTogglePromotion('First Order', false)">Enable</button>
        </div>
      </div>
    </div>
    <!-- Add New Promotion -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Thêm khuyến mãi mới</h3>
        <button class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition" @click="showAddPromotion = true">
          + Thêm Promotion
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- ... các card promotion mới ... -->
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 hover:bg-blue-50 transition cursor-pointer">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-900">Free Shipping</p>
          <p class="text-xs text-gray-500">Miễn phí vận chuyển</p>
        </div>
        <!-- ... các card khác ... -->
      </div>
    </div>
    <!-- Modal thêm/sửa khuyến mãi -->
    <Modal v-if="showAddPromotion || showEditPromotion" v-model="showAddPromotion" :title="editPromotionName ? 'Sửa khuyến mãi' : 'Thêm khuyến mãi mới'">
      <form @submit.prevent="submitPromotion">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Tên khuyến mãi</label>
          <input v-model="promotionName" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Discount</label>
          <input v-model="promotionDiscount" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Valid Until</label>
          <input v-model="promotionValidUntil" type="date" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="closePromotionModal">Hủy</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Lưu</button>
        </div>
      </form>
    </Modal>
    <!-- Modal xác nhận bật/tắt khuyến mãi -->
    <Modal v-if="showToggle" v-model="showToggle" :title="toggleEnable ? 'Bật khuyến mãi' : 'Tắt khuyến mãi'">
      <div class="py-4 text-center">
        <p>Bạn muốn {{ toggleEnable ? 'bật' : 'tắt' }} khuyến mãi <b>{{ togglePromotionName }}</b>?</p>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="showToggle = false">Hủy</button>
          <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg" @click="submitTogglePromotion">Xác nhận</button>
        </div>
      </div>
    </Modal>
    <CreatePromotionModal v-if="showCreate" @close="showCreate = false" @save="showCreate = false" />
    <EditPromotionModal v-if="showEdit" @close="showEdit = false" @save="showEdit = false" />
  </CustomSection>
</template>
<script setup>
import { ref } from 'vue'
import CustomSection from '@/components/CustomSection.vue'
import Modal from '@/components/Modal.vue'
import CreatePromotionModal from './create.vue'
import EditPromotionModal from './edit.vue'

const showAddPromotion = ref(false)
const showEditPromotion = ref(false)
const editPromotionName = ref('')
const promotionName = ref('')
const promotionDiscount = ref('')
const promotionValidUntil = ref('')
const showToggle = ref(false)
const togglePromotionName = ref('')
const toggleEnable = ref(false)
function openEditPromotion(name) {
  editPromotionName.value = name
  promotionName.value = name
  promotionDiscount.value = ''
  promotionValidUntil.value = ''
  showAddPromotion.value = true
}
function openTogglePromotion(name, disable) {
  togglePromotionName.value = name
  toggleEnable.value = !disable
  showToggle.value = true
}
function closePromotionModal() {
  showAddPromotion.value = false
  showEditPromotion.value = false
  editPromotionName.value = ''
}
function submitPromotion() {
  // Xử lý thêm/sửa khuyến mãi
  closePromotionModal()
}
function submitTogglePromotion() {
  // Xử lý bật/tắt khuyến mãi
  showToggle.value = false
}

const showCreate = ref(false)
const showEdit = ref(false)

function openCreate() { showCreate.value = true }
function openEdit() { showEdit.value = true }
</script> 