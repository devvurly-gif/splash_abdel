<template>
    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-text-primary m-0">{{ $t('document.sale.title') }}</h1>
            <div class="flex gap-3">
                <select v-model="documentType" @change="handleTypeChange" class="py-2.5 px-4 border-2 border-surface-border rounded-lg text-sm cursor-pointer bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light">
                    <option value="invoice">{{ $t('document.sale.invoice') }}</option>
                    <option value="delivery_note">{{ $t('document.sale.deliveryNote') }}</option>
                    <option value="return">{{ $t('document.sale.return') }}</option>
                </select>
                <button @click="openCreateForm" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-accent-primary to-accent-secondary text-text-inverse border-none rounded-lg font-semibold cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:brightness-110">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    <span>{{ $t('common.create') }}</span>
                </button>
            </div>
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
                <option value="draft">{{ $t('document.status.draft') }}</option>
                <option value="validated">{{ $t('document.status.validated') }}</option>
                <option value="cancelled">{{ $t('document.status.cancelled') }}</option>
            </select>
        </div>

        <div class="bg-bg-elevated rounded-xl overflow-hidden shadow-md border border-surface-border">
            <table class="w-full border-collapse">
                <thead class="bg-bg-secondary">
                    <tr>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('document.code') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('document.partner') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('document.date') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('document.total') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('document.status') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="documentStore.loading">
                        <td colspan="6" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.loading') }}</span>
                        </td>
                    </tr>
                    <tr v-else-if="documentStore.documents.length === 0">
                        <td colspan="6" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.noData') }}</span>
                        </td>
                    </tr>
                    <tr v-else v-for="document in documentStore.documents" :key="document.id" class="hover:bg-surface-hover">
                        <td class="p-4 border-b border-surface-border text-text-primary font-medium">{{ document.code }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ document.partner?.name || '-' }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ formatDate(document.document_date) }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary font-semibold">{{ formatCurrency(document.total_amount) }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <span :class="[
                                'inline-block px-3 py-1 rounded-xl text-xs font-semibold',
                                getStatusClass(document.status)
                            ]">
                                {{ $t(`document.status.${document.status}`) }}
                            </span>
                        </td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <div class="flex gap-2">
                                <button @click="viewDocument(document)" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-blue-100 text-accent-info hover:bg-blue-200 dark:bg-blue-900/15 dark:hover:bg-blue-900/25" :title="$t('common.view')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                                <button v-if="document.status === 'draft'" @click="editDocument(document)" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/15 dark:hover:bg-green-900/25" :title="$t('common.edit')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <button v-if="document.status === 'draft'" @click="validateDocument(document)" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-purple-100 text-purple-700 hover:bg-purple-200 dark:bg-purple-900/15 dark:hover:bg-purple-900/25" :title="$t('document.validate')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                </button>
                                <button v-if="document.status === 'draft'" @click="deleteDocument(document)" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-red-100 text-accent-error hover:bg-red-200 dark:bg-red-900/15 dark:hover:bg-red-900/25" :title="$t('common.delete')">
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

        <div v-if="documentStore.pagination.last_page > 1" class="flex justify-center items-center gap-4 mt-6">
            <button
                @click="changePage(documentStore.pagination.current_page - 1)"
                :disabled="documentStore.pagination.current_page === 1"
                class="px-4 py-2 border-2 border-surface-border rounded-lg bg-bg-primary text-text-primary cursor-pointer transition-all duration-200 hover:border-accent-primary hover:text-accent-primary hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ $t('common.previous') }}
            </button>
            <span class="text-text-secondary text-sm">
                {{ $t('common.page') }} {{ documentStore.pagination.current_page }} {{ $t('common.of') }} {{ documentStore.pagination.last_page }}
            </span>
            <button
                @click="changePage(documentStore.pagination.current_page + 1)"
                :disabled="documentStore.pagination.current_page === documentStore.pagination.last_page"
                class="px-4 py-2 border-2 border-surface-border rounded-lg bg-bg-primary text-text-primary cursor-pointer transition-all duration-200 hover:border-accent-primary hover:text-accent-primary hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ $t('common.next') }}
            </button>
        </div>

        <SaleForm
            v-if="showForm"
            :show="showForm"
            :document="editingDocument"
            :type="documentType"
            @close="closeForm"
            @saved="handleSaved"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useDocumentStore } from '@/stores/document';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';
import SaleForm from './form.vue';

const documentStore = useDocumentStore();
const { success, error } = useToast();
const { t } = useI18n();

const showForm = ref(false);
const editingDocument = ref(null);
const documentType = ref('invoice');
const searchQuery = ref('');
const statusFilter = ref('');
let searchTimeout = null;

onMounted(async () => {
    await loadDocuments();
});

const loadDocuments = async () => {
    const result = await documentStore.fetchDocuments({
        domain: 'sale',
        type: documentType.value,
        status: statusFilter.value || undefined,
        search: searchQuery.value || undefined
    });
    if (!result.success) {
        error(result.error || t('document.loadFailed'));
    }
};

const openCreateForm = () => {
    editingDocument.value = null;
    documentStore.clearCurrentDocument();
    showForm.value = true;
};

const editDocument = (document) => {
    editingDocument.value = document;
    documentStore.setCurrentDocument(document);
    showForm.value = true;
};

const viewDocument = async (document) => {
    // TODO: Navigate to detail view or open modal
    console.log('View document:', document);
};

const validateDocument = async (document) => {
    if (!confirm(t('document.confirmValidate', { code: document.code }))) {
        return;
    }

    const result = await documentStore.validateDocument(document.id);
    if (result.success) {
        success(t('document.validated'));
        await loadDocuments();
    } else {
        error(result.error || t('document.validateFailed'));
    }
};

const deleteDocument = async (document) => {
    if (!confirm(t('document.confirmDelete', { code: document.code }))) {
        return;
    }

    const result = await documentStore.deleteDocument(document.id);
    if (result.success) {
        success(t('document.deleted'));
        await loadDocuments();
    } else {
        error(result.error || t('document.deleteFailed'));
    }
};

const closeForm = () => {
    showForm.value = false;
    editingDocument.value = null;
    documentStore.clearCurrentDocument();
};

const handleSaved = () => {
    closeForm();
    loadDocuments();
};

const handleTypeChange = () => {
    loadDocuments();
};

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadDocuments();
    }, 500);
};

const handleFilter = () => {
    loadDocuments();
};

const changePage = (page) => {
    documentStore.fetchDocuments({
        page,
        domain: 'sale',
        type: documentType.value,
        status: statusFilter.value || undefined,
        search: searchQuery.value || undefined
    });
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};

const formatCurrency = (amount) => {
    if (!amount) return '0.00';
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);
};

const getStatusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-700 dark:bg-gray-900/20 dark:text-gray-400',
        validated: 'bg-green-100 text-accent-success dark:bg-green-900/20 dark:text-accent-success',
        cancelled: 'bg-red-100 text-accent-error dark:bg-red-900/20 dark:text-accent-error',
        completed: 'bg-blue-100 text-accent-info dark:bg-blue-900/20 dark:text-accent-info'
    };
    return classes[status] || classes.draft;
};
</script>

