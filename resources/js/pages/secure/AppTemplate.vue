<template>
    <div class="app-template">
        <Topbar @toggle-sidebar="toggleSidebar" :show-menu-toggle="true" />
        <div class="app-layout">
            <Sidebar :is-collapsed="sidebarCollapsed" />
            <main class="app-main" :class="{ 'main-expanded': sidebarCollapsed }">
                <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Topbar from '@/components/Topbar.vue';
import Sidebar from '@/components/Sidebar.vue';

const sidebarCollapsed = ref(false);

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};
</script>

<style scoped>
.app-template {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: #f5f5f5;
}

.app-layout {
    display: flex;
    flex: 1;
    margin-top: 64px;
}

.app-main {
    flex: 1;
    padding: 24px;
    margin-left: 260px;
    transition: margin-left 0.3s ease;
    min-height: calc(100vh - 64px);
}

.main-expanded {
    margin-left: 80px;
}

@media (max-width: 768px) {
    .app-main {
        margin-left: 0;
    }
    
    .main-expanded {
        margin-left: 0;
    }
}
</style>

