<template>
    <Head title="Receive Payment" />

    <Modal
        ref="modalRef"
        max-width="sm"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Receive Payment</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body">
                <div class="mb-3">
                    <label class="form-label required">Payment Status</label>
                    <div class="d-grid gap-2 mt-2">
                        <button
                            v-for="opt in paymentStatusOptions"
                            :key="opt.value"
                            type="button"
                            class="btn"
                            :class="
                                form.data.payment_status === opt.value
                                    ? `btn-${opt.color}`
                                    : `btn-outline-${opt.color}`
                            "
                            @click="form.data.payment_status = opt.value"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                    <input-error :message="form.errors.payment_status" />
                </div>
            </div>

            <div class="page-footer-buttons">
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing">Save</submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import * as alert from '@/helpers/alert';
import { Head, router } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

const props = defineProps({
    order: Object,
});

const modalRef = useTemplateRef('modalRef');

const paymentStatusOptions = [
    { value: 'unpaid', label: 'Unpaid', color: 'warning' },
    { value: 'partially_paid', label: 'Partially Paid', color: 'info' },
    { value: 'paid', label: 'Paid', color: 'success' },
    { value: 'cancelled', label: 'Cancelled', color: 'danger' },
];

const form = useAxiosForm({
    payment_status: props.order.payment_status ?? 'unpaid',
});

const submitForm = () => {
    form.post(
        route('brand-partner.orders.receive-payment.store', props.order.id),
        {
            onSuccess: () => {
                alert.showSuccess('Payment status updated successfully.');
                modalRef.value.close();
                router.reload();
            },
        },
    );
};
</script>
