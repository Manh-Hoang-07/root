<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-y-auto max-h-screen" style="touch-action: pan-y; -webkit-overflow-scrolling: touch;">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Chỉnh sửa vai trò</h3>
      </div>
      <form @submit.prevent="submit" class="p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Tên vai trò</label>
          <input v-model="form.name" required class="w-full px-3 py-2 border rounded-lg" placeholder="Tên vai trò" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Tên hiển thị</label>
          <input v-model="form.display_name" required class="w-full px-3 py-2 border rounded-lg" placeholder="Tên hiển thị" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Guard</label>
          <input v-model="form.guard_name" class="w-full px-3 py-2 border rounded-lg" placeholder="Guard (mặc định: web)" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Vai trò cha</label>
          <select v-model="form.parent_id" class="w-full px-3 py-2 border rounded-lg">
            <option value="">-- Không có --</option>
            <option v-for="r in parentOptions" :key="r.id" :value="r.id" :disabled="r.id === props.role.id">
              {{ r.display_name || r.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Phân quyền</label>
          <Multiselect
            v-model="form.permissions"
            :options="permissionOptions"
            :multiple="true"
            :searchable="true"
            :close-on-select="false"
            placeholder="Chọn quyền"
            label="name"
            track-by="id"
          >
            <template #option="{ option }">
              <span>{{ option.display_name }} <span style="color:#888;font-size:12px">({{ option.name }})</span></span>
            </template>
            <template #tag="{ option, remove }">
              <span class="multiselect__tag">
                {{ option.display_name || option.name }}
                <span @click="remove(option)" class="multiselect__tag-icon">×</span>
              </span>
            </template>
          </Multiselect>
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Hủy</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import Multiselect from 'vue-multiselect'
const emit = defineEmits(['close', 'saved'])
const props = defineProps({ role: Object })
const form = ref({ name: '', display_name: '', guard_name: '', parent_id: '', permissions: [] })
const parentOptions = ref([])
const permissionOptions = ref([])

const fetchParentOptions = async () => {
  const res = await axios.get('/api/admin/roles', { params: { per_page: 1000 } })
  parentOptions.value = res.data.data.filter(r => r.id !== props.role.id)
}

const fetchPermissionOptions = async () => {
  const res = await axios.get('/api/admin/permissions', { params: { per_page: 1000 } })
  permissionOptions.value = res.data.data
  console.log('permissionOptions', permissionOptions.value)
}

const setFormFromRole = (val) => {
  if (val) form.value = {
    name: val.name,
    display_name: val.display_name,
    guard_name: val.guard_name,
    parent_id: val.parent_id || '',
    permissions: (val.permissions || []).map(p => ({
      id: p.id,
      name: p.name,
      display_name: p.display_name
    }))
  }
}

watch(() => props.role, (val) => {
  setFormFromRole(val)
  fetchParentOptions()
  fetchPermissionOptions()
}, { immediate: true })

const submit = async () => {
  try {
    await axios.put(`/api/admin/roles/${props.role.id}`, {
      ...form.value,
      permissions: form.value.permissions.map(p => p.id)
    })
    emit('saved')
    emit('close')
  } catch (e) {
    alert('Cập nhật vai trò thất bại!')
  }
}

// Hàm lấy label cho permission
const permissionLabel = (p) => p.display_name || p.name || `#${p.id}`;
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