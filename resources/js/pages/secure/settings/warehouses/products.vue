<template>
    <div class="w-full">
        <div class="flex items-center gap-4 mb-6">
            <button @click="$router.back()" class="p-2 border-none rounded-lg cursor-pointer transition-all duration-200 flex items-center justify-center bg-bg-elevated text-text-primary hover:bg-surface-hover border border-surface-border">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </button>
            <div>
                <h1 class="text-3xl font-bold text-text-primary m-0">{{ $t('warehouse.products') }}</h1>
                <p class="text-text-secondary mt-1 mb-0">{{ warehouse?.title }} ({{ warehouse?.code }})</p>
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
            <button @click="handlePrint" class="flex items-center gap-2 px-5 py-2.5 border-2 border-surface-border rounded-lg bg-bg-primary text-text-primary font-medium cursor-pointer transition-all duration-200 hover:border-border-hover hover:bg-surface-hover">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                <span>{{ $t('common.print') }}</span>
            </button>
            <button @click="handleExportXLS" class="flex items-center gap-2 px-5 py-2.5 border-none rounded-lg bg-gradient-to-br from-green-500 to-green-600 text-white font-medium cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:brightness-110">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span>{{ $t('common.exportXLS') }}</span>
            </button>
        </div>

        <div class="bg-bg-elevated rounded-xl overflow-hidden shadow-md border border-surface-border">
            <table class="w-full border-collapse">
                <thead class="bg-bg-secondary">
                    <tr>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('product.code') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('product.name') }}</th>
                        <th class="p-4 text-right font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('warehouse.quantity') }}</th>
                        <th class="p-4 text-right font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('warehouse.cmup') }}</th>
                        <th class="p-4 text-right font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('warehouse.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="5" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.loading') }}</span>
                        </td>
                    </tr>
                    <tr v-else-if="products.length === 0">
                        <td colspan="5" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.noData') }}</span>
                        </td>
                    </tr>
                    <tr v-else v-for="product in products" :key="product.id" class="hover:bg-surface-hover">
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ product.code }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary font-medium">{{ product.name }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary text-right">{{ product.quantity || 0 }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary text-right">{{ formatPrice(product.cmup || 0) }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary text-right font-semibold">{{ formatPrice(calculateTotal(product)) }}</td>
                    </tr>
                </tbody>
                <tfoot v-if="products.length > 0" class="bg-bg-secondary">
                    <tr>
                        <td colspan="2" class="p-4 text-left font-semibold text-text-secondary border-t-2 border-surface-border">{{ $t('warehouse.grandTotal') }}</td>
                        <td class="p-4 text-right font-semibold text-text-secondary border-t-2 border-surface-border">{{ totalQuantity }}</td>
                        <td class="p-4 text-right font-semibold text-text-secondary border-t-2 border-surface-border">-</td>
                        <td class="p-4 text-right font-semibold text-text-primary border-t-2 border-surface-border">{{ formatPrice(grandTotal) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <Pagination
            v-if="pagination && (pagination.last_page > 1 || perPage !== 15)"
            :current-page="pagination?.current_page || 1"
            :total-pages="pagination?.last_page || 1"
            :total-items="pagination?.total || 0"
            :per-page="perPage"
            :show-total="true"
            @page-change="changePage"
            @per-page-change="changePerPage"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';
import * as XLSX from 'xlsx';

const route = useRoute();
const { error } = useToast();
const { t } = useI18n();

const warehouse = ref(null);
const products = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const perPage = ref(15);
const pagination = ref(null);
let searchTimeout = null;

// Computed properties for totals
const totalQuantity = computed(() => {
    return products.value.reduce((sum, product) => sum + (parseInt(product.quantity) || 0), 0);
});

const grandTotal = computed(() => {
    return products.value.reduce((sum, product) => {
        const quantity = parseInt(product.quantity) || 0;
        const cmup = parseFloat(product.cmup) || 0;
        return sum + (quantity * cmup);
    }, 0);
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

const fetchWarehouseProducts = async (params = {}) => {
    loading.value = true;
    const warehouseId = route.params.id;

    try {
        const queryParams = new URLSearchParams({
            per_page: params.per_page || perPage.value,
            page: params.page || 1,
            ...(params.search && { search: params.search })
        });

        const response = await fetch(`/api/warehouses/${warehouseId}/products?${queryParams}`, {
            method: 'GET',
            headers: getHeaders(),
            credentials: 'include'
        });

        const data = await response.json();

        if (response.ok && data.success) {
            warehouse.value = data.data.warehouse;
            const responseData = data.data.products;
            
            if (responseData && Array.isArray(responseData.data)) {
                products.value = responseData.data;
                pagination.value = {
                    current_page: responseData.current_page || 1,
                    last_page: responseData.last_page || 1,
                    per_page: responseData.per_page || 15,
                    total: responseData.total || 0
                };
            } else {
                products.value = [];
            }
        } else {
            error(data.message || 'Failed to fetch products');
            products.value = [];
        }
    } catch (err) {
        console.error('Fetch products error:', err);
        error('An error occurred while fetching products');
        products.value = [];
    } finally {
        loading.value = false;
    }
};

const formatPrice = (price) => {
    if (!price && price !== 0) return '0.00';
    return parseFloat(price).toFixed(2);
};

const calculateTotal = (product) => {
    const quantity = parseInt(product.quantity) || 0;
    const cmup = parseFloat(product.cmup) || 0;
    return quantity * cmup;
};

const handlePrint = () => {
    const printWindow = window.open('', '_blank');
    const warehouseTitle = warehouse.value?.title || '';
    const warehouseCode = warehouse.value?.code || '';
    
    let printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>${t('warehouse.products')} - ${warehouseTitle}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #333; margin-bottom: 10px; }
                h2 { color: #666; margin-bottom: 20px; font-size: 14px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .text-right { text-align: right; }
                .text-left { text-align: left; }
                tfoot { background-color: #f2f2f2; font-weight: bold; }
                @media print {
                    body { margin: 0; }
                    button { display: none; }
                }
            </style>
        </head>
        <body>
            <h1>${t('warehouse.products')}</h1>
            <h2>${warehouseTitle} (${warehouseCode})</h2>
            <table>
                <thead>
                    <tr>
                        <th>${t('product.code')}</th>
                        <th>${t('product.name')}</th>
                        <th class="text-right">${t('warehouse.quantity')}</th>
                        <th class="text-right">${t('warehouse.cmup')}</th>
                        <th class="text-right">${t('warehouse.total')}</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    products.value.forEach(product => {
        const total = calculateTotal(product);
        printContent += `
            <tr>
                <td>${product.code || ''}</td>
                <td>${product.name || ''}</td>
                <td class="text-right">${product.quantity || 0}</td>
                <td class="text-right">${formatPrice(product.cmup || 0)}</td>
                <td class="text-right">${formatPrice(total)}</td>
            </tr>
        `;
    });
    
    printContent += `
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><strong>${t('warehouse.grandTotal')}</strong></td>
                        <td class="text-right"><strong>${totalQuantity.value}</strong></td>
                        <td class="text-right">-</td>
                        <td class="text-right"><strong>${formatPrice(grandTotal.value)}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
};

const handleExportXLS = () => {
    try {
        // Prepare data for export
        const exportData = products.value.map(product => {
            const total = calculateTotal(product);
            return {
                [t('product.code')]: product.code || '',
                [t('product.name')]: product.name || '',
                [t('warehouse.quantity')]: product.quantity || 0,
                [t('warehouse.cmup')]: parseFloat(product.cmup || 0).toFixed(2),
                [t('warehouse.total')]: formatPrice(total)
            };
        });
        
        // Add grand total row
        exportData.push({
            [t('product.code')]: '',
            [t('product.name')]: t('warehouse.grandTotal'),
            [t('warehouse.quantity')]: totalQuantity.value,
            [t('warehouse.cmup')]: '-',
            [t('warehouse.total')]: formatPrice(grandTotal.value)
        });
        
        // Create workbook and worksheet
        const ws = XLSX.utils.json_to_sheet(exportData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, t('warehouse.products'));
        
        // Generate filename
        const warehouseTitle = warehouse.value?.title || 'Warehouse';
        const warehouseCode = warehouse.value?.code || '';
        const filename = `${warehouseTitle}_${warehouseCode}_${new Date().toISOString().split('T')[0]}.xlsx`;
        
        // Export file
        XLSX.writeFile(wb, filename);
    } catch (error) {
        console.error('Export error:', error);
        error('Failed to export to Excel');
    }
};

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchWarehouseProducts({
            per_page: perPage.value,
            page: 1,
            search: searchQuery.value
        });
    }, 500);
};

const changePage = (page) => {
    fetchWarehouseProducts({
        per_page: perPage.value,
        page,
        search: searchQuery.value
    });
};

const changePerPage = (newPerPage) => {
    perPage.value = newPerPage;
    fetchWarehouseProducts({
        per_page: newPerPage,
        page: 1,
        search: searchQuery.value
    });
};

onMounted(() => {
    fetchWarehouseProducts({ per_page: perPage.value });
});
</script>

