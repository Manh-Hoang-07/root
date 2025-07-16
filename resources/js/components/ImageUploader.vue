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
        <!-- Icon X -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <input type="file" @change="onFileChange" accept="image/*" />
  </div>
</template>
<script setup>
import { ref, watch } from 'vue'
const props = defineProps({
  modelValue: File || String || null,
  defaultUrl: String || null
})
const emit = defineEmits(['update:modelValue', 'remove'])
const previewUrl = ref(null)

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
  if (url.startsWith('http') || url.startsWith('/storage/')) return url
  return `/storage/${url.replace(/^\\|^\//, '')}`
}

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    emit('update:modelValue', file)
    previewUrl.value = URL.createObjectURL(file)
  }
}
function removeImage() {
  emit('update:modelValue', null)
  emit('remove')
  previewUrl.value = null
}
function onImgError(e) {
  e.target.src = 'https://via.placeholder.com/80?text=No+Image'
}
</script> 