<template>
  <div class="optimized-image" :class="containerClass">
    <!-- Placeholder -->
    <div 
      v-if="showPlaceholder"
      class="placeholder"
      :style="placeholderStyle"
      :class="placeholderClass"
    >
      <div v-if="placeholderText" class="placeholder-text">
        {{ placeholderText }}
      </div>
    </div>
    
    <!-- Actual Image -->
    <img
      v-show="!showPlaceholder"
      ref="imgRef"
      :src="currentSrc"
      :alt="alt"
      :class="imageClass"
      :style="imageStyle"
      :loading="lazy ? 'lazy' : 'eager'"
      :sizes="sizes"
      :srcset="srcset"
      @load="handleImageLoad"
      @error="handleImageError"
      @click="$emit('click', $event)"
    />
    
    <!-- Loading Spinner -->
    <div 
      v-if="loading && !showPlaceholder"
      class="loading-spinner"
      :class="spinnerClass"
    >
      <svg class="animate-spin" viewBox="0 0 24 24">
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
          fill="none"
        />
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        />
      </svg>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { 
  lazyLoadImage, 
  generateSrcSet, 
  generateSizes, 
  optimizeImageUrl,
  generatePlaceholder 
} from '@/utils/imageOptimization'

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: ''
  },
  width: {
    type: [Number, String],
    default: null
  },
  height: {
    type: [Number, String],
    default: null
  },
  lazy: {
    type: Boolean,
    default: true
  },
  placeholder: {
    type: Boolean,
    default: true
  },
  placeholderText: {
    type: String,
    default: ''
  },
  responsive: {
    type: Boolean,
    default: true
  },
  sizes: {
    type: Array,
    default: () => [320, 640, 960, 1280]
  },
  quality: {
    type: Number,
    default: 80
  },
  format: {
    type: String,
    default: 'webp'
  },
  fit: {
    type: String,
    default: 'cover'
  },
  containerClass: {
    type: String,
    default: ''
  },
  imageClass: {
    type: String,
    default: ''
  },
  placeholderClass: {
    type: String,
    default: ''
  },
  spinnerClass: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['load', 'error', 'click'])

// Reactive state
const imgRef = ref(null)
const loading = ref(true)
const loaded = ref(false)
const error = ref(false)
const placeholderSrc = ref('')

// Computed properties
const currentSrc = computed(() => {
  if (error.value) return ''
  return optimizeImageUrl(props.src, {
    width: props.width,
    height: props.height,
    quality: props.quality,
    format: props.format,
    fit: props.fit
  })
})

const srcset = computed(() => {
  if (!props.responsive) return ''
  return generateSrcSet(props.src, props.sizes)
})

const sizes = computed(() => {
  if (!props.responsive) return ''
  return generateSizes()
})

const showPlaceholder = computed(() => {
  return props.placeholder && !loaded.value && !error.value
})

const placeholderStyle = computed(() => {
  const style = {}
  if (props.width) style.width = typeof props.width === 'number' ? `${props.width}px` : props.width
  if (props.height) style.height = typeof props.height === 'number' ? `${props.height}px` : props.height
  return style
})

const imageStyle = computed(() => {
  const style = {}
  if (props.width) style.width = typeof props.width === 'number' ? `${props.width}px` : props.width
  if (props.height) style.height = typeof props.height === 'number' ? `${props.height}px` : props.height
  return style
})

// Methods
const handleImageLoad = () => {
  loading.value = false
  loaded.value = true
  emit('load')
}

const handleImageError = () => {
  loading.value = false
  error.value = true
  emit('error')
}

const generatePlaceholderImage = async () => {
  if (!props.placeholder) return
  
  const width = typeof props.width === 'number' ? props.width : 300
  const height = typeof props.height === 'number' ? props.height : 200
  
  placeholderSrc.value = generatePlaceholder(width, height, props.placeholderText)
}

// Lifecycle
onMounted(async () => {
  await generatePlaceholderImage()
  
  if (props.lazy && imgRef.value) {
    lazyLoadImage(imgRef.value)
  }
})

// Watch for src changes
watch(() => props.src, async () => {
  loading.value = true
  loaded.value = false
  error.value = false
  
  if (props.lazy && imgRef.value) {
    lazyLoadImage(imgRef.value)
  }
})
</script>

<style scoped>
.optimized-image {
  position: relative;
  display: inline-block;
  overflow: hidden;
}

.optimized-image img {
  display: block;
  max-width: 100%;
  height: auto;
  transition: opacity 0.3s ease;
}

.placeholder {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #999;
  font-size: 14px;
}

.placeholder-text {
  text-align: center;
  padding: 20px;
}

.loading-spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #6b7280;
}

.loading-spinner svg {
  width: 24px;
  height: 24px;
}

@keyframes loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Responsive image styles */
.optimized-image img[srcset] {
  width: 100%;
  height: auto;
}

/* Lazy loading styles */
.optimized-image img.lazy {
  opacity: 0;
  transition: opacity 0.3s;
}

.optimized-image img.lazy.loaded {
  opacity: 1;
}
</style> 