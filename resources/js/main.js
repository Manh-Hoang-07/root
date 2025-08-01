import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './bootstrap'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Initialize enum store
// import { useEnumStore } from '@/stores/enumStore'
// const enumStore = useEnumStore()
// enumStore.initialize()

app.mount('#app') 