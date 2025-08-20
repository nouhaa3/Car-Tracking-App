import { createRouter, createWebHistory } from 'vue-router';
import HomePage from './components/home.vue';
import LoginPage from './components/login.vue';
import RegisterPage from './components/register.vue';
import AjouterVoiture from './components/voitures/ajouter.vue';

const routes = [
  { path: '/', name: 'Home', component: HomePage },
  { path: '/login', name: 'Login', component: LoginPage },
  { path: '/register', name: 'Register', component: RegisterPage },
  { path: '/voitures/ajouter', name: 'AjouterVoiture', component: AjouterVoiture },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;