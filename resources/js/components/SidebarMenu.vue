<template>
  <aside :class="[
    'fixed inset-y-0 left-0 z-50 w-64 h-screen overflow-y-auto bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl transform transition-transform duration-300 ease-in-out',
    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
  ]">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-slate-700">
      <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
        </div>
        <span class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
          AdminHub
        </span>
      </div>
      <button 
        @click="$emit('close')"
        class="lg:hidden p-1 rounded-md text-slate-400 hover:text-white hover:bg-slate-700"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2">
      <slot name="menu-header"></slot>
      <router-link 
        v-for="item in menuItems" 
        :key="item.path"
        :to="item.path" 
        @click="$emit('select', item)"
        :class="[
          'group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200',
          'hover:bg-gradient-to-r hover:from-blue-500/20 hover:to-purple-500/20 hover:text-white',
          'hover:shadow-lg hover:scale-105',
          activePath === item.path 
            ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg scale-105' 
            : 'text-slate-300'
        ]"
      >
        <component :is="item.icon" class="w-5 h-5 mr-3 transition-transform duration-200" />
        {{ item.name }}
        <div v-if="activePath === item.path" class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
      </router-link>
    </nav>
    <div class="p-4 border-t border-slate-700">
      <slot name="user"></slot>
    </div>
  </aside>
</template>

<script setup>
defineProps({
  menuItems: { type: Array, required: true },
  activePath: { type: String, required: true },
  sidebarOpen: { type: Boolean, default: true }
})
</script>

<style scoped>
aside::-webkit-scrollbar {
  width: 4px;
}
aside::-webkit-scrollbar-track {
  background: transparent;
}
aside::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
}
aside::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}
</style> 