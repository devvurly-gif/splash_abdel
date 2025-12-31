<template>
    <header class="bg-bg-elevated shadow-sm sticky top-0 z-[100] border-b border-surface-border">
        <div class="max-w-full mx-auto px-6 flex items-center justify-between h-16">
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar" class="flex items-center justify-center w-10 h-10 border-none bg-transparent text-text-secondary cursor-pointer rounded-lg transition-all duration-200 hover:bg-surface-hover hover:text-accent-primary" v-if="showMenuToggle">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <h1 class="text-2xl font-bold text-accent-primary m-0 bg-gradient-to-br from-accent-primary to-accent-secondary bg-clip-text text-transparent">Splash</h1>
            </div>

            <div class="flex items-center gap-4">
                <ThemeSelector />
                <LocaleSwitcher />
                <div class="relative flex items-center gap-3 cursor-pointer px-3 py-2 rounded-lg transition-all duration-200 hover:bg-surface-hover group">
                    <div class="flex flex-col items-end gap-0.5">
                        <span class="text-sm font-semibold text-text-primary">{{ authStore.userName || 'User' }}</span>
                        <span class="text-xs text-text-secondary">{{ authStore.userEmail }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent-primary to-accent-secondary flex items-center justify-center text-text-inverse font-semibold text-sm">
                        <span>{{ userInitials }}</span>
                    </div>
                    <div class="absolute top-full right-0 mt-2 bg-bg-elevated rounded-lg shadow-lg border border-surface-border min-w-[180px] opacity-0 invisible -translate-y-2 transition-all duration-200 z-50 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0">
                        <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-3 border-none bg-transparent text-text-primary text-sm cursor-pointer transition-all duration-200 text-left hover:bg-surface-hover hover:text-accent-error">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            <span>{{ t('common.logout') }}</span>
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
import { useI18n } from 'vue-i18n';
import LocaleSwitcher from './LocaleSwitcher.vue';
import ThemeSelector from './ThemeSelector.vue';

const { t } = useI18n();

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

