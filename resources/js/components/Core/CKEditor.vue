<template>
  <div class="ckeditor-wrapper">
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div 
      ref="editorElement" 
      class="ckeditor-container border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500"
    ></div>
    <div v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</div>
    <div v-if="help" class="text-gray-500 text-sm mt-1">{{ help }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

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
    default: '300px'
  },
  toolbar: {
    type: Array,
    default: () => [
      'heading',
      '|',
      'bold',
      'italic',
      'underline',
      'strikethrough',
      '|',
      'bulletedList',
      'numberedList',
      '|',
      'link',
      'blockQuote',
      'insertTable',
      '|',
      'undo',
      'redo'
    ]
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const editorElement = ref(null)
let editor = null

onMounted(async () => {
  await nextTick()
  
  try {
    editor = await ClassicEditor.create(editorElement.value, {
      placeholder: props.placeholder,
      toolbar: props.toolbar,
      language: 'vi',
      removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload'],
      heading: {
        options: [
          { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
          { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
          { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
          { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
        ]
      }
    })

    // Set initial value
    if (props.modelValue) {
      editor.setData(props.modelValue)
    }

    // Listen for changes
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
    console.error('Error initializing CKEditor:', error)
  }
})

onBeforeUnmount(() => {
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
.ckeditor-wrapper {
  width: 100%;
}

.ckeditor-container {
  min-height: 200px;
}

.ckeditor-container :deep(.ck-editor__editable) {
  min-height: 200px;
  max-height: 500px;
  overflow-y: auto;
}

.ckeditor-container :deep(.ck-editor__editable_inline) {
  border: none !important;
  padding: 1rem !important;
}

.ckeditor-container :deep(.ck-toolbar) {
  border: none !important;
  border-bottom: 1px solid #e5e7eb !important;
  border-radius: 0.5rem 0.5rem 0 0 !important;
}

.ckeditor-container :deep(.ck-content) {
  font-size: 14px;
  line-height: 1.6;
}

.ckeditor-container :deep(.ck-content h1) {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 1rem 0 0.5rem 0;
}

.ckeditor-container :deep(.ck-content h2) {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0.75rem 0 0.5rem 0;
}

.ckeditor-container :deep(.ck-content h3) {
  font-size: 1.125rem;
  font-weight: 600;
  margin: 0.5rem 0 0.25rem 0;
}

.ckeditor-container :deep(.ck-content p) {
  margin: 0.5rem 0;
}

.ckeditor-container :deep(.ck-content ul),
.ckeditor-container :deep(.ck-content ol) {
  margin: 0.5rem 0;
  padding-left: 1.5rem;
}

.ckeditor-container :deep(.ck-content table) {
  border-collapse: collapse;
  width: 100%;
  margin: 0.5rem 0;
}

.ckeditor-container :deep(.ck-content table td),
.ckeditor-container :deep(.ck-content table th) {
  border: 1px solid #d1d5db;
  padding: 0.5rem;
}

.ckeditor-container :deep(.ck-content blockquote) {
  border-left: 4px solid #3b82f6;
  margin: 0.5rem 0;
  padding-left: 1rem;
  font-style: italic;
  color: #6b7280;
}
</style> 