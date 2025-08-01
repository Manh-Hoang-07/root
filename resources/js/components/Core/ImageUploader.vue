<template>
  <div>
    <div v-if="previewUrl" class="mb-2 relative inline-block">
      <img
        :src="previewUrl"
        alt="preview"
        class="w-20 h-20 object-cover rounded-full border"
        @error="onImgError"
      />
      <button
        type="button"
        @click="removeImage"
        class="absolute top-0 right-0 bg-white bg-opacity-80 rounded-full p-1 shadow hover:bg-red-100 transition"
        style="transform: translate(40%, -40%);"
        title="Xóa ảnh"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <div v-else class="w-20 h-20 flex items-center justify-center bg-gray-100 rounded-full border text-gray-400">
      <span>No Image</span>
    </div>
    <input type="file" @change="onFileChange" accept="image/*" />
    <div v-if="uploading" class="text-xs text-gray-500 mt-1">Đang upload ảnh...</div>
    <div v-if="error" class="text-xs text-red-500 mt-1">{{ error }}</div>
  </div>
</template>
<script setup>
import { ref, watch } from 'vue'
import api from '@/api/apiClient'
const props = defineProps({
  modelValue: File || String || null,
  defaultUrl: String || null
})
const emit = defineEmits(['update:modelValue', 'remove'])
const previewUrl = ref(null)
const uploading = ref(false)
const error = ref('')

watch(() => props.modelValue, (val) => {
  if (val instanceof File) {
    previewUrl.value = URL.createObjectURL(val)
  } else if (typeof val === 'string' && val) {
    previewUrl.value = getImageUrl(val)
  } else {
    previewUrl.value = props.defaultUrl ? getImageUrl(props.defaultUrl) : null
  }
}, { immediate: true })

function getImageUrl(url) {
  if (!url) return null
  if (url.startsWith('http')) return url
  if (url.startsWith('/storage/')) return url.replace(/^(\/storage\/)+/, '/storage/')
  return `/storage/${url.replace(/^\/storage\//, '')}`
}

async function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    uploading.value = true
    error.value = ''
    try {
      const formData = new FormData()
      formData.append('image', file)
      const res = await api.post('/api/upload-image', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      
      // Kiểm tra response structure
      const responseData = res.data.data || res.data
      const url = responseData.url || responseData.path || responseData.image
      
      if (url) {
        emit('update:modelValue', url)
        previewUrl.value = getImageUrl(url)
      } else {
        console.error('No URL in response:', res.data)
        error.value = 'Upload thất bại: Không nhận được URL!'
      }
    } catch (err) {
      console.error('Upload error:', err)
      if (err.response?.data?.message) {
        error.value = `Upload thất bại: ${err.response.data.message}`
      } else if (err.message) {
        error.value = `Upload thất bại: ${err.message}`
      } else {
        error.value = 'Upload thất bại!'
      }
    } finally {
      uploading.value = false
    }
  }
}
function removeImage() {
  emit('update:modelValue', null)
  emit('remove')
  previewUrl.value = null
}
function onImgError(e) {
  e.target.onerror = null; // Ngăn vòng lặp lỗi
  previewUrl.value = null; // Không set src về placeholder nữa
}
</script> 