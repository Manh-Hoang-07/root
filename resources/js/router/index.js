import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/home/Home.vue';
import Users from '../views/admin/Users.vue';
import About from '../views/home/About.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/users',
    name: 'Users',
    component: Users
  },
  {
    path: '/about',
    name: 'About',
    component: About
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router; 