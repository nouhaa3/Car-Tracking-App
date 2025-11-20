import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './app.vue';
import router from './router';
import i18n from './i18n';
import 'flag-icons/css/flag-icons.min.css';


const app = createApp(App);

// Suppress browser extension errors
window.addEventListener('unhandledrejection', (event) => {
  if (event.reason?.message?.includes('message channel closed')) {
    event.preventDefault();
  }
});

const pinia = createPinia();   //  create once
app.use(pinia);                //  register once
app.use(router);
app.use(i18n);                 //  register i18n

app.mount('#app');
