<template>
  <BaseForm
    :show="show"
    :title="isEdit ? 'Chỉnh sửa tồn kho' : 'Thêm tồn kho mới'"
    :loading="loading"
    :api-errors="apiErrors"
    @submit="handleSubmit"
    @cancel="$emit('cancel')"
  >
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Sản phẩm -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Sản phẩm <span class="text-red-500">*</span>
        </label>
        <select
          v-model="form.product_id"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :disabled="loading"
          required
        >
          <option value="">Chọn sản phẩm</option>
          <option v-for="product in products" :key="product.id" :value="product.id">
            {{ product.name }} ({{ product.code }})
          </option>
        </select>
      </div>

      <!-- Kho hàng -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Kho hàng <span class="text-red-500">*</span>
        </label>
        <select
          v-model="form.warehouse_id"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :disabled="loading"
          required
        >
          <option value="">Chọn kho hàng</option>
          <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
            {{ warehouse.name }}
          </option>
        </select>
      </div>

      <!-- Số lượng -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Số lượng <span class="text-red-500">*</span>
        </label>
        <input
          v-model.number="form.quantity"
          type="number"
          min="0"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :disabled="loading"
          placeholder="Nhập số lượng"
          required
        />
      </div>

      <!-- Ghi chú -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Ghi chú
        </label>
        <textarea
          v-model="form.note"
          rows="3"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :disabled="loading"
          placeholder="Ghi chú về tồn kho (tùy chọn)"
        ></textarea>
      </div>
    </div>
  </BaseForm>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue'
import BaseForm from '@/components/Core/BaseForm.vue'

const props = defineProps({
  show: Boolean,
  inventory: Object,
  products: {
    type: Array,
    default: () => []
  },
  warehouses: {
    type: Array,
    default: () => []
  },
  apiErrors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['submit', 'cancel'])

const loading = ref(false)

const form = reactive({
  product_id: '',
  warehouse_id: '',
  quantity: 0,
  note: ''
})

const isEdit = computed(() => !!props.inventory)

// Watch inventory để populate form khi edit
watch(() => props.inventory, (newInventory) => {
  if (newInventory) {
    form.product_id = newInventory.product_id || ''
    form.warehouse_id = newInventory.warehouse_id || ''
    form.quantity = newInventory.quantity || 0
    form.note = newInventory.note || ''
  } else {
    // Reset form khi tạo mới
    form.product_id = ''
    form.warehouse_id = ''
    form.quantity = 0
    form.note = ''
  }
}, { immediate: true })

async function handleSubmit() {
  loading.value = true
  
  try {
    const formData = { ...form }
    emit('submit', formData)
  } catch (error) {
    console.error('Error submitting form:', error)
  } finally {
    loading.value = false
  }
}
</script> 