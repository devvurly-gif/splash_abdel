<template>
    <div v-if="show" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-[1000] p-5" @click.self="handleClose">
        <div class="bg-bg-elevated rounded-xl w-full max-w-6xl max-h-[90vh] overflow-y-auto shadow-xl border border-surface-border">
            <div class="flex justify-between items-center p-6 border-b border-surface-border sticky top-0 bg-bg-elevated z-10">
                <h2 class="text-xl font-semibold text-text-primary m-0">
                    {{ document ? $t('document.purchase.edit') : $t('document.purchase.create') }} - {{ $t(`document.purchase.${type}`) }}
                </h2>
                <button @click="handleClose" class="bg-transparent border-none text-text-secondary cursor-pointer p-1 rounded transition-all duration-200 hover:bg-surface-hover hover:text-text-primary">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="handleSubmit" class="p-6">
                <!-- Document Header -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">
                            {{ $t('document.partner') }} <span class="text-accent-error">*</span>
                        </label>
                        <select
                            v-model="form.partner_id"
                            required
                            class="w-full py-2.5 px-3 border-2 border-surface-border rounded-lg text-sm bg-bg-primary text-text-primary cursor-pointer transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light"
                        >
                            <option value="">{{ $t('document.selectPartner') }}</option>
                            <option v-for="partner in suppliers" :key="partner.id" :value="partner.id">
                                {{ partner.name }} ({{ partner.code }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">
                            {{ $t('document.warehouse') }} <span class="text-accent-error">*</span>
                        </label>
                        <select
                            v-model="form.warehouse_id"
                            required
                            class="w-full py-2.5 px-3 border-2 border-surface-border rounded-lg text-sm bg-bg-primary text-text-primary cursor-pointer transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light"
                        >
                            <option value="">{{ $t('document.selectWarehouse') }}</option>
                            <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
                                {{ warehouse.title }} ({{ warehouse.code }})
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">
                            {{ $t('document.date') }} <span class="text-accent-error">*</span>
                        </label>
                        <input
                            v-model="form.document_date"
                            type="date"
                            required
                            class="w-full py-2.5 px-3 border-2 border-surface-border rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">
                            {{ $t('document.dueDate') }}
                        </label>
                        <input
                            v-model="form.due_date"
                            type="date"
                            class="w-full py-2.5 px-3 border-2 border-surface-border rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">
                            {{ $t('document.reference') }}
                        </label>
                        <input
                            v-model="form.reference"
                            type="text"
                            class="w-full py-2.5 px-3 border-2 border-surface-border rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light"
                        />
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-text-secondary mb-2">
                        {{ $t('document.notes') }}
                    </label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        class="w-full py-2.5 px-3 border-2 border-surface-border rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light resize-none"
                    ></textarea>
                </div>

                <!-- Document Lines -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-text-primary">{{ $t('document.lines') }}</h3>
                        <button type="button" @click="addLine" class="px-4 py-2 bg-accent-primary text-text-inverse rounded-lg text-sm font-medium hover:bg-accent-primary-dark transition-colors">
                            {{ $t('document.addLine') }}
                        </button>
                    </div>

                    <div class="bg-bg-secondary rounded-lg overflow-hidden">
                        <table class="w-full border-collapse">
                            <thead class="bg-bg-tertiary">
                                <tr>
                                    <th class="p-3 text-left text-xs font-semibold text-text-secondary">{{ $t('document.product') }}</th>
                                    <th class="p-3 text-left text-xs font-semibold text-text-secondary">{{ $t('document.quantity') }}</th>
                                    <th class="p-3 text-left text-xs font-semibold text-text-secondary">{{ $t('document.unitPrice') }}</th>
                                    <th class="p-3 text-left text-xs font-semibold text-text-secondary">{{ $t('document.discount') }} %</th>
                                    <th class="p-3 text-left text-xs font-semibold text-text-secondary">{{ $t('document.tax') }} %</th>
                                    <th class="p-3 text-left text-xs font-semibold text-text-secondary">{{ $t('document.total') }}</th>
                                    <th class="p-3 text-left text-xs font-semibold text-text-secondary">{{ $t('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(line, index) in form.lines" :key="index" class="border-b border-surface-border">
                                    <td class="p-3">
                                        <select
                                            v-model="line.product_id"
                                            @change="onProductChange(index)"
                                            required
                                            class="w-full py-2 px-2 border border-surface-border rounded text-sm bg-bg-primary text-text-primary"
                                        >
                                            <option value="">{{ $t('document.selectProduct') }}</option>
                                            <option v-for="product in products" :key="product.id" :value="product.id">
                                                {{ product.name }} ({{ product.code }})
                                            </option>
                                        </select>
                                    </td>
                                    <td class="p-3">
                                        <input
                                            v-model.number="line.quantity"
                                            type="number"
                                            step="0.001"
                                            min="0.001"
                                            @input="calculateLineTotal(index)"
                                            required
                                            class="w-full py-2 px-2 border border-surface-border rounded text-sm bg-bg-primary text-text-primary"
                                        />
                                    </td>
                                    <td class="p-3">
                                        <input
                                            v-model.number="line.unit_price"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            @input="calculateLineTotal(index)"
                                            required
                                            class="w-full py-2 px-2 border border-surface-border rounded text-sm bg-bg-primary text-text-primary"
                                        />
                                    </td>
                                    <td class="p-3">
                                        <input
                                            v-model.number="line.discount_percent"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="100"
                                            @input="calculateLineTotal(index)"
                                            class="w-full py-2 px-2 border border-surface-border rounded text-sm bg-bg-primary text-text-primary"
                                        />
                                    </td>
                                    <td class="p-3">
                                        <input
                                            v-model.number="line.tax_percent"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="100"
                                            @input="calculateLineTotal(index)"
                                            class="w-full py-2 px-2 border border-surface-border rounded text-sm bg-bg-primary text-text-primary"
                                        />
                                    </td>
                                    <td class="p-3 font-semibold">{{ formatCurrency(calculateLineTotal(index)) }}</td>
                                    <td class="p-3">
                                        <button type="button" @click="removeLine(index)" class="p-1.5 text-accent-error hover:bg-red-100 rounded">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totals -->
                <div class="flex justify-end mb-6">
                    <div class="w-full max-w-md space-y-2">
                        <div class="flex justify-between text-text-secondary">
                            <span>{{ $t('document.subtotal') }}:</span>
                            <span class="font-semibold">{{ formatCurrency(totals.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-text-secondary">
                            <span>{{ $t('document.discount') }}:</span>
                            <span class="font-semibold">{{ formatCurrency(totals.discount) }}</span>
                        </div>
                        <div class="flex justify-between text-text-secondary">
                            <span>{{ $t('document.tax') }}:</span>
                            <span class="font-semibold">{{ formatCurrency(totals.tax) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-text-primary pt-2 border-t border-surface-border">
                            <span>{{ $t('document.total') }}:</span>
                            <span>{{ formatCurrency(totals.total) }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-surface-border">
                    <button type="button" @click="handleClose" class="px-5 py-2.5 border-2 border-surface-border rounded-lg bg-bg-primary text-text-primary font-medium cursor-pointer transition-all duration-200 hover:border-border-hover hover:bg-surface-hover">
                        {{ $t('common.cancel') }}
                    </button>
                    <button type="submit" class="px-5 py-2.5 border-none rounded-lg bg-gradient-to-br from-accent-primary to-accent-secondary text-text-inverse font-semibold cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:brightness-110 disabled:opacity-60 disabled:cursor-not-allowed" :disabled="documentStore.loading">
                        <span v-if="documentStore.loading">{{ $t('common.saving') }}...</span>
                        <span v-else>{{ document ? $t('common.update') : $t('common.create') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useDocumentStore } from '@/stores/document';
import { usePartnerStore } from '@/stores/partner';
import { useWarehouseStore } from '@/stores/warehouse';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    document: {
        type: Object,
        default: null
    },
    type: {
        type: String,
        default: 'invoice'
    }
});

const emit = defineEmits(['close', 'saved']);

const documentStore = useDocumentStore();
const partnerStore = usePartnerStore();
const warehouseStore = useWarehouseStore();
const { success, error } = useToast();
const { t } = useI18n();

const form = ref({
    partner_id: null,
    warehouse_id: null,
    document_date: new Date().toISOString().split('T')[0],
    due_date: null,
    reference: '',
    notes: '',
    lines: []
});

const products = ref([]); // TODO: Load from product store
const suppliers = ref([]);
const warehouses = ref([]);

const totals = computed(() => {
    let subtotal = 0;
    let discount = 0;
    let tax = 0;

    form.value.lines.forEach(line => {
        const lineSubtotal = (line.quantity || 0) * (line.unit_price || 0);
        const lineDiscount = lineSubtotal * ((line.discount_percent || 0) / 100);
        const afterDiscount = lineSubtotal - lineDiscount;
        const lineTax = afterDiscount * ((line.tax_percent || 0) / 100);

        subtotal += lineSubtotal;
        discount += lineDiscount;
        tax += lineTax;
    });

    return {
        subtotal,
        discount,
        tax,
        total: subtotal - discount + tax
    };
});

onMounted(async () => {
    // Load suppliers (partners with type supplier or both)
    await partnerStore.fetchPartners({ type: 'supplier', status: 1 });
    suppliers.value = partnerStore.partners.filter(p => p.isSupplier || p.type === 'both');

    // Load warehouses
    await warehouseStore.fetchWarehouses({ status: 1 });
    warehouses.value = warehouseStore.warehouses;

    // TODO: Load products
});

watch(() => props.document, (newDoc) => {
    if (newDoc) {
        form.value = {
            partner_id: newDoc.partner_id,
            warehouse_id: newDoc.warehouse_id,
            document_date: newDoc.document_date || new Date().toISOString().split('T')[0],
            due_date: newDoc.due_date || null,
            reference: newDoc.reference || '',
            notes: newDoc.notes || '',
            lines: newDoc.lines ? newDoc.lines.map(l => ({
                product_id: l.product_id,
                quantity: l.quantity,
                unit_price: l.unit_price,
                unit_cost: l.unit_cost || 0,
                discount_percent: l.discount_percent || 0,
                tax_percent: l.tax_percent || 0,
                description: l.description || '',
                notes: l.notes || ''
            })) : []
        };
    } else {
        form.value = {
            partner_id: null,
            warehouse_id: null,
            document_date: new Date().toISOString().split('T')[0],
            due_date: null,
            reference: '',
            notes: '',
            lines: []
        };
    }
}, { immediate: true });

const addLine = () => {
    form.value.lines.push({
        product_id: null,
        quantity: 1,
        unit_price: 0,
        unit_cost: 0,
        discount_percent: 0,
        tax_percent: 0,
        description: '',
        notes: ''
    });
};

const removeLine = (index) => {
    form.value.lines.splice(index, 1);
};

const onProductChange = (index) => {
    // TODO: Load product details and set default price
    const product = products.value.find(p => p.id === form.value.lines[index].product_id);
    if (product) {
        form.value.lines[index].unit_price = product.sale_price || 0;
        form.value.lines[index].unit_cost = product.purchase_price || 0;
    }
};

const calculateLineTotal = (index) => {
    const line = form.value.lines[index];
    if (!line) return 0;

    const lineSubtotal = (line.quantity || 0) * (line.unit_price || 0);
    const lineDiscount = lineSubtotal * ((line.discount_percent || 0) / 100);
    const afterDiscount = lineSubtotal - lineDiscount;
    const lineTax = afterDiscount * ((line.tax_percent || 0) / 100);

    return afterDiscount + lineTax;
};

const handleClose = () => {
    emit('close');
};

const handleSubmit = async () => {
    if (form.value.lines.length === 0) {
        error(t('document.noLines'));
        return;
    }

    const documentData = {
        domain: 'purchase',
        type: props.type,
        partner_id: form.value.partner_id,
        warehouse_id: form.value.warehouse_id,
        document_date: form.value.document_date,
        due_date: form.value.due_date || null,
        reference: form.value.reference || null,
        notes: form.value.notes || null,
        lines: form.value.lines.map(line => ({
            product_id: line.product_id,
            quantity: line.quantity,
            unit_price: line.unit_price,
            unit_cost: line.unit_cost || 0,
            discount_percent: line.discount_percent || 0,
            tax_percent: line.tax_percent || 0,
            description: line.description || null,
            notes: line.notes || null
        }))
    };

    let result;
    if (props.document) {
        result = await documentStore.updateDocument(props.document.id, documentData);
    } else {
        result = await documentStore.createDocument(documentData);
    }

    if (result.success) {
        success(props.document ? t('document.updated') : t('document.created'));
        emit('saved');
    } else {
        error(result.error || (props.document ? t('document.updateFailed') : t('document.createFailed')));
    }
};

const formatCurrency = (amount) => {
    if (!amount) return '0.00 €';
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);
};
</script>

