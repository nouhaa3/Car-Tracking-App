import { createRouter, createWebHistory } from 'vue-router';
import HomePage from './components/home.vue';
import LoginPage from './components/login.vue';
import RegisterPage from './components/register.vue';
import ForgotPassword from './components/ForgotPassword.vue';
import ResetPassword from './components/ResetPassword.vue';
import AjouterVoiture from './components/voitures/ajouter.vue';
import DashboardAdmin from './components/admindashboard.vue';
import DashboardTechnicien from './components/techniciendashboard.vue';
import DashboardAgent from './components/agentdashboard.vue';
import CatalogueVoiture from './components/voitures/cataloguevoitures.vue';
import ProfilePage from './components/profile.vue';
import DetailsVoiture from './components/voitures/detailsvoiture.vue';
import Chats from './components/chats.vue';
import Users from './components/users.vue';
import Rapports from './components/rapports.vue';
import HelpPage from './components/help.vue';
import Corbeille from './components/Corbeille.vue';
import Settings from './components/Settings.vue';

// Interventions
import CatalogueInterventions from './components/interventions/catalogue.vue';
import AjouterIntervention from './components/interventions/ajouter.vue';
import DetailsIntervention from './components/interventions/details.vue';

// Alertes
import CatalogueAlertes from './components/alertes/catalogue.vue';
import DetailsAlerte from './components/alertes/details.vue';


const routes = [
  { path: '/', name: 'HomePage', component: HomePage },
  { path: '/login', name: 'Login', component: LoginPage },
  { path: '/register', name: 'Register', component: RegisterPage },
  { path: '/forgot-password', name: 'ForgotPassword', component: ForgotPassword },
  { path: '/reset-password', name: 'ResetPassword', component: ResetPassword },
  { path: '/voitures/ajouter', name: 'AjouterVoiture', component: AjouterVoiture },
  { path: '/voitures/cataloguevoitures', name: 'CatalogueVoiture', component: CatalogueVoiture },
  { path: '/admindashboard', name: 'DashboardAdmin', component: DashboardAdmin},
  { path: '/agentdashboard', name: 'DashboardAgent', component: DashboardAgent},
  { path: '/techniciendashboard', name: 'DashboardTechnicien', component: DashboardTechnicien},
  
  // Interventions routes
  { path: '/interventions/catalogue', name: 'CatalogueInterventions', component: CatalogueInterventions },
  { path: '/interventions/ajouter', name: 'AjouterIntervention', component: AjouterIntervention },
  { path: '/interventions/:idIntervention', name: 'DetailsIntervention', component: DetailsIntervention, props: true },
  
  // Alertes routes
  { path: '/alertes', name: 'Alertes', component: CatalogueAlertes },
  { path: '/alertes/:id', name: 'DetailsAlerte', component: DetailsAlerte, props: true },
  
  { path: '/voitures/:idVoiture', name: 'DetailsVoiture', component: DetailsVoiture, props: true },
  { path: '/profile', name: 'ProfilePage', component: ProfilePage},
  { path: '/chats', name: 'Chats', component: Chats},
  { path: '/users', name: 'Users', component: Users },
  { path: '/rapports', name: 'Rapports', component: Rapports },
  { path: '/help', name: 'Help', component: HelpPage },
  { path: '/corbeille', name: 'Corbeille', component: Corbeille },
  { path: '/settings', name: 'Settings', component: Settings },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;