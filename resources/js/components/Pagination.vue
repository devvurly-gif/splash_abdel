<template>
    <div v-if="safeTotalPages > 0 && safeCurrentPage > 0" class="flex items-center justify-between gap-4 px-4 py-3 border-t border-surface-border bg-bg-elevated">
        <!-- Left side: Page info and per-page selector -->
        <div class="flex items-center gap-4 text-sm text-text-secondary">
            <div class="flex items-center gap-2">
                <span>{{ $t('common.page') }} {{ safeCurrentPage }} {{ $t('common.of') }} {{ safeTotalPages }}</span>
                <span v-if="showTotal" class="text-text-tertiary">
                    ({{ safeTotalItems }} {{ safeTotalItems === 1 ? 'item' : 'items' }})
                </span>
            </div>
            <div v-if="showPerPage" class="flex items-center gap-2">
                <label class="text-text-secondary whitespace-nowrap">{{ $t('common.perPage') || 'Per page' }}:</label>
                <select
                    :value="safePerPage"
                    @change="handlePerPageChange"
                    class="py-1.5 px-3 border border-surface-border rounded-md text-sm cursor-pointer bg-bg-primary text-text-primary focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <!-- Right side: Pagination controls -->
        <div class="flex items-center gap-2">
            <!-- Previous button -->
            <button
                @click="goToPage(safeCurrentPage - 1)"
                :disabled="safeCurrentPage === 1"
                :class="[
                    'px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200',
                    safeCurrentPage === 1
                        ? 'bg-surface-disabled text-text-tertiary cursor-not-allowed'
                        : 'bg-surface-default text-text-primary hover:bg-surface-hover hover:text-accent-primary'
                ]"
            >
                {{ $t('common.previous') }}
            </button>

            <!-- Page numbers -->
            <div v-if="showPageNumbers" class="flex items-center gap-1">
                <!-- First page -->
                <button
                    v-if="showFirstLast && startPage > 1"
                    @click="goToPage(1)"
                    :class="[
                        'min-w-[2.5rem] h-10 px-3 text-sm font-medium rounded-md transition-all duration-200',
                        safeCurrentPage === 1
                            ? 'bg-accent-primary text-white'
                            : 'bg-surface-default text-text-primary hover:bg-surface-hover hover:text-accent-primary'
                    ]"
                >
                    1
                </button>

                <!-- Ellipsis before -->
                <span v-if="showFirstLast && startPage > 2" class="px-2 text-text-tertiary">...</span>

                <!-- Page number buttons -->
                <button
                    v-for="page in visiblePages"
                    :key="page"
                    @click="goToPage(page)"
                    :class="[
                        'min-w-[2.5rem] h-10 px-3 text-sm font-medium rounded-md transition-all duration-200',
                        safeCurrentPage === page
                            ? 'bg-accent-primary text-white'
                            : 'bg-surface-default text-text-primary hover:bg-surface-hover hover:text-accent-primary'
                    ]"
                >
                    {{ page }}
                </button>

                <!-- Ellipsis after -->
                <span v-if="showFirstLast && endPage < safeTotalPages - 1" class="px-2 text-text-tertiary">...</span>

                <!-- Last page -->
                <button
                    v-if="showFirstLast && endPage < safeTotalPages"
                    @click="goToPage(safeTotalPages)"
                    :class="[
                        'min-w-[2.5rem] h-10 px-3 text-sm font-medium rounded-md transition-all duration-200',
                        safeCurrentPage === safeTotalPages
                            ? 'bg-accent-primary text-white'
                            : 'bg-surface-default text-text-primary hover:bg-surface-hover hover:text-accent-primary'
                    ]"
                >
                    {{ safeTotalPages }}
                </button>
            </div>

            <!-- Next button -->
            <button
                @click="goToPage(safeCurrentPage + 1)"
                :disabled="safeCurrentPage === safeTotalPages"
                :class="[
                    'px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200',
                    safeCurrentPage === safeTotalPages
                        ? 'bg-surface-disabled text-text-tertiary cursor-not-allowed'
                        : 'bg-surface-default text-text-primary hover:bg-surface-hover hover:text-accent-primary'
                ]"
            >
                {{ $t('common.next') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
        default: 1
    },
    totalPages: {
        type: Number,
        required: true,
        default: 1
    },
    totalItems: {
        type: Number,
        default: 0
    },
    perPage: {
        type: Number,
        default: 10
    },
    showTotal: {
        type: Boolean,
        default: false
    },
    showPerPage: {
        type: Boolean,
        default: true
    },
    showPageNumbers: {
        type: Boolean,
        default: true
    },
    showFirstLast: {
        type: Boolean,
        default: true
    },
    maxVisiblePages: {
        type: Number,
        default: 5
    }
});

const emit = defineEmits(['page-change', 'per-page-change']);

// Safe computed values with defaults
const safeCurrentPage = computed(() => props.currentPage || 1);
const safeTotalPages = computed(() => props.totalPages || 1);
const safeTotalItems = computed(() => props.totalItems || 0);
const safePerPage = computed(() => props.perPage || 10);

const handlePerPageChange = (event) => {
    const newPerPage = parseInt(event.target.value, 10);
    if (newPerPage !== safePerPage.value) {
        emit('per-page-change', newPerPage);
    }
};

const startPage = computed(() => {
    const half = Math.floor(props.maxVisiblePages / 2);
    let start = safeCurrentPage.value - half;
    
    if (start < 1) {
        start = 1;
    }
    
    if (start + props.maxVisiblePages - 1 > safeTotalPages.value) {
        start = Math.max(1, safeTotalPages.value - props.maxVisiblePages + 1);
    }
    
    return start;
});

const endPage = computed(() => {
    return Math.min(startPage.value + props.maxVisiblePages - 1, safeTotalPages.value);
});

const visiblePages = computed(() => {
    const pages = [];
    for (let i = startPage.value; i <= endPage.value; i++) {
        pages.push(i);
    }
    return pages;
});

const goToPage = (page) => {
    if (page >= 1 && page <= safeTotalPages.value && page !== safeCurrentPage.value) {
        emit('page-change', page);
    }
};
</script>

<style scoped>
/* Additional styles if needed */
</style>

