import './bootstrap';
import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';

import { createApp } from 'vue/dist/vue.esm-bundler.js';
import { createRouter, createWebHistory } from 'vue-router';
import Routes from './routes.js';
import PrimeVue from 'primevue/config';
import bsCustomFileInput from 'bs-custom-file-input'
import ToastService from 'primevue/toastservice';
import Chatbot from './components/Chatbot.vue';

const app = createApp({});
const router = createRouter({
    routes: Routes,
    history: createWebHistory(),
});
router.afterEach((to) => {
    // Sử dụng tiêu đề từ meta nếu nó tồn tại, nếu không thì sử dụng một tiêu đề mặc định
    document.title = to.meta.title || 'Default Title';
});
bsCustomFileInput.init();
app.use(router);
app.use(PrimeVue);
app.use(ToastService);
// Đăng ký component Chatbot
app.component('Chatbot', Chatbot);

app.mount('#app');
