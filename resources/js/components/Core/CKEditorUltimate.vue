<template>
  <div class="ckeditor-ultimate-wrapper" :class="{ 'fullscreen-mode': isFullscreen }">
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <!-- Decoupled Toolbar -->
    <div 
      ref="toolbarElement" 
      class="ckeditor-toolbar border border-gray-300 rounded-t-lg bg-white shadow-sm"
    ></div>
    
    <!-- Decoupled Editor -->
    <div 
      ref="editorElement" 
      class="ckeditor-container border border-gray-300 border-t-0 rounded-b-lg focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500"
    ></div>
    
    <!-- Error & Help -->
    <div v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</div>
    <div v-if="help" class="text-gray-500 text-sm mt-1">{{ help }}</div>
    
    <!-- Fullscreen Button -->
    <div class="flex justify-end text-xs text-gray-500 mt-1">
      <button
        @click="toggleFullscreen"
        type="button"
        class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
        :title="isFullscreen ? 'Thu nhỏ' : 'Phóng to'"
      >
        <svg v-if="!isFullscreen" class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
        </svg>
        <svg v-else class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        {{ isFullscreen ? 'Thu nhỏ' : 'Phóng to' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import api from '@/api/apiClient'

// Dynamic import để tránh conflict
let DecoupledEditor = null

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  help: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Nhập nội dung...'
  },
  height: {
    type: String,
    default: '500px'
  },
  uploadUrl: {
    type: String,
    default: '/api/upload-image'
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const editorElement = ref(null)
const toolbarElement = ref(null)
let editor = null
const isFullscreen = ref(false)

// Custom upload adapter for images
class UltimateUploadAdapter {
  constructor(loader) {
    this.loader = loader
  }

  async upload() {
    try {
      const file = await this.loader.file
      const formData = new FormData()
      formData.append('image', file)

      const response = await api.post(props.uploadUrl, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })

      const responseData = response.data.data || response.data
      const url = responseData.url || responseData.path || responseData.image

      if (!url) {
        throw new Error('No URL returned from upload')
      }

      return { default: url }
    } catch (error) {
      
      throw error
    }
  }

  abort() {
    // Abort upload if needed
  }
}

function uploadPlugin(editor) {
  editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
    return new UltimateUploadAdapter(loader)
  }
}

// Toggle fullscreen mode
function toggleFullscreen() {
  isFullscreen.value = !isFullscreen.value
  
  if (isFullscreen.value) {
    // Enter fullscreen
    document.body.style.overflow = 'hidden'
    if (editorElement.value) {
      editorElement.value.style.height = 'calc(100vh - 200px)'
    }
  } else {
    // Exit fullscreen
    document.body.style.overflow = ''
    if (editorElement.value) {
      editorElement.value.style.height = props.height
    }
  }
}

// Handle escape key to exit fullscreen
function handleKeydown(event) {
  if (event.key === 'Escape' && isFullscreen.value) {
    toggleFullscreen()
  }
}

onMounted(async () => {
  await nextTick()
  
  // Add keyboard listener
  document.addEventListener('keydown', handleKeydown)
  
  try {
    // Dynamic import CKEditor
    if (!DecoupledEditor) {
      const module = await import('@ckeditor/ckeditor5-build-decoupled-document')
      DecoupledEditor = module.default
    }
    
    // Create Decoupled Editor with ultimate configuration
    editor = await DecoupledEditor.create(editorElement.value, {
      placeholder: props.placeholder,
      language: 'vi',
      
      // Ultimate toolbar configuration
      toolbar: [
        'heading',
        '|',
        'fontSize',
        'fontFamily',
        'fontColor',
        'fontBackgroundColor',
        '|',
        'bold',
        'italic',
        'underline',
        'strikethrough',
        'subscript',
        'superscript',
        '|',
        'alignment',
        '|',
        'bulletedList',
        'numberedList',
        'todoList',
        '|',
        'indent',
        'outdent',
        '|',
        'link',
        'blockQuote',
        'insertTable',
        'mediaEmbed',
        '|',
        'imageUpload',
        'imageStyle:full',
        'imageStyle:side',
        'imageStyle:alignLeft',
        'imageStyle:alignCenter',
        'imageStyle:alignRight',
        '|',
        'horizontalLine',
        'pageBreak',
        'specialCharacters',
        '|',
        'code',
        'codeBlock',
        '|',
        'findAndReplace',
        '|',
        'undo',
        'redo'
      ],
      
      // Ultimate heading configuration
      heading: {
        options: [
          { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
          { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
          { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
          { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
          { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
          { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
          { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
        ]
      },
      
      // Ultimate font configuration
      fontSize: {
        options: [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 34, 36, 38, 40, 42, 44, 46, 48, 50, 52, 54, 56, 58, 60, 64, 68, 72, 76, 80, 84, 88, 92, 96]
      },
      
      fontFamily: {
        options: [
          'default',
          'Arial, Helvetica, sans-serif',
          'Courier New, Courier, monospace',
          'Georgia, serif',
          'Lucida Sans Unicode, Lucida Grande, sans-serif',
          'Tahoma, Geneva, sans-serif',
          'Times New Roman, Times, serif',
          'Trebuchet MS, Helvetica, sans-serif',
          'Verdana, Geneva, sans-serif',
          'Comic Sans MS, cursive, sans-serif',
          'Impact, Charcoal, sans-serif',
          'Lucida Console, Monaco, monospace',
          'Palatino Linotype, Book Antiqua, Palatino, serif'
        ]
      },
      
      // Ultimate image configuration
      image: {
        upload: {
          types: ['jpeg', 'png', 'gif', 'webp', 'svg']
        },
        styles: [
          'full',
          'side',
          'alignLeft',
          'alignCenter',
          'alignRight'
        ],
        resizeOptions: [
          {
            name: 'imageResize:original',
            value: null,
            label: 'Original'
          },
          {
            name: 'imageResize:25',
            value: '25',
            label: '25%'
          },
          {
            name: 'imageResize:50',
            value: '50',
            label: '50%'
          },
          {
            name: 'imageResize:75',
            value: '75',
            label: '75%'
          }
        ],
        resizeUnit: '%',
        toolbar: [
          'imageStyle:full',
          'imageStyle:side',
          'imageStyle:alignLeft',
          'imageStyle:alignCenter',
          'imageStyle:alignRight',
          '|',
          'imageResize:25',
          'imageResize:50',
          'imageResize:75',
          'imageResize:original',
          '|',
          'linkImage',
          '|',
          'toggleImageCaption'
        ]
      },
      
      // Ultimate table configuration
      table: {
        contentToolbar: [
          'tableColumn',
          'tableRow',
          'mergeTableCells',
          'tableProperties',
          'tableCellProperties',
          'toggleTableCaption'
        ],
        defaultProperties: {
          borderWidth: '1px',
          borderColor: '#ccc',
          backgroundColor: '#fff'
        }
      },
      
      // Ultimate link configuration
      link: {
        addTargetToExternalLinks: true,
        defaultProtocol: 'https://',
        decorators: {
          addTargetToExternalLinks: {
            mode: 'manual',
            label: 'Open in a new tab',
            attributes: {
              target: '_blank',
              rel: 'noopener noreferrer'
            }
          },
          toggleDownloadLink: {
            mode: 'manual',
            label: 'Downloadable link',
            attributes: {
              download: 'download'
            }
          }
        }
      },
      
      // Media embed configuration
      mediaEmbed: {
        previewsInData: true,
        providers: [
          {
            name: 'youtube',
            url: [
              /^(?:m\.)?youtube\.com\/watch\?v=([\w-]+)/,
              /^(?:m\.)?youtube\.com\/v\/([\w-]+)/,
              /^youtube\.com\/embed\/([\w-]+)/,
              /^youtu\.be\/([\w-]+)/
            ],
            html: match => {
              const id = match[1]
              return (
                '<div class="video-embed">' +
                  '<iframe src="https://www.youtube.com/embed/' + id + '" ' +
                  'frameborder="0" allowfullscreen="true" ' +
                  'width="560" height="315">' +
                  '</iframe>' +
                '</div>'
              )
            }
          },
          {
            name: 'vimeo',
            url: [
              /^vimeo\.com\/(\d+)/,
              /^vimeo\.com\/video\/(\d+)/,
              /^vimeo\.com\/groups\/[\w-]+\/videos\/(\d+)/,
              /^vimeo\.com\/channels\/[\w-]+\/(\d+)/
            ],
            html: match => {
              const id = match[1]
              return (
                '<div class="video-embed">' +
                  '<iframe src="https://player.vimeo.com/video/' + id + '" ' +
                  'frameborder="0" allowfullscreen="true" ' +
                  'width="560" height="315">' +
                  '</iframe>' +
                '</div>'
              )
            }
          }
        ]
      },
      
      // Find and replace configuration
      findAndReplace: {
        ui: {
          showResultsCounter: true,
          showReplaceAllButton: true
        }
      },
      
      // Extra plugins
      extraPlugins: [uploadPlugin]
    })

    // Set initial value
    if (props.modelValue) {
      editor.setData(props.modelValue)
    }

    // Move toolbar to separate container
    if (toolbarElement.value && editor.ui.view.toolbar) {
      toolbarElement.value.appendChild(editor.ui.view.toolbar.element)
    }

    // Listen for changes - simplified without counting
    editor.model.document.on('change:data', () => {
      const data = editor.getData()
      emit('update:modelValue', data)
      emit('change', data)
    })

    // Set custom height
    if (props.height) {
      editorElement.value.style.height = props.height
    }

  } catch (error) {
    
  }
})

onBeforeUnmount(() => {
  // Remove keyboard listener
  document.removeEventListener('keydown', handleKeydown)
  
  // Exit fullscreen if active
  if (isFullscreen.value) {
    document.body.style.overflow = ''
  }
  
  if (editor) {
    editor.destroy()
  }
})

// Watch for external value changes
watch(() => props.modelValue, (newValue) => {
  if (editor && newValue !== editor.getData()) {
    editor.setData(newValue || '')
  }
}, { immediate: false })

// Watch for error changes to update styling
watch(() => props.error, (newError) => {
  if (editorElement.value) {
    if (newError) {
      editorElement.value.classList.add('border-red-500', 'focus-within:ring-red-500', 'focus-within:border-red-500')
      editorElement.value.classList.remove('focus-within:ring-blue-500', 'focus-within:border-blue-500')
    } else {
      editorElement.value.classList.remove('border-red-500', 'focus-within:ring-red-500', 'focus-within:border-red-500')
      editorElement.value.classList.add('focus-within:ring-blue-500', 'focus-within:border-blue-500')
    }
  }
})
</script>

<style scoped>
.ckeditor-ultimate-wrapper {
  width: 100%;
}

.ckeditor-ultimate-wrapper.fullscreen-mode {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9999;
  background: white;
  padding: 1rem;
  overflow: hidden;
}

.ckeditor-ultimate-wrapper.fullscreen-mode .ckeditor-toolbar {
  position: sticky;
  top: 0;
  z-index: 10000;
  background: white;
  border-radius: 0.5rem 0.5rem 0 0;
}

.ckeditor-ultimate-wrapper.fullscreen-mode .ckeditor-container {
  border-radius: 0 0 0.5rem 0.5rem;
  height: calc(100vh - 200px) !important;
}

.ckeditor-toolbar {
  min-height: 50px;
  padding: 0.75rem;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.ckeditor-container {
  min-height: 400px;
}

.ckeditor-container :deep(.ck-editor__editable) {
  min-height: 400px;
  max-height: 800px;
  overflow-y: auto;
  padding: 1.5rem !important;
  font-size: 16px;
  line-height: 1.7;
}

.ckeditor-container :deep(.ck-editor__editable_inline) {
  border: none !important;
}

.ckeditor-container :deep(.ck-toolbar) {
  border: none !important;
  background: transparent !important;
}

.ckeditor-container :deep(.ck-content) {
  font-size: 16px;
  line-height: 1.7;
}

/* Ultimate content styling */
.ckeditor-container :deep(.ck-content h1) {
  font-size: 2.25rem;
  font-weight: 800;
  margin: 2rem 0 1.5rem 0;
  color: #1f2937;
  border-bottom: 3px solid #3b82f6;
  padding-bottom: 0.5rem;
}

.ckeditor-container :deep(.ck-content h2) {
  font-size: 1.875rem;
  font-weight: 700;
  margin: 1.75rem 0 1.25rem 0;
  color: #374151;
  border-bottom: 2px solid #e5e7eb;
  padding-bottom: 0.25rem;
}

.ckeditor-container :deep(.ck-content h3) {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 1.5rem 0 1rem 0;
  color: #4b5563;
}

.ckeditor-container :deep(.ck-content h4) {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 1.25rem 0 0.75rem 0;
  color: #6b7280;
}

.ckeditor-container :deep(.ck-content h5) {
  font-size: 1.125rem;
  font-weight: 600;
  margin: 1rem 0 0.5rem 0;
  color: #6b7280;
}

.ckeditor-container :deep(.ck-content h6) {
  font-size: 1rem;
  font-weight: 600;
  margin: 0.75rem 0 0.5rem 0;
  color: #6b7280;
}

.ckeditor-container :deep(.ck-content p) {
  margin: 1rem 0;
  line-height: 1.8;
  text-align: justify;
}

.ckeditor-container :deep(.ck-content ul),
.ckeditor-container :deep(.ck-content ol) {
  margin: 1rem 0;
  padding-left: 2rem;
}

.ckeditor-container :deep(.ck-content li) {
  margin: 0.5rem 0;
  line-height: 1.7;
}

.ckeditor-container :deep(.ck-content table) {
  border-collapse: collapse;
  width: 100%;
  margin: 1.5rem 0;
  border: 2px solid #d1d5db;
  border-radius: 0.5rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.ckeditor-container :deep(.ck-content table td),
.ckeditor-container :deep(.ck-content table th) {
  border: 1px solid #d1d5db;
  padding: 1rem;
  text-align: left;
}

.ckeditor-container :deep(.ck-content table th) {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  font-weight: 600;
}

.ckeditor-container :deep(.ck-content table tr:nth-child(even)) {
  background-color: #f9fafb;
}

.ckeditor-container :deep(.ck-content table tr:hover) {
  background-color: #f3f4f6;
}

.ckeditor-container :deep(.ck-content blockquote) {
  border-left: 5px solid #3b82f6;
  margin: 1.5rem 0;
  padding: 1rem 1.5rem;
  font-style: italic;
  color: #6b7280;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-radius: 0.5rem;
  box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
}

.ckeditor-container :deep(.ck-content code) {
  background-color: #f3f4f6;
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-family: 'Courier New', monospace;
  font-size: 0.875em;
  color: #dc2626;
  border: 1px solid #e5e7eb;
}

.ckeditor-container :deep(.ck-content pre) {
  background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
  color: #f9fafb;
  padding: 1.5rem;
  border-radius: 0.75rem;
  overflow-x: auto;
  margin: 1.5rem 0;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  border: 1px solid #374151;
}

.ckeditor-container :deep(.ck-content pre code) {
  background-color: transparent;
  padding: 0;
  color: inherit;
  border: none;
}

.ckeditor-container :deep(.ck-content a) {
  color: #3b82f6;
  text-decoration: underline;
  transition: color 0.2s ease;
}

.ckeditor-container :deep(.ck-content a:hover) {
  color: #2563eb;
  text-decoration: none;
}

/* Image styling */
.ckeditor-container :deep(.ck-content img) {
  max-width: 100%;
  height: auto;
  border-radius: 0.75rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease;
}

.ckeditor-container :deep(.ck-content img:hover) {
  transform: scale(1.02);
}

.ckeditor-container :deep(.ck-content img.image-style-side) {
  float: right;
  margin-left: 1.5rem;
  max-width: 50%;
}

.ckeditor-container :deep(.ck-content img.image-style-align-left) {
  float: left;
  margin-right: 1.5rem;
}

.ckeditor-container :deep(.ck-content img.image-style-align-center) {
  display: block;
  margin: 1.5rem auto;
}

.ckeditor-container :deep(.ck-content img.image-style-align-right) {
  float: right;
  margin-left: 1.5rem;
}

/* Video embed styling */
.ckeditor-container :deep(.ck-content .video-embed) {
  margin: 1.5rem 0;
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.ckeditor-container :deep(.ck-content .video-embed iframe) {
  width: 100%;
  height: 400px;
  border: none;
}

/* Todo list styling */
.ckeditor-container :deep(.ck-content .todo-list) {
  list-style: none;
  padding-left: 0;
}

.ckeditor-container :deep(.ck-content .todo-list li) {
  display: flex;
  align-items: center;
  margin: 0.5rem 0;
  padding: 0.5rem;
  background-color: #f9fafb;
  border-radius: 0.375rem;
}

.ckeditor-container :deep(.ck-content .todo-list input[type="checkbox"]) {
  margin-right: 0.75rem;
}

/* Responsive design */
@media (max-width: 768px) {
  .ckeditor-container :deep(.ck-editor__editable) {
    font-size: 16px;
    padding: 1rem !important;
  }
  
  .ckeditor-container :deep(.ck-content h1) {
    font-size: 1.75rem;
  }
  
  .ckeditor-container :deep(.ck-content h2) {
    font-size: 1.5rem;
  }
  
  .ckeditor-container :deep(.ck-content h3) {
    font-size: 1.25rem;
  }
  
  .ckeditor-container :deep(.ck-content img.image-style-side) {
    float: none;
    margin: 1rem 0;
    max-width: 100%;
  }
  
  .ckeditor-container :deep(.ck-content .video-embed iframe) {
    height: 250px;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .ckeditor-container :deep(.ck-content) {
    color: #e5e7eb;
  }
  
  .ckeditor-container :deep(.ck-content h1),
  .ckeditor-container :deep(.ck-content h2),
  .ckeditor-container :deep(.ck-content h3),
  .ckeditor-container :deep(.ck-content h4),
  .ckeditor-container :deep(.ck-content h5),
  .ckeditor-container :deep(.ck-content h6) {
    color: #f9fafb;
  }
  
  .ckeditor-container :deep(.ck-content blockquote) {
    background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
    color: #d1d5db;
  }
}
</style> 
