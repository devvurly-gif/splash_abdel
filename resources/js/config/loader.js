import axios from 'axios';
import { useLoadingStore } from '@/stores/loading';

// Create axios instance with default config
const axiosInstance = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    withCredentials: true
});

// Track active requests to handle multiple concurrent requests
let activeRequests = 0;

// Request interceptor - Show loader when requests start
axiosInstance.interceptors.request.use(
    (config) => {
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            config.headers['X-CSRF-TOKEN'] = csrfToken;
        }

        // Increment active requests counter
        activeRequests++;
        
        // Show loader if this is the first active request
        if (activeRequests === 1) {
            const loadingStore = useLoadingStore();
            loadingStore.startLoading();
        }

        return config;
    },
    (error) => {
        // Decrement on error
        activeRequests = Math.max(0, activeRequests - 1);
        
        // Hide loader if no active requests
        if (activeRequests === 0) {
            const loadingStore = useLoadingStore();
            loadingStore.stopLoading();
        }
        
        return Promise.reject(error);
    }
);

// Response interceptor - Hide loader when requests complete
axiosInstance.interceptors.response.use(
    (response) => {
        // Decrement active requests counter
        activeRequests = Math.max(0, activeRequests - 1);
        
        // Hide loader if no active requests
        if (activeRequests === 0) {
            const loadingStore = useLoadingStore();
            loadingStore.stopLoading();
        }
        
        return response;
    },
    (error) => {
        // Decrement on error
        activeRequests = Math.max(0, activeRequests - 1);
        
        // Hide loader if no active requests
        if (activeRequests === 0) {
            const loadingStore = useLoadingStore();
            loadingStore.stopLoading();
        }
        
        // Handle 419 CSRF token mismatch
        if (error.response?.status === 419) {
            // Try to refresh CSRF token
            return axios.get('/sanctum/csrf-cookie', {
                withCredentials: true
            }).then(() => {
                // Retry the original request
                const config = error.config;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    config.headers['X-CSRF-TOKEN'] = csrfToken;
                }
                return axiosInstance.request(config);
            });
        }
        
        return Promise.reject(error);
    }
);

// Export the configured axios instance
export default axiosInstance;

