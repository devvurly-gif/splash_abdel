<template>
    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-text-primary m-0">{{ $t('navigation.products') }}</h1>
            <router-link to="/app/products/new" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-accent-primary to-accent-secondary text-text-inverse border-none rounded-lg font-semibold cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:brightness-110 no-underline">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                <span>{{ $t('common.create') }}</span>
            </router-link>
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
            <select v-model="categoryFilter" @change="handleFilter" class="py-2.5 px-4 border-2 border-surface-border rounded-lg text-sm cursor-pointer bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light">
                <option value="">{{ $t('product.allCategories') }}</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.title }}</option>
            </select>
            <select v-model="brandFilter" @change="handleFilter" class="py-2.5 px-4 border-2 border-surface-border rounded-lg text-sm cursor-pointer bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light">
                <option value="">{{ $t('product.allBrands') }}</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.title }}</option>
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
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('product.image') || 'Image' }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('product.code') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('product.name') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('product.category') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('product.brand') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('product.salePrice') }}</th>
                        <th class="p-4 text-right font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('product.stock') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('product.status') }}</th>
                        <th class="p-4 text-left font-semibold text-text-secondary text-sm border-b-2 border-surface-border">{{ $t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="productStore.loading">
                        <td colspan="9" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.loading') }}</span>
                        </td>
                    </tr>
                    <tr v-else-if="productStore.products.length === 0">
                        <td colspan="9" class="text-center p-10 text-text-tertiary">
                            <span>{{ $t('common.noData') }}</span>
                        </td>
                    </tr>
                    <tr v-else v-for="product in productStore.products" :key="product.id" class="hover:bg-surface-hover">
                        <td class="p-4 border-b border-surface-border">
                            <div class="w-16 h-16 bg-bg-secondary rounded-lg overflow-hidden flex items-center justify-center">
                                <img 
                                    v-if="getPrimaryImage(product)" 
                                    :src="getPrimaryImage(product).url" 
                                    :alt="getPrimaryImage(product).alt || product.name"
                                    class="w-full h-full object-cover"
                                />
                                <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-text-tertiary">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                            </div>
                        </td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ product.code }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ product.name }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ product.category?.title || '-' }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ product.brand?.title || '-' }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary">{{ formatPrice(product.sale_price) }}</td>
                        <td class="p-4 border-b border-surface-border text-text-primary text-right font-semibold">
                            {{ product.total_stock || 0 }}
                        </td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <span :class="[
                                'inline-block px-3 py-1 rounded-xl text-xs font-semibold',
                                product.isactive 
                                    ? 'bg-green-100 text-accent-success dark:bg-green-900/20 dark:text-accent-success' 
                                    : 'bg-red-100 text-accent-error dark:bg-red-900/20 dark:text-accent-error'
                            ]">
                                {{ product.isactive ? $t('common.active') : $t('common.inactive') }}
                            </span>
                        </td>
                        <td class="p-4 border-b border-surface-border text-text-primary">
                            <div class="flex gap-2">
                                <button @click="openWarehousesModal(product)" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/15 dark:hover:bg-green-900/25" :title="$t('product.viewWarehouses')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                        <polyline points="9 22 9 12 15 12 15 22"/>
                                    </svg>
                                </button>
                                <router-link :to="`/app/products/${product.id}/edit`" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-blue-100 text-accent-info hover:bg-blue-200 dark:bg-blue-900/15 dark:hover:bg-blue-900/25 no-underline" :title="$t('common.edit')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </router-link>
                                <button @click="handleDelete(product)" class="p-1.5 border-none rounded-md cursor-pointer transition-all duration-200 flex items-center justify-center bg-red-100 text-accent-error hover:bg-red-200 dark:bg-red-900/15 dark:hover:bg-red-900/25" :title="$t('common.delete')">
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

        <Pagination
            v-if="productStore.pagination && (productStore.pagination.last_page > 1 || perPage !== 10)"
            :current-page="productStore.pagination?.current_page || 1"
            :total-pages="productStore.pagination?.last_page || 1"
            :total-items="productStore.pagination?.total || 0"
            :per-page="perPage"
            :show-total="true"
            @page-change="changePage"
            @per-page-change="changePerPage"
        />

        <ProductWarehousesModal
            v-if="showWarehousesModal"
            :show="showWarehousesModal"
            :product="selectedProduct"
            @close="closeWarehousesModal"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useProductStore } from '@/stores/product';
import { useCategoryStore } from '@/stores/category';
import { useBrandStore } from '@/stores/brand';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';
import ProductWarehousesModal from './warehouses-modal.vue';

const productStore = useProductStore();
const categoryStore = useCategoryStore();
const brandStore = useBrandStore();
const { success, error } = useToast();
const { t } = useI18n();

const searchQuery = ref('');
const categoryFilter = ref('');
const brandFilter = ref('');
const statusFilter = ref('');
const perPage = ref(10);
const categories = ref([]);
const brands = ref([]);
const showWarehousesModal = ref(false);
const selectedProduct = ref(null);
let searchTimeout = null;

onMounted(async () => {
    // Load products
    const result = await productStore.fetchProducts({ per_page: perPage.value });
    if (!result.success) {
        console.error('Failed to fetch products:', result.error);
        error(result.error || t('product.loadFailed') || 'Failed to load products');
    } else {
        console.log('Products loaded:', productStore.products.length, 'items');
        console.log('Pagination:', productStore.pagination);
    }
    
    // Load categories and brands for filters
    await categoryStore.fetchCategories({ per_page: 100 });
    await brandStore.fetchBrands({ per_page: 100 });
    categories.value = categoryStore.categories;
    brands.value = brandStore.brands;
});


const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        productStore.fetchProducts({
            per_page: perPage.value,
            page: 1,
            search: searchQuery.value,
            category_id: categoryFilter.value || undefined,
            brand_id: brandFilter.value || undefined,
            isactive: statusFilter.value !== '' ? statusFilter.value === '1' : undefined
        });
    }, 500);
};

const handleFilter = () => {
    productStore.fetchProducts({
        per_page: perPage.value,
        page: 1,
        search: searchQuery.value,
        category_id: categoryFilter.value || undefined,
        brand_id: brandFilter.value || undefined,
        isactive: statusFilter.value !== '' ? statusFilter.value === '1' : undefined
    });
};

const changePage = (page) => {
    productStore.fetchProducts({
        per_page: perPage.value,
        page,
        search: searchQuery.value,
        category_id: categoryFilter.value || undefined,
        brand_id: brandFilter.value || undefined,
        isactive: statusFilter.value !== '' ? statusFilter.value === '1' : undefined
    });
};

const changePerPage = (newPerPage) => {
    perPage.value = newPerPage;
    productStore.fetchProducts({
        per_page: newPerPage,
        page: 1,
        search: searchQuery.value,
        category_id: categoryFilter.value || undefined,
        brand_id: brandFilter.value || undefined,
        isactive: statusFilter.value !== '' ? statusFilter.value === '1' : undefined
    });
};

const handleDelete = async (product) => {
    if (!confirm(t('product.confirmDelete', { name: product.name }))) {
        return;
    }

    const result = await productStore.deleteProduct(product.id);
    if (result.success) {
        success(t('product.deleted'));
    } else {
        error(result.error || t('product.deleteFailed'));
    }
};

const formatPrice = (price) => {
    if (!price) return '0.00';
    return parseFloat(price).toFixed(2);
};

const getPrimaryImage = (product) => {
    if (!product.images || !Array.isArray(product.images)) {
        return null;
    }
    // Find image with isprimary = true
    const primaryImage = product.images.find(img => img.isprimary === true);
    // If no primary image, return first image
    return primaryImage || (product.images.length > 0 ? product.images[0] : null);
};

const openWarehousesModal = (product) => {
    selectedProduct.value = product;
    showWarehousesModal.value = true;
};

const closeWarehousesModal = () => {
    showWarehousesModal.value = false;
    selectedProduct.value = null;
};
</script>

