<template>
  <Modal v-model="modalVisible" :title="formTitle">
    <form @submit.prevent="validateAndSubmit" class="space-y-4">
      <!-- Tên vai trò -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên vai trò <span class="text-red-500">*</span></label>
        <input
          id="name"
          v-model="formData.name"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.name || apiErrors.name }"
        />
        <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600">{{ validationErrors.name }}</p>
        <p v-else-if="apiErrors.name" class="mt-1 text-sm text-red-600">{{ apiErrors.name }}</p>
      </div>

      <!-- Tên hiển thị -->
      <div>
        <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Tên hiển thị</label>
        <input
          id="display_name"
          v-model="formData.display_name"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.display_name || apiErrors.display_name }"
        />
        <p v-if="validationErrors.display_name" class="mt-1 text-sm text-red-600">{{ validationErrors.display_name }}</p>
        <p v-else-if="apiErrors.display_name" class="mt-1 text-sm text-red-600">{{ apiErrors.display_name }}</p>
      </div>

      <!-- Guard -->
      <div>
        <label for="guard_name" class="block text-sm font-medium text-gray-700 mb-1">Guard</label>
        <input
          id="guard_name"
          v-model="formData.guard_name"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.guard_name || apiErrors.guard_name }"
        />
        <p v-if="validationErrors.guard_name" class="mt-1 text-sm text-red-600">{{ validationErrors.guard_name }}</p>
        <p v-else-if="apiErrors.guard_name" class="mt-1 text-sm text-red-600">{{ apiErrors.guard_name }}</p>
      </div>

      <!-- Vai trò cha -->
      <div>
        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Vai trò cha</label>
        <select
          id="parent_id"
          v-model="formData.parent_id"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.parent_id || apiErrors.parent_id }"
        >
          <option value="">-- Không có --</option>
          <option v-for="r in parentOptions" :key="r.id" :value="r.id">
            {{ r.display_name || r.name }}
          </option>
        </select>
        <p v-if="validationErrors.parent_id" class="mt-1 text-sm text-red-600">{{ validationErrors.parent_id }}</p>
        <p v-else-if="apiErrors.parent_id" class="mt-1 text-sm text-red-600">{{ apiErrors.parent_id }}</p>
      </div>

      <!-- Trạng thái -->
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
        <select
          id="status"
          v-model="formData.status"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :class="{ 'border-red-500': validationErrors.status || apiErrors.status }"
        >
          <option v-for="(label, value) in statusOptions" :key="value" :value="value">
            {{ label }}
          </option>
        </select>
        <p v-if="validationErrors.status" class="mt-1 text-sm text-red-600">{{ validationErrors.status }}</p>
        <p v-else-if="apiErrors.status" class="mt-1 text-sm text-red-600">{{ apiErrors.status }}</p>
      </div>

      <!-- Quyền -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Quyền</label>
        <div v-if="loadingPermissions" class="text-center py-4 text-gray-500">
          Đang tải danh sách quyền...
        </div>
        <div v-else>
          <Multiselect
            v-model="formData.permissions"
            :options="permissionOptions"
            :multiple="true"
            :searchable="true"
            :close-on-select="false"
            :clear-on-select="false"
            :preserve-search="true"
            placeholder="Chọn quyền..."
            label="label"
            track-by="value"
            :preselect-first="false"
            :class="{ 'border-red-500': validationErrors.permissions || apiErrors.permissions }"
            @input="handlePermissionsChange"
          />
        </div>
        <p v-if="validationErrors.permissions" class="mt-1 text-sm text-red-600">{{ validationErrors.permissions }}</p>
        <p v-else-if="apiErrors.permissions" class="mt-1 text-sm text-red-600">{{ apiErrors.permissions }}</p>
      </div>

      <!-- Buttons -->
      <div class="flex justify-end space-x-3 pt-4">
        <button
          type="button"
          @click="onClose"
          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
        >
          Hủy
        </button>
        <button
          type="submit"
          class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none"
          :disabled="isSubmitting"
        >
          {{ isSubmitting ? 'Đang xử lý...' : (role ? 'Cập nhật' : 'Thêm mới') }}
        </button>
      </div>
    </form>
  </Modal>
</template>

<script setup>
import { ref, computed, reactive, watch, onMounted } from 'vue'
import Modal from '@/components/Core/Modal.vue'
import Multiselect from 'vue-multiselect'
import endpoints from '@/api/endpoints'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  role: Object,
  statusEnums: {
    type: Array,
    default: () => []
  },
  apiErrors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['submit', 'cancel'])

// Status options từ props
const statusOptions = computed(() => {
  const options = {}
  if (Array.isArray(props.statusEnums)) {
    props.statusEnums.forEach(enumItem => {
      options[enumItem.id] = enumItem.name
    })
  }
  return options
})

// Permission options cho multiselect
const permissionOptions = computed(() => {
  return permissions.value.map(permission => ({
    value: permission.id,
    label: permission.display_name || permission.name
  }))
})

// Lấy danh sách vai trò cha
const parentOptions = ref([])
const fetchParentOptions = async () => {
  try {
    const response = await axios.get(endpoints.roles.list, { params: { per_page: 1000 } })
    parentOptions.value = response.data.data || []
  } catch (error) {
    parentOptions.value = []
  }
}

// Lấy danh sách quyền
const permissions = ref([])
const loadingPermissions = ref(false)
const fetchPermissions = async () => {
  loadingPermissions.value = true
  try {
    const response = await axios.get(endpoints.permissions.list, { params: { per_page: 1000 } })
    permissions.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching permissions:', error)
    permissions.value = []
  } finally {
    loadingPermissions.value = false
  }
}

onMounted(() => {
  fetchParentOptions()
  fetchPermissions()
})

// Form title
const formTitle = computed(() => props.role ? 'Chỉnh sửa vai trò' : 'Thêm vai trò mới')

// Modal visibility
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})

// Form data
const formData = reactive({
  name: '',
  display_name: '',
  guard_name: '',
  parent_id: '',
  status: 'active',
  permissions: []
})

// Form state
const validationErrors = reactive({})
const isSubmitting = ref(false)

// Watch role prop to update form data
watch(() => props.role, (newVal) => {
  if (newVal) {
    formData.name = newVal.name || ''
    formData.display_name = newVal.display_name || ''
    formData.guard_name = newVal.guard_name || ''
    formData.parent_id = newVal.parent_id || ''
    formData.status = newVal.status || 'active'
    // Xử lý permissions
    if (newVal.permissions && Array.isArray(newVal.permissions)) {
      // Chuyển đổi permissions thành array of objects cho multiselect
      formData.permissions = newVal.permissions.map(p => ({
        value: p.id || p,
        label: p.display_name || p.name
      }))
    } else {
      formData.permissions = []
    }
  } else {
    resetForm()
  }
}, { immediate: true })

// Reset form
function resetForm() {
  formData.name = ''
  formData.display_name = ''
  formData.guard_name = ''
  formData.parent_id = ''
  formData.status = 'active'
  formData.permissions = []
  clearErrors()
}

// Clear errors
function clearErrors() {
  Object.keys(validationErrors).forEach(key => delete validationErrors[key])
}

// Validation rules
const validationRules = computed(() => ({
  name: [
    { required: 'Tên vai trò là bắt buộc' }
  ],
  display_name: [
    { required: 'Tên hiển thị là bắt buộc' }
  ]
}))

function validateForm() {
  clearErrors()
  let valid = true
  const rules = validationRules.value
  for (const field in rules) {
    for (const rule of rules[field]) {
      if (rule.required && !formData[field]) {
        validationErrors[field] = rule.required
        valid = false
        break
      }
    }
  }
  return valid
}

// Validate and submit form
function validateAndSubmit() {
  if (!validateForm()) {
    return
  }
  
  isSubmitting.value = true
  
  try {
    // Tạo object data thay vì FormData
    const submitData = {
      name: formData.name,
      display_name: formData.display_name,
      guard_name: formData.guard_name,
      parent_id: formData.parent_id || null,
      status: formData.status,
      permissions: []
    }
    
    // Thêm permissions
    if (formData.permissions && formData.permissions.length > 0) {
      // Đảm bảo permissions là array of IDs
      submitData.permissions = formData.permissions.map(permission => {
        return typeof permission === 'object' ? permission.value : permission
      })
    }
    
    emit('submit', submitData)
  } finally {
    isSubmitting.value = false
  }
}

// Handle permissions change
function handlePermissionsChange(selectedPermissions) {
  // Giữ nguyên format objects cho multiselect
  formData.permissions = selectedPermissions || []
}

// Close modal
function onClose() {
  emit('cancel')
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style>
.multiselect__tag, .multiselect__single, .multiselect__option {
  color: #222 !important;
  font-size: 14px !important;
  min-width: 40px;
}
/* Thêm scroll cho vùng tag khi tràn */
.multiselect__tags {
  max-height: 90px;
  overflow-y: auto;
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
}
</style> 
