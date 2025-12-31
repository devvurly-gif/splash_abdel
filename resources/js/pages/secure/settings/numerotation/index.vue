<template>
    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-text-primary m-0">{{ $t('numberingSystem.title') }}</h1>
            <button @click="openCreateModal" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-accent-primary to-accent-secondary text-text-inverse border-none rounded-lg font-semibold cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:brightness-110">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                <span>{{ $t('common.create') }}</span>
            </button>
        </div>

        <div class="flex gap-4 mb-6">
            <div class="flex-1 relative flex items-center">
                <svg class="absolute left-3 text-text-tertiary" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input
                    v-model="searchQuery"
                    type="text"
                    :placeholder="$t('common.search')"
                    @input="handleSearch"
                    class="w-full py-2.5 pl-10 pr-3 border-2 border-surface-border rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light placeholder:text-text-tertiary"
                />
            </div>
            <select v-model="domainFilter" @change="handleFilter" class="py-2.5 px-4 border-2 border-surface-border rounded-lg text-sm cursor-pointer bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light">
                <option value="">{{ $t('common.all') }}</option>
                <option value="structure">{{ $t('numberingSystem.structure') }}</option>
                <option value="sale">{{ $t('numberingSystem.sale') }}</option>
                <option value="purchase">{{ $t('numberingSystem.purchase') }}</option>
                <option value="stock">{{ $t('numberingSystem.stock') }}</option>
            </select>
            <select v-model="statusFilter" @change="handleFilter" class="py-2.5 px-4 border-2 border-surface-border rounded-lg text-sm cursor-pointer bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light">
                <option value="">{{ $t('common.all') }}</option>
                <option value="1">{{ $t('common.active') }}</option>
                <option value="0">{{ $t('common.inactive') }}</option>
            </select>
        </div>

        <div class="bg-bg-elevated rounded-xl overflow-hidden shadow-md border border-surface-border">
            <table class="w-full border-collapse">
                <thead class="bg-bg-secondary">
                    <tr>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('numberingSystem.title') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('numberingSystem.domain') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('numberingSystem.type') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('numberingSystem.template') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('numberingSystem.nextTrick') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('numberingSystem.isActive') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="numberingSystemStore.loading">
                        <td colspan="7" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.loading') }}</span>
                        </td>
                    </tr>
                    <tr v-else-if="numberingSystemStore.numberingSystems.length === 0">
                        <td colspan="7" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.noData') }}</span>
                        </td>
                    </tr>
                    <tr v-else v-for="system in numberingSystemStore.numberingSystems" :key="system.id" class="hover:bg-surface-hover">
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ system.title }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <span class="inline-block px-3 py-1 rounded-xl text-xs font-semibold bg-accent-primary-light text-accent-primary">{{ getDomainLabel(system.domain) }}</span>
                        </td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ system.type }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <code class="bg-bg-tertiary px-2 py-1 rounded text-xs text-text-secondary font-mono">{{ system.template }}</code>
                        </td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ system.next_trick }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <span :class="[
                                'inline-block px-3 py-1 rounded-xl text-xs font-semibold',
                                system.isActive 
                                    ? 'bg-green-100 text-accent-success dark:bg-green-900/20 dark:text-accent-success' 
                                    : 'bg-red-100 text-accent-error dark:bg-red-900/20 dark:text-accent-error'
                            ]">
                                {{ system.isActive ? $t('common.active') : $t('common.inactive') }}
                            </span>
                        </td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <div class="flex gap-2">
                                <button 
                                    @click="handleGenerateNext(system)" 
                                    class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-green-100 text-accent-success hover:bg-green-200 dark:bg-green-900/15 dark:hover:bg-green-900/25 disabled:opacity-50 disabled:cursor-not-allowed" 
                                    :title="$t('numberingSystem.generateNext')"
                                    :disabled="!system.isActive"
                                >
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/>
                                        <path d="M21 3v5h-5"/>
                                    </svg>
                                </button>
                                <button @click="openEditModal(system)" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-blue-100 text-accent-info hover:bg-blue-200 dark:bg-blue-900/15 dark:hover:bg-blue-900/25" :title="$t('common.edit')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <button @click="handleDelete(system)" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-red-100 text-accent-error hover:bg-red-200 dark:bg-red-900/15 dark:hover:bg-red-900/25" :title="$t('common.delete')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="numberingSystemStore.pagination.last_page > 1" class="flex justify-center items-center gap-4 mt-6">
            <button
                @click="changePage(numberingSystemStore.pagination.current_page - 1)"
                :disabled="numberingSystemStore.pagination.current_page === 1"
                class="px-4 py-2 border-2 border-surface-border rounded-lg bg-bg-primary text-text-primary cursor-pointer transition-all duration-200 hover:border-accent-primary hover:text-accent-primary hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ $t('common.previous') }}
            </button>
            <span class="text-text-secondary text-sm">
                {{ $t('common.page') }} {{ numberingSystemStore.pagination.current_page }} {{ $t('common.of') }} {{ numberingSystemStore.pagination.last_page }}
            </span>
            <button
                @click="changePage(numberingSystemStore.pagination.current_page + 1)"
                :disabled="numberingSystemStore.pagination.current_page === numberingSystemStore.pagination.last_page"
                class="px-4 py-2 border-2 border-surface-border rounded-lg bg-bg-primary text-text-primary cursor-pointer transition-all duration-200 hover:border-accent-primary hover:text-accent-primary hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ $t('common.next') }}
            </button>
        </div>

        <NumberingSystemModal
            v-if="showModal"
            :show="showModal"
            :numberingSystem="editingNumberingSystem"
            @close="closeModal"
            @saved="handleSaved"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useNumberingSystemStore } from '@/stores/numberingSystem';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';
import NumberingSystemModal from './modal.vue';

const numberingSystemStore = useNumberingSystemStore();
const { success, error } = useToast();
const { t } = useI18n();

const showModal = ref(false);
const editingNumberingSystem = ref(null);
const searchQuery = ref('');
const domainFilter = ref('');
const statusFilter = ref('');
let searchTimeout = null;

onMounted(async () => {
    const result = await numberingSystemStore.fetchNumberingSystems();
    if (!result.success) {
        console.error('Failed to fetch numbering systems:', result.error);
    } else {
        console.log('Numbering systems loaded:', numberingSystemStore.numberingSystems);
    }
});

const openCreateModal = () => {
    editingNumberingSystem.value = null;
    numberingSystemStore.clearCurrentNumberingSystem();
    showModal.value = true;
};

const openEditModal = (system) => {
    editingNumberingSystem.value = system;
    numberingSystemStore.setCurrentNumberingSystem(system);
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingNumberingSystem.value = null;
    numberingSystemStore.clearCurrentNumberingSystem();
};

const handleSaved = () => {
    closeModal();
};

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        numberingSystemStore.fetchNumberingSystems({
            search: searchQuery.value,
            domain: domainFilter.value || undefined,
            isActive: statusFilter.value !== '' ? statusFilter.value === '1' : undefined
        });
    }, 500);
};

const handleFilter = () => {
    numberingSystemStore.fetchNumberingSystems({
        search: searchQuery.value,
        domain: domainFilter.value || undefined,
        isActive: statusFilter.value !== '' ? statusFilter.value === '1' : undefined
    });
};

const changePage = (page) => {
    numberingSystemStore.fetchNumberingSystems({
        page,
        search: searchQuery.value,
        domain: domainFilter.value || undefined,
        isActive: statusFilter.value !== '' ? statusFilter.value === '1' : undefined
    });
};

const handleDelete = async (system) => {
    if (!confirm(t('numberingSystem.confirmDelete', { title: system.title }))) {
        return;
    }

    const result = await numberingSystemStore.deleteNumberingSystem(system.id);
    if (result.success) {
        success(t('numberingSystem.deleted'));
    } else {
        error(result.error || t('numberingSystem.deleteFailed'));
    }
};

const handleGenerateNext = async (system) => {
    const result = await numberingSystemStore.generateNext(system.id);
    if (result.success) {
        success(t('numberingSystem.generatedNext', { number: result.data.number }));
    } else {
        error(result.error || t('numberingSystem.generateFailed'));
    }
};

const getDomainLabel = (domain) => {
    const domainMap = {
        structure: t('numberingSystem.structure'),
        sale: t('numberingSystem.sale'),
        purchase: t('numberingSystem.purchase'),
        stock: t('numberingSystem.stock')
    };
    return domainMap[domain] || domain;
};
</script>

