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
                        to="/app/documents/sale"
                        :class="[
                            'flex items-center gap-3 px-6 py-3 text-text-secondary no-underline transition-all duration-200 relative',
                            isActiveRoute('/app/documents/sale') 
                                ? 'bg-accent-primary-light text-accent-primary font-semibold before:content-[\'\'] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-0.5 before:bg-accent-primary before:rounded-r-sm' 
                                : 'hover:bg-surface-hover hover:text-accent-primary',
                            isCollapsed ? 'justify-center px-3' : ''
                        ]"
                    >
                        <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10 9 9 9 8 9"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.sales') }}</span>
                    </router-link>
                </li>
                <li class="my-1">
                    <router-link
                        to="/app/documents/purchase"
                        :class="[
                            'flex items-center gap-3 px-6 py-3 text-text-secondary no-underline transition-all duration-200 relative',
                            isActiveRoute('/app/documents/purchase') 
                                ? 'bg-accent-primary-light text-accent-primary font-semibold before:content-[\'\'] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-0.5 before:bg-accent-primary before:rounded-r-sm' 
                                : 'hover:bg-surface-hover hover:text-accent-primary',
                            isCollapsed ? 'justify-center px-3' : ''
                        ]"
                    >
                        <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                                <path d="M9 14l2 2 4-4"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.purchases') }}</span>
                    </router-link>
                </li>
                <li class="my-1">
                    <router-link
                        to="/app/documents/stock"
                        :class="[
                            'flex items-center gap-3 px-6 py-3 text-text-secondary no-underline transition-all duration-200 relative',
                            isActiveRoute('/app/documents/stock') 
                                ? 'bg-accent-primary-light text-accent-primary font-semibold before:content-[\'\'] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-0.5 before:bg-accent-primary before:rounded-r-sm' 
                                : 'hover:bg-surface-hover hover:text-accent-primary',
                            isCollapsed ? 'justify-center px-3' : ''
                        ]"
                    >
                        <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.stock') }}</span>
                    </router-link>
                </li>
                <li class="my-1">
                    <router-link
                        to="/app/stock/movements"
                        :class="[
                            'flex items-center gap-3 px-6 py-3 text-text-secondary no-underline transition-all duration-200 relative',
                            isActiveRoute('/app/stock/movements') 
                                ? 'bg-accent-primary-light text-accent-primary font-semibold before:content-[\'\'] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-0.5 before:bg-accent-primary before:rounded-r-sm' 
                                : 'hover:bg-surface-hover hover:text-accent-primary',
                            isCollapsed ? 'justify-center px-3' : ''
                        ]"
                    >
                        <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 3v18h18M7 16l4-4 4 4 6-6"/>
                                <path d="M17 8V3h-5"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.stockMovements') }}</span>
                    </router-link>
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
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                                <path d="M7 14h.01M3 14h.01M11 14h.01M15 14h.01M19 14h.01"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.warehouses') }}</span>
                    </router-link>
                </li>
                <li class="my-1">
                    <router-link
                        to="/app/settings/partners"
                        :class="[
                            'flex items-center gap-3 px-6 py-3 text-text-secondary no-underline transition-all duration-200 relative',
                            isActiveRoute('/app/settings/partners') 
                                ? 'bg-accent-primary-light text-accent-primary font-semibold before:content-[\'\'] before:absolute before:left-0 before:top-0 before:bottom-0 before:w-0.5 before:bg-accent-primary before:rounded-r-sm' 
                                : 'hover:bg-surface-hover hover:text-accent-primary',
                            isCollapsed ? 'justify-center px-3' : ''
                        ]"
                    >
                        <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </span>
                        <span v-if="!isCollapsed" class="text-sm">{{ $t('navigation.partners') }}</span>
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

