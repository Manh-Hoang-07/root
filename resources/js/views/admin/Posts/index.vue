<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Quản lý bài đăng</h1>
      <button 
        @click="showAddModal = true"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors"
      >
        Thêm bài đăng
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input 
          v-model="filters.search"
          type="text" 
          placeholder="Tìm kiếm bài đăng..."
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <select 
          v-model="filters.status"
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tất cả trạng thái</option>
          <option value="published">Đã xuất bản</option>
          <option value="draft">Bản nháp</option>
          <option value="archived">Đã lưu trữ</option>
        </select>
        <select 
          v-model="filters.category"
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tất cả danh mục</option>
          <option value="news">Tin tức</option>
          <option value="tutorial">Hướng dẫn</option>
          <option value="review">Đánh giá</option>
        </select>
        <button 
          @click="loadPosts"
          class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
        >
          Lọc
        </button>
      </div>
    </div>

    <!-- Posts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bài đăng</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tác giả</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Danh mục</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="post in posts" :key="post.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <img 
                      :src="post.featured_image || '/placeholder.jpg'" 
                      :alt="post.title"
                      class="h-10 w-10 rounded-lg object-cover"
                    >
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ post.title }}</div>
                    <div class="text-sm text-gray-500">{{ post.excerpt }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ post.author?.name || 'Admin' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ post.category }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="{
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                    'bg-green-100 text-green-800': post.status === 'published',
                    'bg-yellow-100 text-yellow-800': post.status === 'draft',
                    'bg-gray-100 text-gray-800': post.status === 'archived'
                  }"
                >
                  {{ getStatusText(post.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(post.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <Actions 
                  :item="post"
                  @edit="editPost"
                  @delete="(item) => deletePost(item.id)"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 flex items-center justify-center">
      <div class="fixed inset-0 z-40 bg-white bg-opacity-10 backdrop-blur-md"></div>
      <div class="relative z-50 bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ showEditModal ? 'Sửa bài đăng' : 'Thêm bài đăng' }}
          </h3>
          <form @submit.prevent="savePost">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Tiêu đề</label>
                <input 
                  v-model="form.title"
                  type="text" 
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Tóm tắt</label>
                <textarea 
                  v-model="form.excerpt"
                  rows="3"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nội dung</label>
                <textarea 
                  v-model="form.content"
                  rows="6"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Danh mục</label>
                <select 
                  v-model="form.category"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="news">Tin tức</option>
                  <option value="tutorial">Hướng dẫn</option>
                  <option value="review">Đánh giá</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select 
                  v-model="form.status"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="draft">Bản nháp</option>
                  <option value="published">Xuất bản</option>
                  <option value="archived">Lưu trữ</option>
                </select>
              </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
              <button 
                type="button"
                @click="closeModal"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
              >
                Hủy
              </button>
              <button 
                type="submit"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
              >
                {{ showEditModal ? 'Cập nhật' : 'Thêm' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Actions from '@/components/Core/Actions.vue';

const posts = ref([]);
const showAddModal = ref(false);
const showEditModal = ref(false);
const editingPost = ref(null);

const filters = ref({
  search: '',
  status: '',
  category: ''
});

const form = ref({
  title: '',
  excerpt: '',
  content: '',
  category: 'news',
  status: 'draft'
});

const loadPosts = async () => {
  try {
    // Mock data for now - replace with actual API call
    posts.value = [
      {
        id: 1,
        title: 'Hướng dẫn sử dụng Laravel với Vue.js',
        excerpt: 'Bài viết hướng dẫn cách tích hợp Laravel backend với Vue.js frontend...',
        content: 'Nội dung chi tiết...',
        category: 'tutorial',
        status: 'published',
        author: { name: 'Admin' },
        created_at: '2024-01-15'
      },
      {
        id: 2,
        title: 'Tin tức công nghệ mới nhất',
        excerpt: 'Cập nhật những tin tức công nghệ mới nhất trong tuần...',
        content: 'Nội dung chi tiết...',
        category: 'news',
        status: 'published',
        author: { name: 'Admin' },
        created_at: '2024-01-14'
      }
    ];
  } catch (error) {
    console.error('Error loading posts:', error);
  }
};

const editPost = (post) => {
  editingPost.value = post;
  form.value = { ...post };
  showEditModal.value = true;
};

const savePost = async () => {
  try {
    if (showEditModal.value) {
      // Update existing post
      const index = posts.value.findIndex(p => p.id === editingPost.value.id);
      if (index > -1) {
        posts.value[index] = { ...posts.value[index], ...form.value };
      }
    } else {
      // Add new post
      const newPost = {
        id: Date.now(),
        ...form.value,
        author: { name: 'Admin' },
        created_at: new Date().toISOString().split('T')[0]
      };
      posts.value.unshift(newPost);
    }
    
    closeModal();
  } catch (error) {
    console.error('Error saving post:', error);
  }
};

const deletePost = async (id) => {
  if (confirm('Bạn có chắc chắn muốn xóa bài đăng này?')) {
    try {
      const index = posts.value.findIndex(p => p.id === id);
      if (index > -1) {
        posts.value.splice(index, 1);
      }
    } catch (error) {
      console.error('Error deleting post:', error);
    }
  }
};

const closeModal = () => {
  showAddModal.value = false;
  showEditModal.value = false;
  editingPost.value = null;
  form.value = {
    title: '',
    excerpt: '',
    content: '',
    category: 'news',
    status: 'draft'
  };
};

const getStatusText = (status) => {
  const statusMap = {
    published: 'Đã xuất bản',
    draft: 'Bản nháp',
    archived: 'Đã lưu trữ'
  };
  return statusMap[status] || status;
};

const formatDate = (dateString) => {
  return new Intl.DateTimeFormat('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(new Date(dateString));
};

onMounted(() => {
  loadPosts();
});
</script>

<style scoped>
/* Custom styles */
</style> 
