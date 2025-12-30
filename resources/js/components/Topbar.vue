<template>
    <header class="topbar">
        <div class="topbar-content">
            <div class="topbar-left">
                <button @click="toggleSidebar" class="menu-toggle" v-if="showMenuToggle">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <h1 class="app-logo">Splash</h1>
            </div>

            <div class="topbar-right">
                <div class="user-menu">
                    <div class="user-info">
                        <span class="user-name">{{ authStore.userName || 'User' }}</span>
                        <span class="user-email">{{ authStore.userEmail }}</span>
                    </div>
                    <div class="user-avatar">
                        <span>{{ userInitials }}</span>
                    </div>
                    <div class="dropdown-menu">
                        <button @click="handleLogout" class="dropdown-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';

const props = defineProps({
    showMenuToggle: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['toggle-sidebar']);

const authStore = useAuthStore();

const userInitials = computed(() => {
    if (!authStore.userName) return 'U';
    const names = authStore.userName.split(' ');
    if (names.length >= 2) {
        return (names[0][0] + names[1][0]).toUpperCase();
    }
    return authStore.userName.substring(0, 2).toUpperCase();
});

const toggleSidebar = () => {
    emit('toggle-sidebar');
};

const handleLogout = async () => {
    await authStore.logout();
};
</script>

<style scoped>
.topbar {
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 100;
    border-bottom: 1px solid #e5e7eb;
}

.topbar-content {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 64px;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.menu-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: none;
    background: transparent;
    color: #555;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.menu-toggle:hover {
    background-color: #f3f4f6;
    color: #667eea;
}

.app-logo {
    font-size: 24px;
    font-weight: 700;
    color: #667eea;
    margin: 0;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.user-menu {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 8px;
    transition: background-color 0.2s ease;
}

.user-menu:hover {
    background-color: #f9fafb;
}

.user-info {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
}

.user-email {
    font-size: 12px;
    color: #6b7280;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    min-width: 180px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);
    transition: all 0.2s ease;
    z-index: 50;
}

.user-menu:hover .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border: none;
    background: transparent;
    color: #374151;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    text-align: left;
}

.dropdown-item:hover {
    background-color: #f3f4f6;
    color: #ef4444;
}

.dropdown-item svg {
    flex-shrink: 0;
}
</style>

