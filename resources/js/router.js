import { createRouter, createWebHistory } from 'vue-router';
import HomePage from './components/home.vue';
import LoginPage from './components/login.vue';
import RegisterPage from './components/register.vue';
import AjouterVoiture from './components/voitures/ajouter.vue';
import DashboardAdmin from './components/admindashboard.vue';
import DashboardTechnicien from './components/techniciendashboard.vue';
import DashboardAgent from './components/agentdashboard.vue';
import CatalogueVoiture from './components/voitures/cataloguevoitures.vue';
//import Interventions from './components/interventions.vue';
//import Alertes from './components/alertes.vue';
import ProfilePage from './components/profile.vue';


const routes = [
  { path: '/', name: 'HomePage', component: HomePage },
  { path: '/login', name: 'Login', component: LoginPage },
  { path: '/register', name: 'Register', component: RegisterPage },
  { path: '/voitures/ajouter', name: 'AjouterVoiture', component: AjouterVoiture },
  { path: '/voitures/cataloguevoitures', name: 'CatalogueVoiture', component: CatalogueVoiture },
  { path: '/admindashboard', name: 'DashboardAdmin', component: DashboardAdmin},
  { path: '/agentdashboard', name: 'DashboardAgent', component: DashboardAgent},
  { path: '/techniciendashboard', name: 'DashboardTechnicien', component: DashboardTechnicien},
  //{ path: '/interventions', name: 'Interventions', component: Interventions },
  //{ path: '/alertes', name: 'Alertes', component: Alertes },
  {path: '/profile', name: 'ProfilePage', component: ProfilePage},
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;