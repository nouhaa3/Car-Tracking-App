import HomePage from './components/HomePage.vue';

const routes = [
  { path: '/', component: HomePage },  
  { path: '/login', component: () => import('./components/Login.vue') },
  { path: '/register', component: () => import('./components/Register.vue') },
  /*{ path: '/voitures', component: () => import('./components/ListVoitures.vue') },
  { path: '/create', component: () => import('./components/CreateVoiture.vue') },
  { path: '/edit/:id', component: () => import('./components/EditVoiture.vue') },*/
];