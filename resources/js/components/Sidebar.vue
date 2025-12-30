<template>
    <aside :class="['sidebar', { 'sidebar-collapsed': isCollapsed }]">
        <nav class="sidebar-nav">
            <div class="sidebar-header">
                <h2 v-if="!isCollapsed" class="sidebar-title">Menu</h2>
            </div>

            <ul class="sidebar-menu">
                <li class="menu-item">
                    <router-link
                        to="/app/dashboard"
                        class="menu-link"
                        :class="{ 'menu-link-active': isActiveRoute('/app/dashboard') }"
                    >
                        <span class="menu-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="menu-text">Dashboard</span>
                    </router-link>
                </li>
                <!-- Add more menu items here -->
            </ul>
        </nav>

        <div class="sidebar-footer" v-if="!isCollapsed">
            <div class="sidebar-footer-content">
                <p class="footer-text">© 2024 Splash</p>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const props = defineProps({
    isCollapsed: {
        type: Boolean,
        default: false
    }
});

const route = useRoute();

const isActiveRoute = (path) => {
    return route.path === path || route.path.startsWith(path + '/');
};
</script>

<style scoped>
.sidebar {
    width: 260px;
    min-height: 100vh;
    background: white;
    border-right: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    transition: width 0.3s ease;
    position: fixed;
    left: 0;
    top: 64px;
    bottom: 0;
    overflow-y: auto;
    z-index: 10;
}

.sidebar-collapsed {
    width: 80px;
}

.sidebar-nav {
    flex: 1;
    padding: 24px 0;
}

.sidebar-header {
    padding: 0 24px 16px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 16px;
}

.sidebar-title {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-item {
    margin: 4px 0;
}

.menu-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 24px;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.2s ease;
    position: relative;
}

.menu-link:hover {
    background-color: #f9fafb;
    color: #667eea;
}

.menu-link-active {
    background-color: rgba(102, 126, 234, 0.1);
    color: #667eea;
    font-weight: 600;
}

.menu-link-active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #667eea;
    border-radius: 0 2px 2px 0;
}

.menu-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.menu-text {
    font-size: 14px;
}

.sidebar-collapsed .menu-text {
    display: none;
}

.sidebar-collapsed .menu-link {
    justify-content: center;
    padding: 12px;
}

.sidebar-footer {
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
    margin-top: auto;
}

.sidebar-footer-content {
    text-align: center;
}

.footer-text {
    font-size: 12px;
    color: #9ca3af;
    margin: 0;
}

.sidebar-collapsed .sidebar-footer {
    padding: 16px;
}

.sidebar-collapsed .footer-text {
    display: none;
}

/* Scrollbar styling */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

