import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useThemeStore = defineStore('theme', () => {
    // State - default to 'light' or system preference
    const theme = ref(localStorage.getItem('theme') || 'light');

    // Initialize theme on store creation
    const initTheme = () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            theme.value = savedTheme;
        } else {
            // Check system preference
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            theme.value = prefersDark ? 'dark' : 'light';
        }
        applyTheme(theme.value);
    };

    // Apply theme to document
    const applyTheme = (newTheme) => {
        const root = document.documentElement;
        if (newTheme === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
    };

    // Set theme
    const setTheme = (newTheme) => {
        if (newTheme !== 'light' && newTheme !== 'dark') {
            console.warn('Invalid theme. Use "light" or "dark"');
            return;
        }
        theme.value = newTheme;
        localStorage.setItem('theme', newTheme);
        applyTheme(newTheme);
    };

    // Toggle theme
    const toggleTheme = () => {
        const newTheme = theme.value === 'light' ? 'dark' : 'light';
        setTheme(newTheme);
    };

    // Watch for system preference changes
    if (typeof window !== 'undefined') {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const handleChange = (e) => {
            // Only apply system preference if no manual theme is set
            if (!localStorage.getItem('theme')) {
                theme.value = e.matches ? 'dark' : 'light';
                applyTheme(theme.value);
            }
        };
        mediaQuery.addEventListener('change', handleChange);
    }

    // Initialize on store creation
    if (typeof window !== 'undefined') {
        initTheme();
    }

    return {
        theme,
        setTheme,
        toggleTheme,
        initTheme
    };
});

