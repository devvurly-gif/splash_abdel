<template>
    <aside :class="[
        'min-h-screen bg-bg-elevated border-r border-surface-border flex flex-col transition-all duration-300 fixed left-0 top-16 bottom-0 overflow-y-auto z-10',
        isCollapsed ? 'w-20' : 'w-[260px]'
    ]">
        <nav class="flex-1 py-6">
            <div class="px-6 pb-4 border-b border-surface-border mb-4">
                <h2 v-if="!isCollapsed" class="text-xs font-semibold text-text-tertiary uppercase tracking-wider m-0">Menu</h2>
            </div>

            <ul class="list-none p-0 m-0">
                <li class="my-1">
                    <router-link
                        to="/app/dashboard"
                        :class="[
                            'flex items-center gap-3 px-6 py-3 text-text-secondary no-underline transition-all duration-200 relative',
                            isActiveRoute('/app/dashboard') 
                                ? 'bg-accent-primary-light text-accent-primary font-semibold before:content-[\'\'] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-0.5 before:bg-accent-primary before:rounded-r-sm' 
                                : 'hover:bg-surface-hover hover:text-accent-primary',
                            isCollapsed ? 'justify-center px-3' : ''
                        ]"
                    >
                        <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.dashboard') }}</span>
                    </router-link>
                </li>
                <li class="my-1">
                    <router-link
                        to="/app/categories"
                        :class="[
                            'flex items-center gap-3 px-6 py-3 text-text-secondary no-underline transition-all duration-200 relative',
                            isActiveRoute('/app/categories') 
                                ? 'bg-accent-primary-light text-accent-primary font-semibold before:content-[\'\'] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-0.5 before:bg-accent-primary before:rounded-r-sm' 
                                : 'hover:bg-surface-hover hover:text-accent-primary',
                            isCollapsed ? 'justify-center px-3' : ''
                        ]"
                    >
                        <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 7h16M4 12h16M4 17h16"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.categories') }}</span>
                    </router-link>
                </li>
                <li class="my-1">
                    <router-link
                        to="/app/brands"
                        :class="[
                            'flex items-center gap-3 px-6 py-3 text-text-secondary no-underline transition-all duration-200 relative',
                            isActiveRoute('/app/brands') 
                                ? 'bg-accent-primary-light text-accent-primary font-semibold before:content-[\'\'] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-0.5 before:bg-accent-primary before:rounded-r-sm' 
                                : 'hover:bg-surface-hover hover:text-accent-primary',
                            isCollapsed ? 'justify-center px-3' : ''
                        ]"
                    >
                        <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.brands') }}</span>
                    </router-link>
                </li>
                <li class="my-1">
                    <router-link
                        to="/app/products"
                        :class="[
                            'flex items-center gap-3 px-6 py-3 text-text-secondary no-underline transition-all duration-200 relative',
                            isActiveRoute('/app/products') 
                                ? 'bg-accent-primary-light text-accent-primary font-semibold before:content-[\'\'] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-0.5 before:bg-accent-primary before:rounded-r-sm' 
                                : 'hover:bg-surface-hover hover:text-accent-primary',
                            isCollapsed ? 'justify-center px-3' : ''
                        ]"
                    >
                        <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.products') }}</span>
                    </router-link>
                </li>
                
                <!-- Settings Section -->
                <li v-if="!isCollapsed" class="my-4 px-6">
                    <h3 class="text-xs font-semibold text-text-tertiary uppercase tracking-wider m-0">{{ $t('navigation.settings') }}</h3>
                </li>
                <li class="my-1">
                    <router-link
                        to="/app/settings/numerotation"
                        :class="[
                            'flex items-center gap-3 px-6 py-3 text-text-secondary no-underline transition-all duration-200 relative',
                            isActiveRoute('/app/settings/numerotation') 
                                ? 'bg-accent-primary-light text-accent-primary font-semibold before:content-[\'\'] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-0.5 before:bg-accent-primary before:rounded-r-sm' 
                                : 'hover:bg-surface-hover hover:text-accent-primary',
                            isCollapsed ? 'justify-center px-3' : ''
                        ]"
                    >
                        <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <path d="M8 8h8M8 12h8M8 16h4"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.numberingSystems') }}</span>
                    </router-link>
                </li>
                <li class="my-1">
                    <router-link
                        to="/app/settings/warehouses"
                        :class="[
                            'flex items-center gap-3 px-6 py-3 text-text-secondary no-underline transition-all duration-200 relative',
                            isActiveRoute('/app/settings/warehouses') 
                                ? 'bg-accent-primary-light text-accent-primary font-semibold before:content-[\'\'] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-0.5 before:bg-accent-primary before:rounded-r-sm' 
                                : 'hover:bg-surface-hover hover:text-accent-primary',
                            isCollapsed ? 'justify-center px-3' : ''
                        ]"
                    >
                        <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.warehouses') }}</span>
                    </router-link>
                </li>
                
                <!-- Add more menu items here -->
            </ul>
        </nav>

        <div v-if="!isCollapsed" :class="['px-6 py-4 border-t border-surface-border mt-auto', isCollapsed ? 'px-4' : '']">
            <div class="text-center">
                <p :class="['text-xs text-text-tertiary m-0', isCollapsed ? 'hidden' : '']">© 2024 Splash</p>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

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
/* Custom scrollbar styling for sidebar */
aside::-webkit-scrollbar {
    width: 6px;
}

aside::-webkit-scrollbar-track {
    background: var(--bg-secondary);
}

aside::-webkit-scrollbar-thumb {
    background: var(--border-default);
    border-radius: 3px;
}

aside::-webkit-scrollbar-thumb:hover {
    background: var(--border-hover);
}
</style>

