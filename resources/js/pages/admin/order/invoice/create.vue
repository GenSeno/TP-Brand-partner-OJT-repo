<template>
    <Head title="Create Billing Statement" />

    <Modal ref="modalRef" max-width="md" v-slot="{ close }">
        <div class="page-header">
            <h4>
                Create Billing Statement{{
                    isInstallment ? ' - Installment' : ''
                }}
            </h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <div class="mb-3">
                    <label class="form-label required">Amount Unbilled</label>
                    <input
                        type="text"
                        class="form-control"
                        :value="amountUnbilled.formatted"
                        disabled
                    />
                </div>
                <div class="mb-3">
                    <label class="form-label required">Date</label>
                    <VueDatePicker
                        v-model="form.invoiced_at"
                        placeholder="Select Date"
                        :formats="{
                            month: 'MMMM',
                        }"
                        :time-config="{
                            enableTimePicker: false,
                        }"
                        :ui="{
                            input: classMerge([
                                'form-control',
                                {
                                    'is-invalid': form.errors.invoiced_at,
                                },
                            ]),
                        }"
                        @update:model-value="form.clearErrors('invoiced_at')"
                        auto-apply
                    />
                    <input-error :message="form.errors.invoiced_at" />
                </div>
                <div class="mb-3">
                    <label class="form-label required">Due Date</label>
                    <VueDatePicker
                        v-model="form.due_at"
                        placeholder="Select Date"
                        :formats="{
                            month: 'MMMM',
                        }"
                        :time-config="{
                            enableTimePicker: false,
                        }"
                        :ui="{
                            input: classMerge([
                                'form-control',
                                {
                                    'is-invalid': form.errors.due_at,
                                },
                            ]),
                        }"
                        @update:model-value="form.clearErrors('due_at')"
                        auto-apply
                    />
                    <input-error :message="form.errors.due_at" />
                </div>
                <div v-if="!isInstallment" class="mb-3">
                    <label class="form-label required">Type</label>
                    <vue-select
                        v-model="form.type"
                        :options="types"
                        :reduce="(option) => option.value"
                        label="label"
                        placeholder="Select type"
                    />
                    <input-error :message="form.errors.type" />
                </div>
                <div v-show="!isFullPayment" class="mb-3">
                    <label class="form-label required">Amount</label>
                    <input-text
                        type="number"
                        v-model="form.amount_due"
                        step="0.01"
                    />
                    <input-error :message="form.errors.amount_due" />
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
                    <submit-btn :loading="form.processing">
                        Create Billing
                    </submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { InvoiceType } from '@/enums/invoice-type';
import { classMerge } from '@/helpers/layout';
import { Head, useForm } from '@inertiajs/vue3';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import dayjs from 'dayjs';
import { computed, useTemplateRef } from 'vue';

const props = defineProps({
    order: Object,
    types: Object,
    isInstallment: Boolean,
    amountUnbilled: Object,
});

const modalRef = useTemplateRef('modalRef');

const form = useForm({
    invoiced_at: dayjs().format('YYYY-MM-DD'),
    amount_due: '',
    due_at: '',
    type: '',
});

const isFullPayment = computed(() => {
    return form.type === InvoiceType.FINAL_PAYMENT;
});

const submitForm = () => {
    form.transform((data) => {
        if (props.isInstallment) {
            data.type = InvoiceType.INSTALLMENT;
        } else if (data.type === InvoiceType.FINAL_PAYMENT) {
            data.amount_due = null;
        }
        return data;
    }).post(route('admin.order.billing.store', props.order.id));
};
</script>
