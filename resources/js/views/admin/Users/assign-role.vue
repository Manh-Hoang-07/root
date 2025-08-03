<template>
  <Modal v-model="modalVisible" title="Phân quyền người dùng">
    <FormWrapper
      :default-values="defaultValues"
      :rules="validationRules"
      :api-errors="apiErrors"
      submit-text="Cập nhật quyền"
      @submit="handleSubmit"
      @cancel="onClose"
    >
      <template #default="{ form, errors, clearError, isSubmitting }">
                 <!-- Thông tin user -->
         <div class="mb-4 p-3 bg-gray-50 rounded-lg">
           <div class="text-sm text-gray-600">
             <div><strong>Tên:</strong> {{ userDetail?.name || userDetail?.username || 'N/A' }}</div>
             <div><strong>Email:</strong> {{ userDetail?.email || 'N/A' }}</div>
           </div>
         </div>
        
                 
         
                   <!-- Vai trò -->
          <MultipleSelect
            v-model="form.role_ids"
            label="Vai trò"
            :options="roleOptions"
            :error="errors.role_ids"
            placeholder="Chọn vai trò..."
            @update:model-value="clearError('role_ids')"
          />
      </template>
    </FormWrapper>
  </Modal>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Modal from '@/components/Core/Modal.vue'
import FormWrapper from '@/components/Core/FormWrapper.vue'
import MultipleSelect from '@/components/Core/MultipleSelect.vue'
import endpoints from '@/api/endpoints'
import { useApiFormSubmit } from '@/utils/useApiFormSubmit'
import apiClient from '@/api/apiClient'

const props = defineProps({
  show: Boolean,
  user: Object,
  onClose: Function
})

const emit = defineEmits(['role-assigned'])

const userDetail = ref(null)
const roles = ref([])

const formTitle = computed(() => `Phân quyền cho ${userDetail.value?.name || userDetail.value?.username || 'người dùng'}`)
const modalVisible = computed({
  get: () => props.show,
  set: () => onClose()
})

// Watch show prop để fetch user detail và roles
watch(() => props.show, async (newValue) => {
  if (newValue && props.user?.id) {
    await Promise.all([
      fetchUserDetail(),
      loadRoles()
    ])
  }
}, { immediate: true })

async function fetchUserDetail() {
  try {
    const response = await apiClient.get(endpoints.users.showWithRoles(props.user.id))
    if (response.data.success) {
      userDetail.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching user detail:', error)
  }
}

async function loadRoles() {
  try {
    const response = await apiClient.get(endpoints.roles.list)
    if (response.data.success) {
      roles.value = response.data.data
    } else {
      console.error('Roles API returned success: false')
    }
  } catch (error) {
    console.error('Error loading roles:', error)
  }
}

const defaultValues = computed(() => {
  const obj = userDetail.value || {}
  
  // Extract role_ids from roles array if exists
  let roleIds = []
  if (obj.roles && Array.isArray(obj.roles)) {
    roleIds = obj.roles.map(role => role.id)
  }
  
  return {
    role_ids: roleIds,
    ...obj
  }
})

const { apiErrors, submit } = useApiFormSubmit({
  endpoint: endpoints.users.assignRoles(props.user?.id),
  emit,
  onClose: props.onClose,
  eventName: 'role-assigned',
  method: 'post'
})

const validationRules = computed(() => ({
  role_ids: [
    { required: 'Vui lòng chọn ít nhất một vai trò.' }
  ]
}))

const roleOptions = computed(() => {
  return (roles.value || []).map(opt => ({
    value: opt.id,
    label: opt.name
  }))
})

async function handleSubmit(formData) {
  // Chỉ gửi role_ids để cập nhật
  const dataToSubmit = {
    role_ids: Array.isArray(formData.role_ids) ? formData.role_ids : [formData.role_ids].filter(Boolean)
  }
  
  await submit(dataToSubmit)
}

function onClose() {
  emit('role-assigned')
}
</script> 