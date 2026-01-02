<template>
    <Transition name="fade">
        <div v-if="loading" class="fixed inset-0 bg-black dark:bg-black flex items-center justify-center z-[9999]">
            <div class="bg-bg-elevated rounded-xl p-8 shadow-2xl border border-surface-border flex flex-col items-center gap-4">
                <div class="relative w-16 h-16">
                    <svg class="animate-spin text-accent-primary" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                    </svg>
                </div>
                <p class="text-text-primary font-medium">{{ text || $t('common.loading') }}</p>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { useI18n } from 'vue-i18n';

defineProps({
    loading: {
        type: Boolean,
        default: false
    },
    text: {
        type: String,
        default: null
    }
});

const { t } = useI18n();
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

