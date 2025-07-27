# Hướng dẫn sử dụng CKEditor 5

## Tổng quan

CKEditor 5 đã được tích hợp vào dự án Laravel + Vue.js với các tính năng:

- **Rich text editing** với nhiều công cụ định dạng
- **HTML output** an toàn
- **Responsive design** phù hợp với Tailwind CSS
- **Vue.js integration** hoàn chỉnh
- **Custom styling** phù hợp với design system

## Các component đã tạo

### 1. CKEditor Component
**File:** `resources/js/components/Core/CKEditor.vue`

**Tính năng:**
- Rich text editor với toolbar đầy đủ
- Support v-model cho Vue.js
- Error handling và validation
- Custom styling với Tailwind CSS
- Placeholder và help text

**Sử dụng:**
```vue
<template>
  <CKEditor
    v-model="content"
    label="Nội dung"
    placeholder="Nhập nội dung..."
    height="300px"
    :error="errors.content"
    @change="handleChange"
  />
</template>

<script setup>
import CKEditor from '@/components/Core/CKEditor.vue'

const content = ref('')
</script>
```

### 2. HtmlContent Component
**File:** `resources/js/components/Core/HtmlContent.vue`

**Tính năng:**
- Hiển thị HTML content an toàn
- Line clamping (giới hạn số dòng)
- HTML sanitization
- Clickable option
- Responsive design

**Sử dụng:**
```vue
<template>
  <HtmlContent 
    :content="description" 
    :max-lines="3"
    placeholder="Không có nội dung"
    :clickable="true"
    @click="handleClick"
  />
</template>

<script setup>
import HtmlContent from '@/components/Core/HtmlContent.vue'
</script>
```

## Tích hợp vào Categories

### 1. Form tạo/sửa danh mục
- Trường "Mô tả" sử dụng CKEditor thay vì textarea
- Validation tăng từ 255 lên 2000 ký tự
- HTML content được lưu vào database

### 2. Bảng danh sách
- Hiển thị mô tả với HtmlContent component
- Line clamping 3 dòng
- Sanitized HTML output

### 3. Database
- Trường `description` trong bảng `categories` đã hỗ trợ TEXT
- Migration không cần thay đổi

## Các tính năng CKEditor

### Toolbar mặc định:
- **Heading** (H1, H2, H3)
- **Text formatting** (Bold, Italic, Underline, Strikethrough)
- **Lists** (Bulleted, Numbered)
- **Links**
- **Blockquote**
- **Tables**
- **Undo/Redo**

### Customization:

#### Thay đổi toolbar:
```vue
<CKEditor
  v-model="content"
  :toolbar="[
    'heading',
    '|',
    'bold',
    'italic',
    '|',
    'bulletedList',
    'numberedList',
    '|',
    'link'
  ]"
/>
```

#### Thay đổi height:
```vue
<CKEditor
  v-model="content"
  height="400px"
/>
```

#### Thay đổi placeholder:
```vue
<CKEditor
  v-model="content"
  placeholder="Nhập mô tả chi tiết..."
/>
```

## Demo Page

Truy cập `/admin/categories/demo` để test CKEditor với:
- Editor với nội dung mẫu
- HTML output preview
- Live preview

## Bảo mật

### HTML Sanitization:
- Chỉ cho phép các tag an toàn
- Loại bỏ script tags và event handlers
- Sanitize attributes

### Allowed tags:
- `p`, `br`, `strong`, `b`, `em`, `i`, `u`
- `ul`, `ol`, `li`
- `h1`, `h2`, `h3`, `h4`, `h5`, `h6`
- `blockquote`, `code`, `pre`

### Allowed attributes:
- `class`, `style`

## Styling

### CKEditor styling:
- Border và focus states phù hợp với Tailwind
- Custom toolbar styling
- Responsive design
- Consistent với design system

### HtmlContent styling:
- Line clamping với CSS
- Hover effects
- Typography phù hợp
- Color scheme consistent

## Troubleshooting

### Lỗi thường gặp:

1. **CKEditor không load:**
   - Kiểm tra package đã cài: `@ckeditor/ckeditor5-build-classic`
   - Kiểm tra import path

2. **HTML không hiển thị:**
   - Sử dụng `v-html` directive
   - Kiểm tra HtmlContent component

3. **Validation errors:**
   - Tăng max length trong validation rules
   - Kiểm tra HTML content length

### Debug:
```javascript
// Log content changes
function handleChange(newContent) {
  console.log('Content changed:', newContent)
  console.log('Content length:', newContent.length)
}
```

## Mở rộng

### Thêm plugins:
1. Cài đặt plugin package
2. Import vào CKEditor component
3. Thêm vào toolbar configuration

### Custom toolbar:
```javascript
const toolbar = [
  'heading',
  '|',
  'bold',
  'italic',
  'underline',
  'strikethrough',
  '|',
  'fontSize',
  'fontColor',
  'fontBackgroundColor',
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
```

## Performance

### Optimization:
- Lazy loading cho CKEditor
- Debounce cho content changes
- Efficient HTML sanitization
- Minimal re-renders

### Bundle size:
- CKEditor Classic build: ~200KB gzipped
- Vue integration: ~50KB
- Total impact: ~250KB

## Kết luận

CKEditor 5 đã được tích hợp thành công vào dự án với:
- ✅ Rich text editing đầy đủ
- ✅ Vue.js integration hoàn chỉnh
- ✅ Security và sanitization
- ✅ Responsive design
- ✅ Performance optimization
- ✅ Easy customization

Sẵn sàng sử dụng cho các trường mô tả trong toàn bộ ứng dụng! 