/**
 * Vue Toastification composable for easy toast notifications
 * Usage in components:
 * 
 * import { useToast } from '@/composables/useToast';
 * 
 * const { success, error, info, warning } = useToast();
 * 
 * success('Operation completed!');
 * error('Something went wrong!');
 * info('Here is some info');
 * warning('Be careful!');
 */

import { useToast as useVueToast } from 'vue-toastification';

export function useToast() {
    const toast = useVueToast();

    const success = (message, options = {}) => {
        return toast.success(message, {
            timeout: 3000,
            ...options
        });
    };

    const error = (message, options = {}) => {
        return toast.error(message, {
            timeout: 4000,
            ...options
        });
    };

    const info = (message, options = {}) => {
        return toast.info(message, {
            timeout: 3000,
            ...options
        });
    };

    const warning = (message, options = {}) => {
        return toast.warning(message, {
            timeout: 3000,
            ...options
        });
    };

    const defaultToast = (message, options = {}) => {
        return toast(message, {
            timeout: 3000,
            ...options
        });
    };

    return {
        toast,
        success,
        error,
        info,
        warning,
        default: defaultToast
    };
}

