<template>
    <div class="inline-block">
        <select v-model="currentLocale" @change="handleLocaleChange" class="py-1.5 px-3 border border-surface-border rounded-md bg-bg-elevated text-text-primary text-sm cursor-pointer transition-all duration-200 hover:border-accent-primary hover:bg-surface-hover focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light">
            <option value="en">English</option>
            <option value="fr">Français</option>
        </select>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useLocale } from '@/composables/useLocale';

const { locale, setLocale } = useLocale();
const currentLocale = ref(locale.value);

onMounted(() => {
    currentLocale.value = locale.value;
});

const handleLocaleChange = () => {
    setLocale(currentLocale.value);
};
</script>

