<template>
    <div>
        <Head title="Edit Payment Voucher" />

        <Modal
            ref="modalRef"
            max-width="md"
            :close-explicitly="true"
            #default="{ close }"
        >
            <div class="page-header">
                <h4>Edit Payment Voucher</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="page-body new-employee-field">
                    <!-- Expense Date -->
                    <div class="mb-3">
                        <label class="form-label required">Expense Date</label>
                        <VueDatePicker
                            v-model="form.data.expense_date"
                            placeholder="Select Date"
                            :time-config="{ enableTimePicker: false }"
                            :ui="{
                                input: form.errors.expense_date
                                    ? 'border-danger'
                                    : '',
                            }"
                            auto-apply
                        />
                        <input-error :message="form.errors.expense_date" />
                    </div>

                    <!-- Supplier -->
                    <div class="mb-3">
                        <label class="form-label required">Supplier</label>
                        <vue-select
                            v-model="form.data.supplier_id"
                            :options="suppliers"
                            :reduce="(o) => o.id"
                            label="name"
                            placeholder="Select supplier"
                        />
                        <input-error :message="form.errors.supplier_id" />
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea
                            v-model="form.data.description"
                            rows="2"
                            class="form-control"
                            placeholder="Enter description"
                        ></textarea>
                        <input-error :message="form.errors.description" />
                    </div>

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
                    <div class="mb-3">
                        <label class="form-label">Payment Reference </label>
                        <input-text v-model="form.data.reference_no" autofocus />
                        <input-error :message="form.errors.reference_no" />
                    </div>
                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label required">Status</label>
                        <select v-model="form.data.status" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <input-error :message="form.errors.status" />
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

                    <submit-btn :loading="form.processing">
                        Update Voucher
                    </submit-btn>
                </div>
            </form>
        </Modal>
    </div>
</template>
<script setup>
import { ref } from 'vue';
import dayjs from 'dayjs';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import { Head } from '@inertiajs/vue3';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { useTemplateRef } from 'vue';

const props = defineProps({
    expense: Object,
    suppliers: Array,
    paymentMethods: Array,
});
const updateExpenseEmitterEvent = ref('expense:updated');
const expsenseHeaderEmitterEvent = ref('expenseheader:updated');
const modalRef = useTemplateRef('modalRef');

/**
 * Form (pre-filled)
 */
const form = useAxiosForm({
    expense_date: props.expense.expense_date
        ? new Date(props.expense.expense_date)
        : new Date(),

    supplier_id: props.expense.supplier_id,
    description: props.expense.description,

    payment_date: props.expense.payment_date
        ? new Date(props.expense.payment_date)
        : null,

    payment_method: props.expense.payment_method || '',
    reference_no: props.expense.reference_no,

    status: props.expense.status,
});

/**
 * Submit
 */
const submitForm = () => {
    form.submit('post', route('admin.expense.update', props.expense.id), {
        data: {
            ...form.data,
            expense_date: dayjs(form.data.expense_date).format(
                'YYYY-MM-DD HH:mm:ss',
            ),

            payment_date: form.data.payment_date
                ? dayjs(form.data.payment_date).format('YYYY-MM-DD')
                : null,
        },
        onSuccess: (response) => {
            alert.showSuccess(
                response.data.message ||
                    'Payment voucher updated successfully.',
            );
            modalRef.value?.close();
            emitter.emit(
                updateExpenseEmitterEvent.value,
                response.data.expense,
            );
            emitter.emit(
                expsenseHeaderEmitterEvent.value,
                response.data.expense,
            );
        },
    });
};
</script>
