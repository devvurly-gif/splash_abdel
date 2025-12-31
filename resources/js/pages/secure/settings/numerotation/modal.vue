<template>
    <div v-if="show" class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-[1000] p-5" @click.self="handleClose">
        <div class="bg-bg-elevated rounded-xl w-full max-w-[600px] max-h-[90vh] overflow-y-auto shadow-xl border border-surface-border">
            <div class="flex justify-between items-center p-6 border-b border-surface-border">
                <h2 class="text-xl font-semibold text-text-primary m-0">
                    {{ numberingSystem ? $t('numberingSystem.edit') : $t('numberingSystem.create') }}
                </h2>
                <button @click="handleClose" class="bg-transparent border-none text-text-secondary cursor-pointer p-1 rounded transition-all duration-200 hover:bg-surface-hover hover:text-text-primary">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="handleSubmit" class="p-6">
                <div class="mb-5">
                    <label class="block text-sm font-medium text-text-secondary mb-2" for="title">
                        {{ $t('numberingSystem.title') }} <span class="text-accent-error">*</span>
                    </label>
                    <input
                        id="title"
                        v-model="form.title"
                        type="text"
                        :placeholder="$t('numberingSystem.titlePlaceholder')"
                        required
                        class="w-full py-2.5 px-3 border-2 rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light placeholder:text-text-tertiary"
                        :class="errors.title ? 'border-accent-error focus:ring-red-200' : 'border-surface-border'"
                    />
                    <span v-if="errors.title" class="block mt-1 text-xs text-accent-error">{{ errors.title }}</span>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-text-secondary mb-2" for="domain">
                        {{ $t('numberingSystem.domain') }} <span class="text-accent-error">*</span>
                    </label>
                    <select
                        id="domain"
                        v-model="form.domain"
                        class="w-full py-2.5 px-3 border-2 rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light cursor-pointer"
                        :class="errors.domain ? 'border-accent-error focus:ring-red-200' : 'border-surface-border'"
                        required
                    >
                        <option value="">{{ $t('common.select') }}</option>
                        <option value="structure">{{ $t('numberingSystem.structure') }}</option>
                        <option value="sale">{{ $t('numberingSystem.sale') }}</option>
                        <option value="purchase">{{ $t('numberingSystem.purchase') }}</option>
                        <option value="stock">{{ $t('numberingSystem.stock') }}</option>
                    </select>
                    <span v-if="errors.domain" class="block mt-1 text-xs text-accent-error">{{ errors.domain }}</span>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-text-secondary mb-2" for="type">
                        {{ $t('numberingSystem.type') }} <span class="text-accent-error">*</span>
                    </label>
                    <input
                        id="type"
                        v-model="form.type"
                        type="text"
                        :placeholder="$t('numberingSystem.typePlaceholder')"
                        required
                        class="w-full py-2.5 px-3 border-2 rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light placeholder:text-text-tertiary"
                        :class="errors.type ? 'border-accent-error focus:ring-red-200' : 'border-surface-border'"
                    />
                    <small class="block mt-1 text-xs text-text-tertiary">{{ $t('numberingSystem.typeHint') }}</small>
                    <span v-if="errors.type" class="block mt-1 text-xs text-accent-error">{{ errors.type }}</span>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-text-secondary mb-2" for="template">
                        {{ $t('numberingSystem.template') }} <span class="text-accent-error">*</span>
                    </label>
                    <input
                        id="template"
                        v-model="form.template"
                        type="text"
                        :placeholder="$t('numberingSystem.templatePlaceholder')"
                        required
                        class="w-full py-2.5 px-3 border-2 rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light placeholder:text-text-tertiary"
                        :class="errors.template ? 'border-accent-error focus:ring-red-200' : 'border-surface-border'"
                    />
                    <small class="block mt-1 text-xs text-text-tertiary">{{ $t('numberingSystem.templateHint') }}</small>
                    <span v-if="errors.template" class="block mt-1 text-xs text-accent-error">{{ errors.template }}</span>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-text-secondary mb-2" for="next_trick">
                        {{ $t('numberingSystem.nextTrick') }}
                    </label>
                    <input
                        id="next_trick"
                        v-model.number="form.next_trick"
                        type="number"
                        min="1"
                        :placeholder="$t('numberingSystem.nextTrickPlaceholder')"
                        class="w-full py-2.5 px-3 border-2 rounded-lg text-sm bg-bg-primary text-text-primary transition-all duration-200 focus:outline-none focus:border-border-focus focus:ring-2 focus:ring-accent-primary-light placeholder:text-text-tertiary"
                        :class="errors.next_trick ? 'border-accent-error focus:ring-red-200' : 'border-surface-border'"
                    />
                    <small class="block mt-1 text-xs text-text-tertiary">{{ $t('numberingSystem.nextTrickHint') }}</small>
                    <span v-if="errors.next_trick" class="block mt-1 text-xs text-accent-error">{{ errors.next_trick }}</span>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-text-secondary mb-2" for="isActive">
                        {{ $t('numberingSystem.isActive') }}
                    </label>
                    <div class="flex items-center gap-2">
                        <input
                            id="isActive"
                            v-model="form.isActive"
                            type="checkbox"
                            class="w-[18px] h-[18px] cursor-pointer"
                        />
                        <label for="isActive" class="text-sm text-text-secondary cursor-pointer">
                            {{ $t('numberingSystem.isActiveLabel') }}
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-surface-border">
                    <button type="button" @click="handleClose" class="px-5 py-2.5 border-2 border-surface-border rounded-lg bg-bg-primary text-text-primary font-medium cursor-pointer transition-all duration-200 hover:border-border-hover hover:bg-surface-hover">
                        {{ $t('common.cancel') }}
                    </button>
                    <button type="submit" class="px-5 py-2.5 border-none rounded-lg bg-gradient-to-br from-accent-primary to-accent-secondary text-text-inverse font-semibold cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:brightness-110 disabled:opacity-60 disabled:cursor-not-allowed" :disabled="numberingSystemStore.loading">
                        <span v-if="numberingSystemStore.loading">{{ $t('common.saving') }}...</span>
                        <span v-else>{{ numberingSystem ? $t('common.update') : $t('common.create') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useNumberingSystemStore } from '@/stores/numberingSystem';
import { useToast } from '@/composables/useToast';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    numberingSystem: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'saved']);

const numberingSystemStore = useNumberingSystemStore();
const { success, error } = useToast();
const { t } = useI18n();

const form = ref({
    title: '',
    domain: '',
    type: '',
    template: '',
    next_trick: 1,
    isActive: true
});

const errors = ref({});

watch(() => props.numberingSystem, (newNumberingSystem) => {
    if (newNumberingSystem) {
        form.value = {
            title: newNumberingSystem.title || '',
            domain: newNumberingSystem.domain || '',
            type: newNumberingSystem.type || '',
            template: newNumberingSystem.template || '',
            next_trick: newNumberingSystem.next_trick || 1,
            isActive: newNumberingSystem.isActive !== undefined ? newNumberingSystem.isActive : true
        };
    } else {
        form.value = {
            title: '',
            domain: '',
            type: '',
            template: '',
            next_trick: 1,
            isActive: true
        };
    }
    errors.value = {};
}, { immediate: true });

watch(() => props.show, (isShow) => {
    if (isShow && props.numberingSystem) {
        form.value = {
            title: props.numberingSystem.title || '',
            domain: props.numberingSystem.domain || '',
            type: props.numberingSystem.type || '',
            template: props.numberingSystem.template || '',
            next_trick: props.numberingSystem.next_trick || 1,
            isActive: props.numberingSystem.isActive !== undefined ? props.numberingSystem.isActive : true
        };
    } else if (isShow && !props.numberingSystem) {
        form.value = {
            title: '',
            domain: '',
            type: '',
            template: '',
            next_trick: 1,
            isActive: true
        };
    }
    errors.value = {};
});

const handleClose = () => {
    emit('close');
    form.value = {
        title: '',
        domain: '',
        type: '',
        template: '',
        next_trick: 1,
        isActive: true
    };
    errors.value = {};
};

const handleSubmit = async () => {
    errors.value = {};

    // Validation
    if (!form.value.title.trim()) {
        errors.value.title = t('numberingSystem.titleRequired');
        return;
    }

    if (!form.value.domain) {
        errors.value.domain = t('numberingSystem.domainRequired');
        return;
    }

    if (!form.value.type.trim()) {
        errors.value.type = t('numberingSystem.typeRequired');
        return;
    }

    if (!form.value.template.trim()) {
        errors.value.template = t('numberingSystem.templateRequired');
        return;
    }

    if (form.value.next_trick && form.value.next_trick < 1) {
        errors.value.next_trick = t('numberingSystem.nextTrickMin');
        return;
    }

    const numberingSystemData = {
        title: form.value.title.trim(),
        domain: form.value.domain,
        type: form.value.type.trim(),
        template: form.value.template.trim(),
        next_trick: form.value.next_trick || 1,
        isActive: form.value.isActive
    };

    let result;
    if (props.numberingSystem) {
        result = await numberingSystemStore.updateNumberingSystem(props.numberingSystem.id, numberingSystemData);
    } else {
        result = await numberingSystemStore.createNumberingSystem(numberingSystemData);
    }

    if (result.success) {
        success(props.numberingSystem ? t('numberingSystem.updated') : t('numberingSystem.created'));
        emit('saved');
    } else {
        error(result.error || (props.numberingSystem ? t('numberingSystem.updateFailed') : t('numberingSystem.createFailed')));
        if (result.errors) {
            errors.value = result.errors;
        }
    }
};
</script>

