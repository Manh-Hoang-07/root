import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';
import { Auth } from './utils/auth';
import { authDirectives } from './directives/auth';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

// Đăng ký auth directives
Object.keys(authDirectives).forEach(key => {
  app.directive(key, authDirectives[key]);
});

// Khởi tạo Auth trước khi mount app
Auth.init().then(() => {
app.mount('#app');
});
