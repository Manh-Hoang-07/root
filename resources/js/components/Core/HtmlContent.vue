<template>
  <div class="html-content">
    <div 
      v-if="content" 
      class="content-preview"
      :class="[`line-clamp-${maxLines}`, { 'cursor-pointer': clickable }]"
      v-html="sanitizedContent"
      @click="handleClick"
    ></div>
    <span v-else class="text-gray-400 italic">{{ placeholder || 'Không có nội dung' }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  content: {
    type: String,
    default: ''
  },
  maxLines: {
    type: Number,
    default: 3
  },
  placeholder: {
    type: String,
    default: 'Không có nội dung'
  },
  clickable: {
    type: Boolean,
    default: false
  },
  allowHtml: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['click'])

const sanitizedContent = computed(() => {
  if (!props.content) return ''
  
  if (!props.allowHtml) {
    // Strip HTML tags
    return stripHtml(props.content)
  }
  
  // Basic HTML sanitization - chỉ cho phép các tag an toàn
  const allowedTags = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'code', 'pre']
  const allowedAttributes = ['class', 'style']
  
  let sanitized = props.content
  
  // Remove script tags and event handlers
  sanitized = sanitized.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
  sanitized = sanitized.replace(/\s*on\w+\s*=\s*["'][^"']*["']/gi, '')
  
  // Only allow specific tags
  const tempDiv = document.createElement('div')
  tempDiv.innerHTML = sanitized
  
  // Remove disallowed tags
  const allTags = tempDiv.querySelectorAll('*')
  allTags.forEach(tag => {
    if (!allowedTags.includes(tag.tagName.toLowerCase())) {
      tag.outerHTML = tag.innerHTML
    }
  })
  
  // Remove disallowed attributes
  allTags.forEach(tag => {
    const attributes = Array.from(tag.attributes)
    attributes.forEach(attr => {
      if (!allowedAttributes.includes(attr.name.toLowerCase())) {
        tag.removeAttribute(attr.name)
      }
    })
  })
  
  return tempDiv.innerHTML
})

function stripHtml(html) {
  if (!html) return ''
  const div = document.createElement('div')
  div.innerHTML = html
  return div.textContent || div.innerText || ''
}

function handleClick() {
  if (props.clickable) {
    emit('click', props.content)
  }
}
</script>

<style scoped>
.html-content {
  width: 100%;
}

.content-preview {
  line-height: 1.5;
}

.content-preview.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.content-preview.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.content-preview.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.content-preview.line-clamp-4 {
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.content-preview.line-clamp-5 {
  display: -webkit-box;
  -webkit-line-clamp: 5;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.content-preview.cursor-pointer {
  cursor: pointer;
}

.content-preview.cursor-pointer:hover {
  opacity: 0.8;
}

/* Styling for HTML content */
.content-preview :deep(p) {
  margin: 0.25rem 0;
}

.content-preview :deep(ul),
.content-preview :deep(ol) {
  margin: 0.25rem 0;
  padding-left: 1rem;
}

.content-preview :deep(li) {
  margin: 0.125rem 0;
}

.content-preview :deep(h1),
.content-preview :deep(h2),
.content-preview :deep(h3),
.content-preview :deep(h4),
.content-preview :deep(h5),
.content-preview :deep(h6) {
  margin: 0.25rem 0;
  font-weight: 600;
}

.content-preview :deep(blockquote) {
  margin: 0.25rem 0;
  padding-left: 0.5rem;
  border-left: 2px solid #3b82f6;
  font-style: italic;
  color: #6b7280;
}

.content-preview :deep(strong),
.content-preview :deep(b) {
  font-weight: 600;
}

.content-preview :deep(em),
.content-preview :deep(i) {
  font-style: italic;
}
</style> 