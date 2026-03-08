<template>
    <div>
        <Head title="Add Adjustment" />

        <Modal ref="modalRef" max-width="xl" :close-explicitly="true" #default>
            <div class="page-header">
                <h4>Add Discount</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="row page-body">
                    <div class="col-md-12">
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
                        >Apply Discount</submit-btn
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
    expense: Object,
});
const modalRef = ref(null);
const form = useAxiosForm({});

const discountEmitterEvent = ref('discountexpense:updated');

let rowCounter = 0;

const createRow = (data = {}) => ({
    id: rowCounter++,
    name: data.label ?? '',
    amount: data.amount ?? 0,
    adjustment_type: data.method ?? 'fixed',
});

const adjustments = reactive({
    discount: [],
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

// DISCOUNT
watch(
    () => props.expense?.discount_breakdown,
    (rows) => {
        adjustments.discount.length = 0;
        if (rows && rows.length) {
            rows.forEach((row) => {
                adjustments.discount.push(
                    createRow({
                        label: row.label,
                        amount: row.value,
                        method: row.method,
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
    const breakdown = [...buildBreakdown(adjustments.discount, 'discount')];

    const discount_total = breakdown
        .filter((b) => b.type === 'discount')
        .reduce((s, b) => s + b.amount, 0);

    const adjustment_total = breakdown.reduce((s, b) => {
        return b.type === 'discount' ? s - b.amount : s + b.amount;
    }, 0);

    form.submit(
        'post',
        route('admin.expense.store.adjustment', props.expense.id),
        {
            data: {
                adjustment_breakdown: breakdown,
                discount_total,
                adjustment_total,
            },
            onSuccess: (response) => {
                alert.showSuccess(
                    response.message || 'Discount applied successfully!',
                );
                modalRef.value?.close();
                emitter.emit(discountEmitterEvent.value, {
                    expense: response.data.expense,
                });
            },
        },
    );
};
</script>
