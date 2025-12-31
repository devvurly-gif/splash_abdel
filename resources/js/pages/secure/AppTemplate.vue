<template>
    <div class="min-h-screen flex flex-col bg-bg-secondary">
        <Topbar @toggle-sidebar="toggleSidebar" :show-menu-toggle="true" />
        <div class="flex flex-1 mt-16">
            <Sidebar :is-collapsed="sidebarCollapsed" />
            <main :class="[
                'flex-1 p-6 min-h-[calc(100vh-4rem)] transition-all duration-300',
                sidebarCollapsed ? 'ml-20' : 'ml-[260px]',
                'max-md:ml-0'
            ]">
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

