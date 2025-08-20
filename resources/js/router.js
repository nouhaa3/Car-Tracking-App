

/*const routes = [
  { path: '/home', component: () => import('./components/home.vue')},  
  { path: '/login', component: () => import('./components/login.vue') },
  { path: '/register', component: () => import('./components/register.vue') },
];
*/

import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../components/home.vue';
import LoginPage from '../components/login.vue';
import RegisterPage from '../components/register.vue';

const routes = [
  { path: '/', name: 'Home', component: HomePage },
  { path: '/login', name: 'Login', component: LoginPage },
  { path: '/register', name: 'Register', component: RegisterPage },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
