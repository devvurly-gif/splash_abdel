import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

// Lazy load components
const Login = () => import('../pages/auth/login.vue');
const AppTemplate = () => import('../pages/secure/AppTemplate.vue');
const Dashboard = () => import('../pages/secure/dashboard.vue');
const CategoriesIndex = () => import('../pages/secure/categories/index.vue');
const BrandsIndex = () => import('../pages/secure/brands/index.vue');
const NumberingSystemsIndex = () => import('../pages/secure/settings/numerotation/index.vue');
const WarehousesIndex = () => import('../pages/secure/settings/warehouses/index.vue');

const routes = [
    {
        path: '/',
        redirect: '/app/dashboard'
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            title: 'Login',
            requiresAuth: false,
            layout: 'auth'
        }
    },
    {
        path: '/app',
        component: AppTemplate,
        meta: {
            requiresAuth: true
        },
        children: [
            {
                path: '',
                redirect: 'dashboard'
            },
            {
                path: 'dashboard',
                name: 'dashboard',
                component: Dashboard,
                meta: {
                    title: 'Dashboard',
                    requiresAuth: true,
                    icon: 'dashboard'
                }
            },
            {
                path: 'categories',
                name: 'categories',
                component: CategoriesIndex,
                meta: {
                    title: 'Categories',
                    requiresAuth: true,
                    icon: 'categories'
                }
            },
            {
                path: 'brands',
                name: 'brands',
                component: BrandsIndex,
                meta: {
                    title: 'Brands',
                    requiresAuth: true,
                    icon: 'brands'
                }
            },
            {
                path: 'settings/numerotation',
                name: 'numbering-systems',
                component: NumberingSystemsIndex,
                meta: {
                    title: 'Numbering Systems',
                    requiresAuth: true,
                    icon: 'settings'
                }
            },
            {
                path: 'settings/warehouses',
                name: 'warehouses',
                component: WarehousesIndex,
                meta: {
                    title: 'Warehouses',
                    requiresAuth: true,
                    icon: 'settings'
                }
            }
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        redirect: '/app/dashboard'
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    // Scroll behavior
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    }
});

// Navigation guards
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    
    // Check if route requires authentication
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    
    // Initialize auth from localStorage if not already done
    if (!authStore.user) {
        authStore.initializeAuth();
    }
    
    // Verify authentication with server if route requires auth
    if (requiresAuth && !authStore.isAuthenticated) {
        const isAuthenticated = await authStore.checkAuth();
        if (!isAuthenticated) {
            // Redirect to login if not authenticated
            next({
                name: 'login',
                query: { redirect: to.fullPath }
            });
            return;
        }
    }
    
    // Redirect authenticated users away from login page
    if (!requiresAuth && authStore.isAuthenticated && to.name === 'login') {
        next({ name: 'dashboard' });
        return;
    }
    
    // Update document title
    if (to.meta.title) {
        document.title = `${to.meta.title} - Splash`;
    } else {
        document.title = 'Splash';
    }
    
    next();
});

export default router;


