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
import PrivacyPolicy from './components/PrivacyPolicy.vue';
import TermsOfService from './components/TermsOfService.vue';
import Notifications from './components/Notifications.vue';
import AboutPage from './components/About.vue';
import FAQPage from './components/FAQ.vue';
import NotFoundPage from './components/NotFound.vue';

// Interventions
import CatalogueInterventions from './components/interventions/catalogue.vue';
import AjouterIntervention from './components/interventions/ajouter.vue';
import DetailsIntervention from './components/interventions/details.vue';

// Alertes
import CatalogueAlertes from './components/alertes/catalogue.vue';
import DetailsAlerte from './components/alertes/details.vue';


const routes = [
  { path: '/', name: 'HomePage', component: HomePage },
  { path: '/about', name: 'About', component: AboutPage },
  { path: '/faq', name: 'FAQ', component: FAQPage },
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
  { path: '/privacy-policy', name: 'PrivacyPolicy', component: PrivacyPolicy },
  { path: '/terms-of-service', name: 'TermsOfService', component: TermsOfService },
  { path: '/notifications', name: 'Notifications', component: Notifications },
  
  // 404 Catch-all route (must be last)
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFoundPage },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;