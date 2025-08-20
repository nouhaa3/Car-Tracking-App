import './bootstrap';

/*import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';*/

import { createApp } from 'vue';
import App from './app.vue';
import router from './router';

createApp(App)
    .use(router)
    .mount('#app');
