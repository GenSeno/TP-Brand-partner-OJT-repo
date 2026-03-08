<template>
    <div>
        <Head title="Add Adjustment" />

        <Modal ref="modalRef" max-width="xl" :close-explicitly="true" #default>
            <div class="page-header">
                <h4>Add Adjustment</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="row page-body">
                    <div class="col-md-12">
                        <!-- SHIPPING -->
                        <AdjustmentSection
                            title="Shipping"
                            type="shipping"
                            :rows="adjustments.shipping"
                            @add="addRow"
                            @remove="removeRow"
                            :showTypeDropdown="false"
                        />

                        <!-- TAX -->
                        <AdjustmentSection
                            title="Tax"
                            type="tax"
                            :rows="adjustments.tax"
                            @add="addRow"
                            @remove="removeRow"
                            :showTypeDropdown="true"
                        />

                        <!-- DISCOUNT -->
                        <AdjustmentSection
                            title="Discount"
                            type="discount"
                            :rows="adjustments.discount"
                            @add="addRow"
                            @remove="removeRow"
                            :showTypeDropdown="true"
                        />
                    </div>
                </div>

                <div class="page-footer-buttons mt-3 text-end">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="modalRef.close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing"
                        >Apply Adjustment</submit-btn
                    >
                </div>
            </form>
        </Modal>
    </div>
</template>
<script setup>
import { ref, reactive, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useAxiosForm } from '@/composables/axiosForm';
import * as alert from '@/helpers/alert';
import AdjustmentSection from './adj_template.vue';
import { emitter } from '@/composables/eventBus';

const props = defineProps({
    quotation: Object,
});
const modalRef = ref(null);
const form = useAxiosForm({});

let rowCounter = 0;

const createRow = (data = {}) => ({
    id: rowCounter++,
    name: data.label ?? '',
    amount: data.amount ?? 0,
    adjustment_type: data.method ?? 'fixed',
});

const adjustments = reactive({
    shipping: [],
    tax: [],
    discount: [],
    other: [],
});

const addRow = (type) => {
    if (!adjustments[type]) return;
    adjustments[type].push(createRow());
};

const removeRow = (type, index) => {
    if (!adjustments[type]) return;
    if (adjustments[type].length === 1) return;
    adjustments[type].splice(index, 1);
};

const slugify = (text = '') =>
    text
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^a-z0-9-]/g, '');

const buildBreakdown = (rows, type) =>
    rows
        .filter((r) => r.name && r.amount > 0)
        .map((r) => ({
            label: r.name,
            value: slugify(r.name),
            amount: Number(r.amount),
            method: r.adjustment_type,
            type,
        }));

const normalizeBreakdown = (raw) => {
    if (!raw) return [];

    try {
        if (typeof raw === 'string') return JSON.parse(raw);
        if (Array.isArray(raw)) return raw;
        if (typeof raw === 'object') return raw;
    } catch (e) {
        console.error('Breakdown parse failed', e, raw);
    }

    return [];
};

watch(
    () => props.quotation?.shipping_breakdown,
    (raw) => {
        adjustments.shipping.length = 0;

        const rows = normalizeBreakdown(raw);

        if (rows.length) {
            rows.forEach((row) => {
                adjustments.shipping.push(
                    createRow({
                        label: row.name,
                        amount: Number(row?.formatted?.replace(/[^\d.-]/g, '')),
                    }),
                );
            });
        } else {
            adjustments.shipping.push(createRow());
        }
    },
    { immediate: true },
);

watch(
    () => props.quotation?.tax_breakdown,
    (raw) => {
        adjustments.tax.length = 0;

        const rows = normalizeBreakdown(raw);

        if (rows.length) {
            rows.forEach((row) => {
                adjustments.tax.push(
                    createRow({
                        label: row.identifier,
                        amount:
                            row.percentage === 0
                                ? Number(
                                      row?.description?.replace(/[^\d.-]/g, ''),
                                  )
                                : (row.percentage ?? 0),
                        method: row.percentage === 0 ? 'fixed' : 'percentage',
                    }),
                );
            });
        } else {
            adjustments.tax.push(createRow());
        }
    },
    { immediate: true },
);

// DISCOUNT
watch(
    () => props.quotation?.discount_breakdown?.amounts,
    (rows) => {
        adjustments.discount.length = 0;
        if (rows && rows.length) {
            rows.forEach((row) => {
                adjustments.discount.push(
                    createRow({
                        label: row.description,
                        amount:
                            row.type === 'percentage'
                                ? (row.percentage ?? 0)
                                : (row.amount ?? 0),
                        method: row.type,
                    }),
                );
            });
            return;
        }
        adjustments.discount.push(createRow());
    },
    { immediate: true },
);

const submitForm = () => {
    const breakdown = [
        ...buildBreakdown(adjustments.shipping, 'shipping'),
        ...buildBreakdown(adjustments.tax, 'tax'),
        ...buildBreakdown(adjustments.discount, 'discount'),
    ];

    const discount_total = breakdown
        .filter((b) => b.type === 'discount')
        .reduce((s, b) => s + b.amount, 0);

    const adjustment_total = breakdown.reduce((s, b) => {
        return b.type === 'discount' ? s - b.amount : s + b.amount;
    }, 0);

    form.submit(
        'post',
        route('admin.quotation.store.adjustment', props.quotation.id),
        {
            data: {
                adjustment_breakdown: breakdown,
                discount_total,
                adjustment_total,
            },
            onSuccess: () => {
                alert.showSuccess('Adjustment applied successfully.');
                emitter.emit('adjustment:updated', props.quotation.id);
                modalRef.value?.close();
            },
        },
    );
};
</script>
