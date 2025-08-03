/**
 * Image Optimization Utilities
 */

// Lazy loading observer
let imageObserver = null

// Initialize intersection observer for lazy loading
const initImageObserver = () => {
  if (imageObserver) return imageObserver
  
  imageObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target
        img.src = img.dataset.src
        img.classList.remove('lazy')
        imageObserver.unobserve(img)
      }
    })
  }, {
    rootMargin: '50px 0px',
    threshold: 0.01
  })
  
  return imageObserver
}

// Lazy load image
export const lazyLoadImage = (imgElement) => {
  const observer = initImageObserver()
  observer.observe(imgElement)
}

// Generate responsive image srcset
export const generateSrcSet = (imageUrl, sizes = [320, 640, 960, 1280]) => {
  if (!imageUrl) return ''
  
  return sizes
    .map(size => `${imageUrl}?w=${size} ${size}w`)
    .join(', ')
}

// Generate responsive image sizes attribute
export const generateSizes = (breakpoints = {
  sm: '640px',
  md: '768px',
  lg: '1024px',
  xl: '1280px'
}) => {
  return Object.entries(breakpoints)
    .map(([breakpoint, width]) => `(min-width: ${width}) ${width}`)
    .join(', ') + ', 100vw'
}

// Optimize image URL with parameters
export const optimizeImageUrl = (url, options = {}) => {
  if (!url) return ''
  
  const {
    width,
    height,
    quality = 80,
    format = 'webp',
    fit = 'cover'
  } = options
  
  const params = new URLSearchParams()
  
  if (width) params.append('w', width)
  if (height) params.append('h', height)
  if (quality) params.append('q', quality)
  if (format) params.append('f', format)
  if (fit) params.append('fit', fit)
  
  const separator = url.includes('?') ? '&' : '?'
  return `${url}${separator}${params.toString()}`
}

// Preload critical images
export const preloadImage = (src) => {
  return new Promise((resolve, reject) => {
    const img = new Image()
    img.onload = () => resolve(img)
    img.onerror = reject
    img.src = src
  })
}

// Preload multiple images
export const preloadImages = (srcs) => {
  return Promise.all(srcs.map(src => preloadImage(src)))
}

// Get image dimensions
export const getImageDimensions = (file) => {
  return new Promise((resolve) => {
    const img = new Image()
    img.onload = () => {
      resolve({
        width: img.naturalWidth,
        height: img.naturalHeight,
        aspectRatio: img.naturalWidth / img.naturalHeight
      })
    }
    img.src = URL.createObjectURL(file)
  })
}

// Compress image before upload
export const compressImage = (file, options = {}) => {
  const {
    maxWidth = 1920,
    maxHeight = 1080,
    quality = 0.8,
    format = 'jpeg'
  } = options
  
  return new Promise((resolve) => {
    const canvas = document.createElement('canvas')
    const ctx = canvas.getContext('2d')
    const img = new Image()
    
    img.onload = () => {
      // Calculate new dimensions
      let { width, height } = img
      
      if (width > maxWidth) {
        height = (height * maxWidth) / width
        width = maxWidth
      }
      
      if (height > maxHeight) {
        width = (width * maxHeight) / height
        height = maxHeight
      }
      
      // Set canvas dimensions
      canvas.width = width
      canvas.height = height
      
      // Draw and compress
      ctx.drawImage(img, 0, 0, width, height)
      
      canvas.toBlob((blob) => {
        resolve(new File([blob], file.name, {
          type: `image/${format}`,
          lastModified: Date.now()
        }))
      }, `image/${format}`, quality)
    }
    
    img.src = URL.createObjectURL(file)
  })
}

// Generate placeholder for image
export const generatePlaceholder = (width, height, text = '') => {
  const canvas = document.createElement('canvas')
  const ctx = canvas.getContext('2d')
  
  canvas.width = width
  canvas.height = height
  
  // Background
  ctx.fillStyle = '#f3f4f6'
  ctx.fillRect(0, 0, width, height)
  
  // Text
  if (text) {
    ctx.fillStyle = '#9ca3af'
    ctx.font = '14px Arial'
    ctx.textAlign = 'center'
    ctx.textBaseline = 'middle'
    ctx.fillText(text, width / 2, height / 2)
  }
  
  return canvas.toDataURL()
}

// Check if image is loaded
export const isImageLoaded = (src) => {
  return new Promise((resolve) => {
    const img = new Image()
    img.onload = () => resolve(true)
    img.onerror = () => resolve(false)
    img.src = src
  })
}

// Get dominant color from image
export const getDominantColor = (imageUrl) => {
  return new Promise((resolve) => {
    const img = new Image()
    img.crossOrigin = 'anonymous'
    
    img.onload = () => {
      const canvas = document.createElement('canvas')
      const ctx = canvas.getContext('2d')
      
      canvas.width = img.width
      canvas.height = img.height
      
      ctx.drawImage(img, 0, 0)
      
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height).data
      const colorCounts = {}
      
      for (let i = 0; i < imageData.length; i += 4) {
        const r = imageData[i]
        const g = imageData[i + 1]
        const b = imageData[i + 2]
        const rgb = `${r},${g},${b}`
        
        colorCounts[rgb] = (colorCounts[rgb] || 0) + 1
      }
      
      const dominantColor = Object.keys(colorCounts).reduce((a, b) => 
        colorCounts[a] > colorCounts[b] ? a : b
      )
      
      resolve(dominantColor)
    }
    
    img.onerror = () => resolve(null)
    img.src = imageUrl
  })
} 