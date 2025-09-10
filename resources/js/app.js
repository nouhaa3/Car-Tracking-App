import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './app.vue';
import router from './router';

const app = createApp(App);

const pinia = createPinia();   // ✅ create once
app.use(pinia);                // ✅ register once
app.use(router);

app.mount('#app');
