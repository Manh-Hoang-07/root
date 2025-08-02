<template>
  <Modal v-model="modalVisible" title="Nhập kho" size="4xl">
    <FormWrapper
      :default-values="defaultValues"
      :rules="validationRules"
      :api-errors="apiErrors"
      :submit-text="'Nhập kho'"
      @submit="handleSubmit"
      @cancel="onClose"
      ref="formWrapperRef"
    >
      <template #default="{ form, errors, clearError, isSubmitting }">
        <div class="space-y-6">
          <!-- Thông tin nhập kho -->
          <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin nhập kho</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Sản phẩm -->
              <div class="md:col-span-2">
                <FormField
                  v-model="form.product_id"
                  label="Sản phẩm"
                  name="product_id"
                  type="select"
                  :options="productOptions"
                  :error="errors.product_id"
                  required
                  @update:model-value="clearError('product_id')"
                />
              </div>

              <!-- Kho hàng -->
              <div class="md:col-span-2">
                <FormField
                  v-model="form.warehouse_id"
                  label="Kho hàng"
                  name="warehouse_id"
                  type="select"
                  :options="warehouseOptions"
                  :error="errors.warehouse_id"
                  required
                  @update:model-value="clearError('warehouse_id')"
                />
              </div>

              <!-- Số lượng nhập -->
              <FormField
                v-model="form.quantity"
                label="Số lượng nhập"
                name="quantity"
                type="number"
                :error="errors.quantity"
                required
                :min="1"
                @update:model-value="clearError('quantity')"
              />
            </div>
          </div>

          <!-- Thông tin lô hàng -->
          <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin lô hàng</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Mã lô hàng -->
              <FormField
                v-model="form.batch_no"
                label="Mã lô hàng"
                name="batch_no"
                :error="errors.batch_no"
                placeholder="Nhập mã lô hàng"
                @update:model-value="clearError('batch_no')"
              />

              <!-- Số lô sản xuất -->
              <FormField
                v-model="form.lot_no"
                label="Số lô sản xuất"
                name="lot_no"
                :error="errors.lot_no"
                placeholder="Nhập số lô sản xuất"
                @update:model-value="clearError('lot_no')"
              />

              <!-- Hạn sử dụng -->
              <FormField
                v-model="form.expiry_date"
                label="Hạn sử dụng"
                name="expiry_date"
                type="date"
                :error="errors.expiry_date"
                @update:model-value="clearError('expiry_date')"
              />

              <!-- Giá vốn -->
              <FormField
                v-model="form.cost_price"
                label="Giá vốn"
                name="cost_price"
                type="number"
                :error="errors.cost_price"
                :min="0"
                :step="0.01"
                placeholder="Nhập giá vốn"
                @update:model-value="clearError('cost_price')"
              />
            </div>
          </div>
        </div>
      </template>
    </FormWrapper>
  </Modal>
</template>

<script setup>
import Modal from '@/components/Core/Modal.vue'
import FormWrapper from '@/components/Core/FormWrapper.vue'
import FormField from '@/components/Core/FormField.vue'
import { ref, computed, watch } from 'vue'
import { useFormDefaults } from '@/utils/useFormDefaults'

const props = defineProps({
  show: Boolean,
  apiErrors: {
    type: Object,
    default: () => ({})
  },
  products: {
    type: Array,
    default: () => []
  },
  warehouses: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['submit', 'cancel'])

const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})
const formWrapperRef = ref(null)

// Default values for form
const defaultValues = useFormDefaults(props, 'import', {
  product_id: '',
  warehouse_id: '',
  quantity: 1,
  batch_no: '',
  lot_no: '',
  expiry_date: '',
  cost_price: null
})

// Validation rules
const validationRules = computed(() => ({
  product_id: [
    { required: 'Vui lòng chọn sản phẩm.' }
  ],
  warehouse_id: [
    { required: 'Vui lòng chọn kho hàng.' }
  ],
  quantity: [
    { required: 'Vui lòng nhập số lượng nhập kho.' },
    { integer: 'Số lượng phải là số nguyên.' },
    { min: [1, 'Số lượng nhập kho phải lớn hơn 0.'] }
  ],
  batch_no: [
    { max: [100, 'Mã lô hàng không được vượt quá 100 ký tự.'] }
  ],
  lot_no: [
    { max: [100, 'Số lô sản xuất không được vượt quá 100 ký tự.'] }
  ],
  expiry_date: [
    { date: 'Hạn sử dụng không đúng định dạng.' },
    { after: ['today', 'Hạn sử dụng phải sau ngày hiện tại.'] }
  ],
  cost_price: [
    { numeric: 'Giá vốn phải là số.' },
    { min: [0, 'Giá vốn không được âm.'] }
  ]
}))

// Computed options for selects
const productOptions = computed(() => {
  return [
    { value: '', label: 'Chọn sản phẩm' },
    ...props.products.map(product => ({
      value: product.id,
      label: `${product.name} (${product.code})`
    }))
  ]
})

const warehouseOptions = computed(() => {
  return [
    { value: '', label: 'Chọn kho hàng' },
    ...props.warehouses.map(warehouse => ({
      value: warehouse.id,
      label: warehouse.name
    }))
  ]
})



// Handle form submission
function handleSubmit(formData) {
  emit('submit', formData)
}

// Handle modal close
function onClose() {
  emit('cancel')
}
</script> 
