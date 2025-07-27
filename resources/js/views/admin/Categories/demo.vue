<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Demo CKEditor 5</h1>
    
    <div class="bg-white shadow-md rounded-lg p-6">
      <h2 class="text-lg font-semibold mb-4">Editor với các tính năng cơ bản</h2>
      
      <CKEditor
        v-model="content"
        label="Nội dung"
        placeholder="Nhập nội dung..."
        height="300px"
        @change="handleChange"
      />
      
      <div class="mt-4">
        <h3 class="text-md font-medium mb-2">HTML Output:</h3>
        <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto">{{ content }}</pre>
      </div>
      
      <div class="mt-4">
        <h3 class="text-md font-medium mb-2">Preview:</h3>
        <div class="bg-gray-50 p-4 rounded border" v-html="content"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import CKEditor from '@/components/Core/CKEditor.vue'

const content = ref(`
<h2>Chào mừng đến với CKEditor 5!</h2>
<p>Đây là một <strong>rich text editor</strong> mạnh mẽ với các tính năng:</p>
<ul>
  <li><strong>Định dạng văn bản:</strong> Bold, Italic, Underline</li>
  <li><strong>Danh sách:</strong> Bullet và Numbered lists</li>
  <li><strong>Liên kết:</strong> Tạo và chỉnh sửa links</li>
  <li><strong>Bảng:</strong> Tạo và quản lý tables</li>
  <li><strong>Blockquote:</strong> Trích dẫn văn bản</li>
</ul>
<p>Bạn có thể <em>chỉnh sửa</em> nội dung này để test các tính năng!</p>
`)

function handleChange(newContent) {
  console.log('Content changed:', newContent)
}
</script> 