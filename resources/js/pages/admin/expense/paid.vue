<template>
    <div>
        <Head title="Payment Information" />

        <Modal
            ref="modalRef"
            max-width="md"
            :close-explicitly="true"
            #default="{ close }"
        >
            <div class="page-header">
                <h4>Payment Information</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="page-body new-employee-field">
                    <!-- Payment Date -->
                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <VueDatePicker
                            v-model="form.data.payment_date"
                            placeholder="Select Payment Date"
                            :time-config="{ enableTimePicker: false }"
                            :ui="{
                                input: form.errors.payment_date
                                    ? 'border-danger'
                                    : '',
                            }"
                            auto-apply
                        />
                        <input-error :message="form.errors.payment_date" />
                    </div>

                    <!-- Mode of Payment -->
                    <div class="mb-3">
                        <label class="form-label">Mode of Payment</label>
                        <select
                            v-model="form.data.payment_method"
                            class="form-select"
                        >
                            <option value="" disabled>
                                Select mode of payment
                            </option>
                            <option
                                v-for="method in paymentMethods"
                                :key="method.value"
                                :value="method.value"
                            >
                                {{ method.label }}
                            </option>
                        </select>
                        <input-error :message="form.errors.payment_method" />
                    </div>

                    <!-- Payment Reference No -->
                    <div class="mb-3">
                        <label class="form-label">Payment Reference No</label>
                        <input
                            type="text"
                            v-model="form.data.reference_no"
                            class="form-control"
                            placeholder="Enter reference number"
                        />
                        <input-error :message="form.errors.reference_no" />
                    </div>
                </div>

                <!-- Footer -->
                <div class="page-footer-buttons">
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close"
                    >
                        Cancel
                    </button>

                    <submit-btn :loading="form.processing"> Submit </submit-btn>
                </div>
            </form>
        </Modal>
    </div>
</template>
<script setup>
import dayjs from 'dayjs';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import { Head } from '@inertiajs/vue3';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { useTemplateRef, ref } from 'vue';

const props = defineProps({
    expense: Object,
    paymentMethods: Array,
});

const modalRef = useTemplateRef('modalRef');
const updateExpenseEmitterEvent = ref('expense:updated');
/**
 * Form (pre-filled)
 */
const form = useAxiosForm({
    payment_date: props.expense.payment_date
        ? new Date(props.expense.payment_date)
        : null,

    payment_method: props.expense.payment_method || '',
    reference_no: props.expense.reference_no,
    status: 'paid',
});

/**
 * Submit
 */
const submitForm = () => {
    form.submit('post', route('admin.expense.markAsPaid', props.expense.id), {
        data: {
            ...form.data,
            payment_date: form.data.payment_date
                ? dayjs(form.data.payment_date).format('YYYY-MM-DD')
                : null,
        },
        onSuccess: (response) => {
            alert.showSuccess(
                response.data.message || 'Payment voucher paid successfully.',
            );
            modalRef.value?.close();
            emitter.emit('expenseheader:updated', response.data.expense);
            emitter.emit(
                updateExpenseEmitterEvent.value,
                response.data.expense,
            );
        },
    });
};
</script>
