import { createRouter, createWebHistory } from 'vue-router';
import Login from '../pages/auth/login.vue';

const routes = [
    {
        path: '/',
        name: 'login',
        component: Login
    },
    {
        path: '/login',
        name: 'login-alias',
        component: Login
    },
    {
        path: '/app',
        component: () => import('../pages/secure/AppTemplate.vue'),
        children: [
            {
                path: 'dashboard',
                name: 'dashboard',
                component: () => import('../pages/secure/dashboard.vue')
            }
        ]
    }
    // Add more routes here
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

export default router;

