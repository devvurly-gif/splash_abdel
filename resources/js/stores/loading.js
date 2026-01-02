import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useLoadingStore = defineStore('loading', () => {
    const isLoading = ref(false);
    const loadingText = ref(null);

    const setLoading = (value, text = null) => {
        isLoading.value = value;
        loadingText.value = text;
    };

    const startLoading = (text = null) => {
        setLoading(true, text);
    };

    const stopLoading = () => {
        setLoading(false, null);
    };

    return {
        isLoading,
        loadingText,
        setLoading,
        startLoading,
        stopLoading
    };
});

