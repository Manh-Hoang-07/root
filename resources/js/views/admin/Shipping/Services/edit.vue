<template>
  <Modal :model-value="true" title="Sửa dịch vụ" @update:model-value="$emit('close')">
    <form @submit.prevent="handleSubmit">
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Tên dịch vụ</label>
        <input v-model="serviceName" type="text" class="w-full px-4 py-2 border rounded-xl" />
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Mã dịch vụ (code)</label>
        <input v-model="serviceCode" type="text" class="w-full px-4 py-2 border rounded-xl" />
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Provider</label>
        <select v-model="providerId" class="w-full px-4 py-2 border rounded-xl">
          <option v-for="provider in providers" :key="provider.id" :value="provider.id">{{ provider.name }}</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Base Price</label>
        <input v-model="serviceBasePrice" type="number" class="w-full px-4 py-2 border rounded-xl" />
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Weight Multiplier</label>
        <input v-model="serviceWeightFee" type="number" class="w-full px-4 py-2 border rounded-xl" />
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Estimated Days</label>
        <input v-model="serviceDays" type="number" class="w-full px-4 py-2 border rounded-xl" />
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Trạng thái</label>
        <select v-model="serviceStatus" class="w-full px-4 py-2 border rounded-xl">
          <option value="active">Hoạt động</option>
          <option value="inactive">Không hoạt động</option>
        </select>
      </div>
      <div class="flex justify-end gap-2 mt-6">
        <button type="button" class="px-4 py-2 bg-gray-200 rounded-lg" @click="$emit('close')">Hủy</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Lưu</button>
      </div>
    </form>
  </Modal>
</template>
<script setup>
import Modal from '@/components/Modal.vue'
</script> 