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
                <router-view v-slot="{ Component }">
                    <Transition name="fade" mode="out-in" @after-enter="onPageRendered">
                        <component :is="Component" :key="$route.fullPath" />
                    </Transition>
                </router-view>
            </main>
        </div>
        <Loader :loading="loadingStore.isLoading" :text="loadingStore.loadingText" />
    </div>
</template>

<script setup>
import { ref, nextTick, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useLoadingStore } from '@/stores/loading';
import Topbar from '@/components/Topbar.vue';
import Sidebar from '@/components/Sidebar.vue';
import Loader from '@/components/Loader.vue';

const loadingStore = useLoadingStore();
const route = useRoute();
const sidebarCollapsed = ref(false);

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};

// Hide loader after page transition is complete
const onPageRendered = async () => {
    // Wait for all DOM updates and async operations
    await nextTick();
    await nextTick(); // Double nextTick to ensure all child components are rendered
    
    // Additional delay to ensure images, fonts, and other resources are loaded
    setTimeout(() => {
        loadingStore.stopLoading();
    }, 300);
};

// Watch route changes to handle loader when transition doesn't fire
watch(() => route.fullPath, () => {
    // If loader is still showing after route change, ensure it gets hidden
    setTimeout(() => {
        if (loadingStore.isLoading) {
            onPageRendered();
        }
    }, 1000);
}, { immediate: false });

// Ensure loader is hidden on mount
onMounted(() => {
    setTimeout(() => {
        if (loadingStore.isLoading) {
            loadingStore.stopLoading();
        }
    }, 500);
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

