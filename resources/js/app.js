import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './app.vue';
import router from './router';
import i18n from './i18n';

const app = createApp(App);

const pinia = createPinia();   //  create once
app.use(pinia);                //  register once
app.use(router);
app.use(i18n);                 //  register i18n

app.mount('#app');
