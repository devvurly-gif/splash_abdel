import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from '@/composables/useToast';

export const useAuthStore = defineStore('auth', () => {
    // Initialize router and toast at top level
    const router = useRouter();
    const { success: showSuccess, error: showError } = useToast();

    // State
    const user = ref(null);
    const token = ref(null);
    const loading = ref(false);

    // Getters
    const isAuthenticated = computed(() => !!user.value && !!token.value);
    const userName = computed(() => user.value?.name || '');
    const userEmail = computed(() => user.value?.email || '');

    // Helper function to get CSRF token
    const getCsrfToken = () => {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    };

    // Helper function to get headers
    const getHeaders = () => {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        };

        if (token.value) {
            headers['Authorization'] = `Bearer ${token.value}`;
        }

        return headers;
    };

    // Actions
    const login = async (credentials) => {
        loading.value = true;

        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include', // Include cookies for session-based auth
                body: JSON.stringify({
                    email: credentials.email,
                    password: credentials.password,
                    remember: credentials.remember || false
                })
            });

            const data = await response.json();

            if (response.ok) {
                // Store user data and token
                user.value = data.user || data;
                token.value = data.token || data.access_token || null;

                // Store token in localStorage if remember is true
                if (credentials.remember && token.value) {
                    localStorage.setItem('auth_token', token.value);
                    localStorage.setItem('user', JSON.stringify(user.value));
                }

                showSuccess('Login successful!');
                
                // Redirect to dashboard
                router.push('/app/dashboard');
                
                return { success: true, data };
            } else {
                const errorMessage = data.message || data.error || 'Login failed. Please check your credentials.';
                console.error('Login failed:', errorMessage, data);
                showError(errorMessage);
                return { success: false, error: errorMessage };
            }
        } catch (err) {
            const errorMessage = 'An error occurred. Please try again.';
            console.error('Login error:', err);
            showError(errorMessage);
            return { success: false, error: errorMessage };
        } finally {
            loading.value = false;
        }
    };

    const logout = async () => {
        loading.value = true;

        try {
            const response = await fetch('/api/logout', {
                method: 'POST',
                headers: getHeaders(),
                credentials: 'include' // Include cookies for session-based auth
            });

            if (response.ok || response.status === 401 || response.status === 422) {
                // Clear state regardless of response
                clearAuth();
                showSuccess('Logged out successfully');
                router.push('/login');
                return { success: true };
            } else {
                // Still logout even if API call fails
                clearAuth();
                showError('Logout failed');
                router.push('/login');
                return { success: false };
            }
        } catch (err) {
            // Still logout even if API call fails
            clearAuth();
            showError('An error occurred during logout');
            console.error('Logout error:', err);
            router.push('/login');
            return { success: false, error: err.message };
        } finally {
            loading.value = false;
        }
    };

    const clearAuth = () => {
        user.value = null;
        token.value = null;
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
    };

    const checkAuth = async () => {
        try {
            const response = await fetch('/api/user', {
                method: 'GET',
                headers: getHeaders(),
                credentials: 'include' // Include cookies for session-based auth
            });

            if (response.ok) {
                const data = await response.json();
                user.value = data;
                return true;
            } else {
                clearAuth();
                return false;
            }
        } catch (err) {
            console.error('Auth check error:', err);
            clearAuth();
            return false;
        }
    };

    const initializeAuth = () => {
        // Try to restore from localStorage
        const storedToken = localStorage.getItem('auth_token');
        const storedUser = localStorage.getItem('user');

        if (storedToken && storedUser) {
            token.value = storedToken;
            try {
                user.value = JSON.parse(storedUser);
            } catch (e) {
                console.error('Error parsing stored user:', e);
                clearAuth();
            }
        }
    };

    return {
        // State
        user,
        token,
        loading,
        // Getters
        isAuthenticated,
        userName,
        userEmail,
        // Actions
        login,
        logout,
        clearAuth,
        checkAuth,
        initializeAuth
    };
});

