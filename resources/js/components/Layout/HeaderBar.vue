<template>
  <header class="bg-white/80 backdrop-blur-sm border-b border-slate-200 h-12 flex items-center fixed top-0 left-0 right-0 z-40" style="height: 48px;">
    <div class="flex items-center justify-between h-12 px-1 lg:px-2 w-full">
      <div class="flex items-center space-x-3">
        <slot name="menu-btn">
          <!-- Hamburger button mặc định -->
          <button 
            @click="$emit('toggle-sidebar')"
            class="p-0.5 rounded text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            aria-label="Toggle menu"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </slot>
        <h1 class="text-sm font-semibold text-slate-900">{{ title }}</h1>
      </div>
      <div class="flex items-center space-x-1">
        <slot name="actions"></slot>
        <div class="flex items-center space-x-0.5">
          <span class="text-[11px] text-slate-700 font-medium">{{ userName }}</span>
          <button @click="$emit('logout')" class="p-0.5 text-red-400 hover:text-white hover:bg-red-500/20 rounded transition-all duration-200" style="min-width: 24px; min-height: 24px;">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
defineProps({
  title: { type: String, default: 'Admin Panel' },
  userName: { type: String, default: 'Admin User' }
})
</script>

<style scoped>
header {
  height: 48px;
}
</style> 
