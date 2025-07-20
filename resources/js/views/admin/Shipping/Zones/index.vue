<template>
  <CustomSection title="Zone Mapping" description="Quản lý bản đồ khu vực vận chuyển">
    <button class="mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg" @click="openCreate">+ Thêm zone</button>
    <button class="mb-4 px-4 py-2 bg-gray-600 text-white rounded-lg ml-2" @click="openEdit">Sửa zone</button>
    <!-- Zone Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <!-- Card zone mẫu -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Miền Bắc</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between"><span>Provinces:</span><span class="font-medium">25 tỉnh</span></div>
          <div class="flex justify-between"><span>Base Fee:</span><span class="font-medium">15,000đ</span></div>
          <div class="flex justify-between"><span>Delivery Time:</span><span class="font-medium">2-3 ngày</span></div>
          <div class="flex justify-between"><span>Services:</span><span class="font-medium">3 active</span></div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openEditZone('Miền Bắc')">Edit</button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openViewZone('Miền Bắc')">View Details</button>
        </div>
      </div>
      <!-- Card zone khác ... -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Miền Trung</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between"><span>Provinces:</span><span class="font-medium">19 tỉnh</span></div>
          <div class="flex justify-between"><span>Base Fee:</span><span class="font-medium">20,000đ</span></div>
          <div class="flex justify-between"><span>Delivery Time:</span><span class="font-medium">3-4 ngày</span></div>
          <div class="flex justify-between"><span>Services:</span><span class="font-medium">2 active</span></div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openEditZone('Miền Trung')">Edit</button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openViewZone('Miền Trung')">View Details</button>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Miền Nam</h3>
          <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
        </div>
        <div class="space-y-2 text-sm text-gray-600">
          <div class="flex justify-between"><span>Provinces:</span><span class="font-medium">19 tỉnh</span></div>
          <div class="flex justify-between"><span>Base Fee:</span><span class="font-medium">18,000đ</span></div>
          <div class="flex justify-between"><span>Delivery Time:</span><span class="font-medium">2-3 ngày</span></div>
          <div class="flex justify-between"><span>Services:</span><span class="font-medium">3 active</span></div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition" @click="openEditZone('Miền Nam')">Edit</button>
          <button class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition" @click="openViewZone('Miền Nam')">View Details</button>
        </div>
      </div>
    </div>
    <!-- Province Mapping -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Mapping tỉnh/thành phố</h3>
        <button class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition" @click="showAddZone = true">
          + Thêm Zone
        </button>
      </div>
      <div class="overflow-x-auto">
        <!-- ... giữ nguyên table mapping ... -->
        <table class="w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left font-medium text-gray-900">Tỉnh/Thành</th>
              <th class="px-4 py-3 text-left font-medium text-gray-900">Zone</th>
              <th class="px-4 py-3 text-left font-medium text-gray-900">Base Fee</th>
              <th class="px-4 py-3 text-left font-medium text-gray-900">Delivery Time</th>
              <th class="px-4 py-3 text-left font-medium text-gray-900">Status</th>
              <th class="px-4 py-3 text-left font-medium text-gray-900">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <!-- ... các dòng mapping ... -->
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">Hà Nội</td>
              <td class="px-4 py-3">Miền Bắc</td>
              <td class="px-4 py-3">15,000đ</td>
              <td class="px-4 py-3">1-2 ngày</td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Active</span>
              </td>
              <td class="px-4 py-3">
                <button class="text-blue-600 hover:text-blue-800 text-sm" @click="openEditZone('Hà Nội')">Edit</button>
                <button class="text-red-600 hover:text-red-800 text-sm ml-2" @click="openDeleteZone('Hà Nội')">Delete</button>
              </td>
            </tr>
            <!-- ... các dòng khác ... -->
          </tbody>
        </table>
      </div>
    </div>
    <!-- Modal thêm/sửa zone -->
    <Modal v-if="showAddZone || showEditZone" v-model="showAddZone" :title="editZoneName ? 'Sửa zone' : 'Thêm zone mới'">
      <form @submit.prevent="submitZone">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Tên zone</label>
          <input v-model="zoneName" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Provinces</label>
          <input v-model="zoneProvinces" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Base Fee</label>
          <input v-model="zoneBaseFee" type="number" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Delivery Time</label>
          <input v-model="zoneDeliveryTime" type="text" class="w-full px-4 py-2 border rounded-xl" required />
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="closeZoneModal">Hủy</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Lưu</button>
        </div>
      </form>
    </Modal>
    <!-- Modal xác nhận View Details -->
    <Modal v-if="showViewZone" v-model="showViewZone" title="Chi tiết zone">
      <div class="py-4 text-center">
        <p>Hiển thị chi tiết zone <b>{{ viewZoneName }}</b> (demo).</p>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg" @click="showViewZone = false">Đóng</button>
        </div>
      </div>
    </Modal>
    <!-- Modal xác nhận xóa zone -->
    <Modal v-if="showDeleteZone" v-model="showDeleteZone" title="Xóa zone">
      <div class="py-4 text-center">
        <p>Bạn muốn xóa zone <b>{{ deleteZoneName }}</b>?</p>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="showDeleteZone = false">Hủy</button>
          <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg" @click="submitDeleteZone">Xóa</button>
        </div>
      </div>
    </Modal>
    <CreateZoneModal v-if="showCreate" @close="showCreate = false" @save="showCreate = false" />
    <EditZoneModal v-if="showEdit" @close="showEdit = false" @save="showEdit = false" />
  </CustomSection>
</template>
<script setup>
import { ref } from 'vue'
import CustomSection from '@/components/CustomSection.vue'
import Modal from '@/components/Modal.vue'
import CreateZoneModal from './create.vue'
import EditZoneModal from './edit.vue'

const showAddZone = ref(false)
const showEditZone = ref(false)
const editZoneName = ref('')
const zoneName = ref('')
const zoneProvinces = ref('')
const zoneBaseFee = ref('')
const zoneDeliveryTime = ref('')
const showViewZone = ref(false)
const viewZoneName = ref('')
const showDeleteZone = ref(false)
const deleteZoneName = ref('')
const showCreate = ref(false)
const showEdit = ref(false)

function openEditZone(name) {
  editZoneName.value = name
  zoneName.value = name
  zoneProvinces.value = ''
  zoneBaseFee.value = ''
  zoneDeliveryTime.value = ''
  showAddZone.value = true
}
function openViewZone(name) {
  viewZoneName.value = name
  showViewZone.value = true
}
function openDeleteZone(name) {
  deleteZoneName.value = name
  showDeleteZone.value = true
}
function closeZoneModal() {
  showAddZone.value = false
  showEditZone.value = false
  editZoneName.value = ''
}
function submitZone() {
  // Xử lý thêm/sửa zone
  closeZoneModal()
}
function submitDeleteZone() {
  // Xử lý xóa zone
  showDeleteZone.value = false
}
function openCreate() { showCreate.value = true }
function openEdit() { showEdit.value = true }
</script> 