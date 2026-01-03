<template>
    <div v-if="show" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-[1000] p-5" @click.self="handleClose">
        <div class="bg-bg-elevated rounded-xl w-full max-w-[700px] max-h-[90vh] overflow-y-auto shadow-xl border border-surface-border">
            <div class="flex justify-between items-center p-6 border-b border-surface-border">
                <div>
                    <h2 class="text-xl font-semibold text-text-primary m-0">
                        {{ $t('product.warehouses') }}
                    </h2>
                    <p class="text-sm text-text-secondary mt-1 mb-0">{{ product?.name }} ({{ product?.code }})</p>
                </div>
                <button @click="handleClose" class="bg-transparent border-none text-text-secondary cursor-pointer p-1 rounded transition-all duration-200 hover:bg-surface-hover hover:text-text-primary">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <div class="p-0">
                <div v-if="loading" class="text-center p-10 text-text-tertiary">
                    <span>{{ $t('common.loading') }}</span>
                </div>
                <div v-else-if="warehouses.length === 0" class="text-center p-10 text-text-tertiary">
                    <span>{{ $t('product.noWarehouses') }}</span>
                </div>
                <div v-else>
                    <div class="bg-bg-elevated rounded-xl overflow-hidden shadow-md border border-surface-border">
                        <table class="w-full border-collapse">
                            <thead class="bg-bg-secondary">
                                <tr>
                                    <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('warehouse.code') }}</th>
                                    <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('warehouse.title') }}</th>
                                    <th class="p-4 text-center w-1/3 font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('warehouse.quantity') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="warehouse in warehouses"
                                    :key="warehouse.id"
                                    class="hover:bg-surface-hover"
                                >
                                    <td class="p-4 border-b border-surface-border text-text-primary">{{ warehouse.code }}</td>
                                    <td class="p-4 border-b border-surface-border text-text-primary font-medium">{{ warehouse.title }}</td>
                                    <td class="p-4 border-b border-surface-border text-text-primary text-center w-1/3">{{ warehouse.quantity || 0 }}</td>
                                </tr>
                            </tbody>
                            <tfoot v-if="warehouses.length > 0" class="bg-bg-secondary">
                                <tr>
                                    <td colspan="2" class="p-4 text-left font-semibold text-text-secondary border-t-2 border-surface-border">{{ $t('warehouse.total') }}</td>
                                    <td class="p-4 text-center w-1/3 font-semibold text-text-primary border-t-2 border-surface-border">{{ totalQuantity }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 p-6 border-t border-surface-border">
                <button @click="handleClose" class="px-5 py-2.5 border-2 border-surface-border rounded-lg bg-bg-primary text-text-primary font-medium cursor-pointer transition-all duration-200 hover:border-border-hover hover:bg-surface-hover">
                    {{ $t('common.close') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    product: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close']);

const { error } = useToast();
const { t } = useI18n();

const warehouses = ref([]);
const loading = ref(false);

// Computed properties for totals
const totalQuantity = computed(() => {
    return warehouses.value.reduce((sum, warehouse) => sum + (parseInt(warehouse.quantity) || 0), 0);
});

// Helper function to get CSRF token
const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

// Helper function to get headers
const getHeaders = () => {
    return {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
    };
};

const fetchWarehouses = async () => {
    if (!props.product?.id) return;
    
    loading.value = true;
    try {
        const response = await fetch(`/api/products/${props.product.id}/warehouses`, {
            method: 'GET',
            headers: getHeaders(),
            credentials: 'include'
        });

        const data = await response.json();

        if (response.ok && data.success) {
            warehouses.value = data.data.warehouses || [];
        } else {
            error(data.message || 'Failed to fetch warehouses');
            warehouses.value = [];
        }
    } catch (err) {
        console.error('Fetch warehouses error:', err);
        error('An error occurred while fetching warehouses');
        warehouses.value = [];
    } finally {
        loading.value = false;
    }
};

watch(() => props.show, (isShow) => {
    if (isShow && props.product) {
        fetchWarehouses();
    } else {
        warehouses.value = [];
    }
});

watch(() => props.product, (newProduct) => {
    if (props.show && newProduct) {
        fetchWarehouses();
    }
}, { immediate: true });

const handleClose = () => {
    emit('close');
    warehouses.value = [];
};
</script>

