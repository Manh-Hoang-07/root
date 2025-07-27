<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Test Upload Ảnh</h1>
    
    <div class="bg-white shadow-md rounded-lg p-6">
      <h2 class="text-lg font-semibold mb-4">Upload Ảnh Test</h2>
      
      <div class="space-y-4">
        <!-- File Input -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Chọn ảnh
          </label>
          <input 
            type="file" 
            @change="onFileChange" 
            accept="image/*"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
          />
        </div>

        <!-- Preview -->
        <div v-if="previewUrl" class="mt-4">
          <h3 class="text-md font-medium mb-2">Preview:</h3>
          <img 
            :src="previewUrl" 
            alt="preview" 
            class="w-32 h-32 object-cover rounded border"
          />
        </div>

        <!-- Upload Button -->
        <div v-if="selectedFile && !uploadedUrl" class="space-x-2">
          <button 
            @click="uploadImage"
            :disabled="uploading"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none disabled:opacity-50"
          >
            {{ uploading ? 'Đang upload...' : 'Upload Ảnh' }}
          </button>
          
          <button 
            @click="testUpload"
            :disabled="uploading"
            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none disabled:opacity-50"
          >
            {{ uploading ? 'Đang test...' : 'Test Upload' }}
          </button>
        </div>

        <!-- Upload Result -->
        <div v-if="uploadedUrl" class="mt-4 p-4 bg-green-50 border border-green-200 rounded">
          <h3 class="text-md font-medium text-green-800 mb-2">Upload Thành Công!</h3>
          <p class="text-sm text-green-700 mb-2">URL: {{ uploadedUrl }}</p>
          <img 
            :src="uploadedUrl" 
            alt="uploaded" 
            class="w-32 h-32 object-cover rounded border"
          />
        </div>

        <!-- Error -->
        <div v-if="error" class="mt-4 p-4 bg-red-50 border border-red-200 rounded">
          <h3 class="text-md font-medium text-red-800 mb-2">Lỗi Upload!</h3>
          <p class="text-sm text-red-700">{{ error }}</p>
        </div>

        <!-- Debug Info -->
        <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded">
          <h3 class="text-md font-medium text-gray-800 mb-2">Debug Info:</h3>
          <pre class="text-xs text-gray-600 overflow-auto">{{ debugInfo }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import api from '@/api/apiClient'

const selectedFile = ref(null)
const previewUrl = ref(null)
const uploadedUrl = ref(null)
const uploading = ref(false)
const error = ref('')
const responseData = ref(null)

const debugInfo = computed(() => ({
  selectedFile: selectedFile.value ? {
    name: selectedFile.value.name,
    size: selectedFile.value.size,
    type: selectedFile.value.type
  } : null,
  previewUrl: previewUrl.value,
  uploadedUrl: uploadedUrl.value,
  uploading: uploading.value,
  error: error.value,
  responseData: responseData.value
}))

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    selectedFile.value = file
    previewUrl.value = URL.createObjectURL(file)
    uploadedUrl.value = null
    error.value = ''
    responseData.value = null
  }
}

async function uploadImage() {
  if (!selectedFile.value) return

  uploading.value = true
  error.value = ''
  responseData.value = null

  try {
    const formData = new FormData()
    formData.append('image', selectedFile.value)

    console.log('Uploading file:', selectedFile.value.name)
    
    const res = await api.post('/api/upload-image', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    console.log('Upload response:', res.data)
    responseData.value = res.data

    // Kiểm tra response structure
    const data = res.data.data || res.data
    const url = data.url || data.path || data.image

    if (url) {
      uploadedUrl.value = url
      console.log('Upload successful, URL:', url)
    } else {
      console.error('No URL in response:', res.data)
      error.value = 'Upload thất bại: Không nhận được URL!'
    }

  } catch (err) {
    console.error('Upload error:', err)
    responseData.value = err.response?.data || err.message
    
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

async function testUpload() {
  if (!selectedFile.value) return

  uploading.value = true
  error.value = ''
  responseData.value = null

  try {
    const formData = new FormData()
    formData.append('image', selectedFile.value)

    console.log('Testing upload with file:', selectedFile.value.name)
    
    const res = await api.post('/api/test-upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    console.log('Test response:', res.data)
    responseData.value = res.data

  } catch (err) {
    console.error('Test error:', err)
    responseData.value = err.response?.data || err.message
    
    if (err.response?.data?.message) {
      error.value = `Test thất bại: ${err.response.data.message}`
    } else if (err.message) {
      error.value = `Test thất bại: ${err.message}`
    } else {
      error.value = 'Test thất bại!'
    }
  } finally {
    uploading.value = false
  }
}
</script> 