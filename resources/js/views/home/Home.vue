<template>
  <div class="space-y-8">
    <!-- Hero Section -->
    <div class="text-center py-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg text-white">
      <h1 class="text-4xl font-bold mb-4">Chào mừng đến với Laravel Vue App</h1>
      <p class="text-xl mb-6">Ứng dụng web hiện đại được xây dựng với Laravel và Vue.js</p>
      <button 
        @click="showAlert"
        class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors"
      >
        Bắt đầu ngay
      </button>
    </div>

    <!-- Features Grid -->
    <div class="grid md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-blue-500 text-3xl mb-4">🚀</div>
        <h3 class="text-xl font-semibold mb-2">Hiệu suất cao</h3>
        <p class="text-gray-600">Ứng dụng được tối ưu hóa với Vite và Vue 3 Composition API</p>
      </div>
      
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-green-500 text-3xl mb-4">🎨</div>
        <h3 class="text-xl font-semibold mb-2">Giao diện đẹp</h3>
        <p class="text-gray-600">Thiết kế hiện đại với Tailwind CSS và responsive design</p>
      </div>
      
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-purple-500 text-3xl mb-4">⚡</div>
        <h3 class="text-xl font-semibold mb-2">Phát triển nhanh</h3>
        <p class="text-gray-600">Hot reload và developer experience tuyệt vời</p>
      </div>
    </div>

    <!-- Counter Section -->
    <div class="bg-white p-8 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6 text-center">Ví dụ Counter</h2>
      <div class="text-center space-y-4">
        <div class="text-6xl font-bold text-blue-600">{{ counter }}</div>
        <div class="space-x-4">
          <button 
            @click="increment"
            class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors"
          >
            Tăng
          </button>
          <button 
            @click="decrement"
            class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition-colors"
          >
            Giảm
          </button>
          <button 
            @click="reset"
            class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors"
          >
            Reset
          </button>
        </div>
      </div>
    </div>

    <!-- Todo List Example -->
    <div class="bg-white p-8 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6">Danh sách công việc</h2>
      <div class="space-y-4">
        <div class="flex gap-2">
          <input 
            v-model="newTodo"
            @keyup.enter="addTodo"
            type="text" 
            placeholder="Thêm công việc mới..."
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
          <button 
            @click="addTodo"
            class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition-colors"
          >
            Thêm
          </button>
        </div>
        
        <ul class="space-y-2">
          <li 
            v-for="(todo, index) in todos" 
            :key="index"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
          >
            <span :class="{ 'line-through text-gray-500': todo.completed }">
              {{ todo.text }}
            </span>
            <div class="space-x-2">
              <button 
                @click="toggleTodo(index)"
                :class="todo.completed ? 'bg-yellow-500' : 'bg-green-500'"
                class="text-white px-3 py-1 rounded text-sm hover:opacity-80 transition-opacity"
              >
                {{ todo.completed ? 'Hoàn thành' : 'Chưa xong' }}
              </button>
              <button 
                @click="removeTodo(index)"
                class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 transition-colors"
              >
                Xóa
              </button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Counter state
const counter = ref(0);

// Todo list state
const todos = ref([
  { text: 'Học Vue.js', completed: false },
  { text: 'Tích hợp với Laravel', completed: false },
  { text: 'Tạo giao diện đẹp', completed: true }
]);
const newTodo = ref('');

// Counter methods
const increment = () => counter.value++;
const decrement = () => counter.value--;
const reset = () => counter.value = 0;

// Todo methods
const addTodo = () => {
  if (newTodo.value.trim()) {
    todos.value.push({ text: newTodo.value.trim(), completed: false });
    newTodo.value = '';
  }
};

const toggleTodo = (index) => {
  todos.value[index].completed = !todos.value[index].completed;
};

const removeTodo = (index) => {
  todos.value.splice(index, 1);
};

// Alert method
const showAlert = () => {
  alert('Chào mừng bạn đến với ứng dụng Vue!');
};
</script> 