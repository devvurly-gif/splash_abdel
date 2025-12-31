/**
 * Composable for managing locale/i18n
 * Usage:
 * 
 * import { useLocale } from '@/composables/useLocale';
 * 
 * const { locale, setLocale, t } = useLocale();
 * 
 * setLocale('ar');
 * const message = t('common.welcome');
 */

import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

export function useLocale() {
    const { locale, t } = useI18n();

    const setLocale = (newLocale) => {
        locale.value = newLocale;
        localStorage.setItem('locale', newLocale);
        // Update HTML lang attribute
        document.documentElement.lang = newLocale;
        // All supported languages are LTR
        document.documentElement.dir = 'ltr';
    };

    const currentLocale = computed(() => locale.value);

    const isRTL = computed(() => false); // Both English and French are LTR

    return {
        locale: currentLocale,
        setLocale,
        t,
        isRTL,
    };
}

