<template>
    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-text-primary m-0">{{ $t('navigation.warehouses') }}</h1>
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
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('warehouse.code') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('warehouse.title') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('warehouse.status') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="warehouseStore.loading">
                        <td colspan="4" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.loading') }}</span>
                        </td>
                    </tr>
                    <tr v-else-if="warehouseStore.warehouses.length === 0">
                        <td colspan="4" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.noData') }}</span>
                        </td>
                    </tr>
                    <tr v-else v-for="warehouse in warehouseStore.warehouses" :key="warehouse.id" class="hover:bg-surface-hover">
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ warehouse.code }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ warehouse.title }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <span :class="[
                                'inline-block px-3 py-1 rounded-xl text-xs font-semibold',
                                warehouse.status 
                                    ? 'bg-green-100 text-accent-success dark:bg-green-900/20 dark:text-accent-success' 
                                    : 'bg-red-100 text-accent-error dark:bg-red-900/20 dark:text-accent-error'
                            ]">
                                {{ warehouse.status ? $t('common.active') : $t('common.inactive') }}
                            </span>
                        </td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <div class="flex gap-2">
                                <button @click="openEditModal(warehouse)" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-blue-100 text-accent-info hover:bg-blue-200 dark:bg-blue-900/15 dark:hover:bg-blue-900/25" :title="$t('common.edit')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <button @click="handleDelete(warehouse)" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-red-100 text-accent-error hover:bg-red-200 dark:bg-red-900/15 dark:hover:bg-red-900/25" :title="$t('common.delete')">
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

        <div v-if="warehouseStore.pagination.last_page > 1" class="flex justify-center items-center gap-4 mt-6">
            <button
                @click="changePage(warehouseStore.pagination.current_page - 1)"
                :disabled="warehouseStore.pagination.current_page === 1"
                class="px-4 py-2 border-2 border-surface-border rounded-lg bg-bg-primary text-text-primary cursor-pointer transition-all duration-200 hover:border-accent-primary hover:text-accent-primary hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ $t('common.previous') }}
            </button>
            <span class="text-text-secondary text-sm">
                {{ $t('common.page') }} {{ warehouseStore.pagination.current_page }} {{ $t('common.of') }} {{ warehouseStore.pagination.last_page }}
            </span>
            <button
                @click="changePage(warehouseStore.pagination.current_page + 1)"
                :disabled="warehouseStore.pagination.current_page === warehouseStore.pagination.last_page"
                class="px-4 py-2 border-2 border-surface-border rounded-lg bg-bg-primary text-text-primary cursor-pointer transition-all duration-200 hover:border-accent-primary hover:text-accent-primary hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ $t('common.next') }}
            </button>
        </div>

        <WarehouseModal
            v-if="showModal"
            :show="showModal"
            :warehouse="editingWarehouse"
            @close="closeModal"
            @saved="handleSaved"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useWarehouseStore } from '@/stores/warehouse';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';
import WarehouseModal from './modal.vue';

const warehouseStore = useWarehouseStore();
const { success, error } = useToast();
const { t } = useI18n();

const showModal = ref(false);
const editingWarehouse = ref(null);
const searchQuery = ref('');
const statusFilter = ref('');
let searchTimeout = null;

onMounted(async () => {
    const result = await warehouseStore.fetchWarehouses();
    if (!result.success) {
        console.error('Failed to fetch warehouses:', result.error);
    } else {
        console.log('Warehouses loaded:', warehouseStore.warehouses);
    }
});

const openCreateModal = () => {
    editingWarehouse.value = null;
    warehouseStore.clearCurrentWarehouse();
    showModal.value = true;
};

const openEditModal = (warehouse) => {
    editingWarehouse.value = warehouse;
    warehouseStore.setCurrentWarehouse(warehouse);
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingWarehouse.value = null;
    warehouseStore.clearCurrentWarehouse();
};

const handleSaved = () => {
    closeModal();
    // Toast notification is already shown in modal.vue, no need to show again
};

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        warehouseStore.fetchWarehouses({
            search: searchQuery.value,
            status: statusFilter.value !== '' ? statusFilter.value === '1' : undefined
        });
    }, 500);
};

const handleFilter = () => {
    warehouseStore.fetchWarehouses({
        search: searchQuery.value,
        status: statusFilter.value !== '' ? statusFilter.value === '1' : undefined
    });
};

const changePage = (page) => {
    warehouseStore.fetchWarehouses({
        page,
        search: searchQuery.value,
        status: statusFilter.value !== '' ? statusFilter.value === '1' : undefined
    });
};

const handleDelete = async (warehouse) => {
    if (!confirm(t('warehouse.confirmDelete', { title: warehouse.title }))) {
        return;
    }

    const result = await warehouseStore.deleteWarehouse(warehouse.id);
    if (result.success) {
        success(t('warehouse.deleted'));
    } else {
        error(result.error || t('warehouse.deleteFailed'));
    }
};
</script>

