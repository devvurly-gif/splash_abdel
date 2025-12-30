import { createApp } from 'vue';

// Import your components
import App from './App.vue';

// Import router
import router from './router';

// Import Pinia
import { createPinia } from 'pinia';

// Import Toastification
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

// Create Vue app
const app = createApp(App);

// Create Pinia instance
const pinia = createPinia();

// Use plugins
app.use(pinia);
app.use(router);
app.use(Toast, {
    transition: 'Vue-Toastification__bounce',
    maxToasts: 20,
    newestOnTop: true,
    position: 'top-right',
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: 'button',
    icon: true,
    rtl: false
});

// Mount the app
app.mount('#app');

// Initialize auth store to restore session from localStorage
import { useAuthStore } from './stores/auth';
const authStore = useAuthStore();
authStore.initializeAuth();
