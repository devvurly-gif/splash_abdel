<template>
    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-text-primary m-0">{{ $t('stockMovement.title') }}</h1>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-4 mb-6">
            <div class="flex-1 min-w-[250px] relative flex items-center">
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
            <select v-model="warehouseFilter" @change="handleFilter" class="py-2.5 px-4 border-2 border-surface-border rounded-lg text-sm cursor-pointer bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light min-w-[180px]">
                <option value="">{{ $t('stockMovement.allWarehouses') }}</option>
                <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
                    {{ warehouse.title }}
                </option>
            </select>
            <select v-model="productFilter" @change="handleFilter" class="py-2.5 px-4 border-2 border-surface-border rounded-lg text-sm cursor-pointer bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light min-w-[180px]">
                <option value="">{{ $t('stockMovement.allProducts') }}</option>
                <option v-for="product in products" :key="product.id" :value="product.id">
                    {{ product.name }}
                </option>
            </select>
            <select v-model="movementTypeFilter" @change="handleFilter" class="py-2.5 px-4 border-2 border-surface-border rounded-lg text-sm cursor-pointer bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light min-w-[180px]">
                <option value="">{{ $t('stockMovement.allTypes') }}</option>
                <option value="entry">{{ $t('stockMovement.entry') }}</option>
                <option value="exit">{{ $t('stockMovement.exit') }}</option>
            </select>
            <select v-model="typeFilter" @change="handleFilter" class="py-2.5 px-4 border-2 border-surface-border rounded-lg text-sm cursor-pointer bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light min-w-[200px]">
                <option value="">{{ $t('stockMovement.allMovementTypes') }}</option>
                <option value="sale_invoice">{{ $t('stockMovement.saleInvoice') }}</option>
                <option value="sale_delivery">{{ $t('stockMovement.saleDelivery') }}</option>
                <option value="sale_return">{{ $t('stockMovement.saleReturn') }}</option>
                <option value="purchase_invoice">{{ $t('stockMovement.purchaseInvoice') }}</option>
                <option value="purchase_receipt">{{ $t('stockMovement.purchaseReceipt') }}</option>
                <option value="purchase_return">{{ $t('stockMovement.purchaseReturn') }}</option>
                <option value="transfer_in">{{ $t('stockMovement.transferIn') }}</option>
                <option value="transfer_out">{{ $t('stockMovement.transferOut') }}</option>
                <option value="adjustment_increase">{{ $t('stockMovement.adjustmentIncrease') }}</option>
                <option value="adjustment_decrease">{{ $t('stockMovement.adjustmentDecrease') }}</option>
                <option value="manual_entry">{{ $t('stockMovement.manualEntry') }}</option>
                <option value="manual_exit">{{ $t('stockMovement.manualExit') }}</option>
            </select>
            <div class="flex gap-2 min-w-[300px]">
                <input
                    v-model="dateFrom"
                    type="date"
                    @change="handleFilter"
                    class="flex-1 py-2.5 px-3 border-2 border-surface-border rounded-lg text-sm bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light"
                    :placeholder="$t('stockMovement.dateFrom')"
                />
                <input
                    v-model="dateTo"
                    type="date"
                    @change="handleFilter"
                    class="flex-1 py-2.5 px-3 border-2 border-surface-border rounded-lg text-sm bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light"
                    :placeholder="$t('stockMovement.dateTo')"
                />
            </div>
        </div>

        <!-- Table -->
        <div class="bg-bg-elevated rounded-xl overflow-hidden shadow-md border border-surface-border">
            <table class="w-full border-collapse">
                <thead class="bg-bg-secondary">
                    <tr>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('stockMovement.code') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('stockMovement.date') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('stockMovement.warehouse') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('stockMovement.product') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('stockMovement.type') }}</th>
                        <th class="p-4 text-right font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('stockMovement.quantity') }}</th>
                        <th class="p-4 text-right font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('stockMovement.unitCost') }}</th>
                        <th class="p-4 text-right font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('stockMovement.totalCost') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('stockMovement.document') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="journalStockStore.loading">
                        <td colspan="10" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.loading') }}</span>
                        </td>
                    </tr>
                    <tr v-else-if="journalStockStore.movements.length === 0">
                        <td colspan="10" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.noData') }}</span>
                        </td>
                    </tr>
                    <tr v-else v-for="movement in journalStockStore.movements" :key="movement.id" class="hover:bg-surface-hover">
                        <td class="p-4 border-b border-surface-border text-text-primary font-medium">{{ movement.code }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ formatDate(movement.movement_date) }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ movement.warehouse?.title || '-' }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ movement.product?.name || '-' }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <span :class="[
                                'inline-block px-3 py-1 rounded-xl text-xs font-semibold',
                                getMovementTypeClass(movement.movement_type, movement.quantity)
                            ]">
                                {{ $t(`stockMovement.${getMovementTypeLabel(movement.movement_type)}`) }}
                            </span>
                        </td>
                        <td class="p-4 border-b border-surface-border text-text-primary text-right">
                            <span :class="movement.quantity >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                {{ formatQuantity(movement.quantity) }}
                            </span>
                        </td>
                        <td class="p-4 border-b border-surface-border text-text-primary text-right">{{ formatCurrency(movement.unit_cost) }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary text-right font-semibold">{{ formatCurrency(movement.total_cost) }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <span v-if="movement.document_header" class="text-accent-primary hover:underline cursor-pointer" @click="viewDocument(movement.document_header)">
                                {{ movement.document_header.code }}
                            </span>
                            <span v-else class="text-text-tertiary">-</span>
                        </td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <div class="flex gap-2">
                                <button @click="viewMovement(movement)" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-blue-100 text-accent-info hover:bg-blue-200 dark:bg-blue-900/15 dark:hover:bg-blue-900/25" :title="$t('common.view')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="journalStockStore.pagination.last_page > 1" class="flex justify-between items-center mt-6">
            <div class="text-sm text-text-secondary">
                {{ $t('common.page') }} {{ journalStockStore.pagination.current_page }} {{ $t('common.of') }} {{ journalStockStore.pagination.last_page }}
                ({{ journalStockStore.pagination.total }} {{ $t('stockMovement.totalMovements') }})
            </div>
            <div class="flex gap-2">
                <button
                    @click="loadPage(journalStockStore.pagination.current_page - 1)"
                    :disabled="journalStockStore.pagination.current_page === 1"
                    class="px-4 py-2 border-2 border-surface-border rounded-lg text-sm font-medium cursor-pointer transition-all duration-200 bg-bg-primary text-text-primary hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ $t('common.previous') }}
                </button>
                <button
                    @click="loadPage(journalStockStore.pagination.current_page + 1)"
                    :disabled="journalStockStore.pagination.current_page === journalStockStore.pagination.last_page"
                    class="px-4 py-2 border-2 border-surface-border rounded-lg text-sm font-medium cursor-pointer transition-all duration-200 bg-bg-primary text-text-primary hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ $t('common.next') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useJournalStockStore } from '@/stores/journalStock';
import { useWarehouseStore } from '@/stores/warehouse';

const router = useRouter();
const journalStockStore = useJournalStockStore();
const warehouseStore = useWarehouseStore();

// Filters
const searchQuery = ref('');
const warehouseFilter = ref('');
const productFilter = ref('');
const movementTypeFilter = ref('');
const typeFilter = ref('');
const dateFrom = ref('');
const dateTo = ref('');

// State
const products = ref([]);

// Computed
const warehouses = computed(() => warehouseStore.warehouses || []);

// Methods
const loadMovements = async (page = 1) => {
    const params = {
        page,
        per_page: 15,
        ...(searchQuery.value && { search: searchQuery.value }),
        ...(warehouseFilter.value && { warehouse_id: warehouseFilter.value }),
        ...(productFilter.value && { product_id: productFilter.value }),
        ...(movementTypeFilter.value && { entry_type: movementTypeFilter.value }),
        ...(typeFilter.value && { movement_type: typeFilter.value }),
        ...(dateFrom.value && { date_from: dateFrom.value }),
        ...(dateTo.value && { date_to: dateTo.value })
    };

    await journalStockStore.fetchMovements(params);
};

const handleSearch = () => {
    loadMovements(1);
};

const handleFilter = () => {
    loadMovements(1);
};

const loadPage = (page) => {
    loadMovements(page);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};

const formatQuantity = (quantity) => {
    return quantity >= 0 ? `+${quantity}` : `${quantity}`;
};

const formatCurrency = (amount) => {
    if (!amount) return '0.00';
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
};

const getMovementTypeLabel = (type) => {
    // Map movement types to translation keys
    const typeMap = {
        'sale_invoice': 'saleInvoice',
        'sale_delivery': 'saleDelivery',
        'sale_return': 'saleReturn',
        'purchase_invoice': 'purchaseInvoice',
        'purchase_receipt': 'purchaseReceipt',
        'purchase_return': 'purchaseReturn',
        'transfer_in': 'transferIn',
        'transfer_out': 'transferOut',
        'adjustment_increase': 'adjustmentIncrease',
        'adjustment_decrease': 'adjustmentDecrease',
        'manual_entry': 'manualEntry',
        'manual_exit': 'manualExit'
    };
    return typeMap[type] || type;
};

const getMovementTypeClass = (type, quantity) => {
    if (quantity >= 0) {
        return 'bg-green-100 text-green-700 dark:bg-green-900/15 dark:text-green-400';
    } else {
        return 'bg-red-100 text-red-700 dark:bg-red-900/15 dark:text-red-400';
    }
};

const viewMovement = (movement) => {
    // TODO: Implement view movement details modal or page
    console.log('View movement:', movement);
};

const viewDocument = (document) => {
    // Navigate to document based on domain
    if (document.domain === 'sale') {
        router.push(`/app/documents/sale`);
    } else if (document.domain === 'purchase') {
        router.push(`/app/documents/purchase`);
    } else if (document.domain === 'stock') {
        router.push(`/app/documents/stock`);
    }
};

// Helper to fetch products
const fetchProducts = async () => {
    try {
        const response = await fetch('/api/products?per_page=1000&status=1', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'include'
        });
        const data = await response.json();
        if (response.ok && data.success) {
            products.value = Array.isArray(data.data?.data) ? data.data.data : (Array.isArray(data.data) ? data.data : []);
        }
    } catch (err) {
        console.error('Failed to fetch products:', err);
    }
};

// Lifecycle
onMounted(async () => {
    // Load warehouses and products for filters
    await warehouseStore.fetchWarehouses();
    await fetchProducts();
    
    // Load movements
    await loadMovements();
});
</script>

