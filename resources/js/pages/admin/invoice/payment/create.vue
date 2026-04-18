<template>
    <div>
        <Head title="Add Payment" />

        <Modal
            ref="modalRef"
            max-width="lg"
            :close-explicitly="true"
            v-slot="{ close }"
        >
            <div class="page-header">
                <h4 v-if="invoice.status == InvoiceStatusEnum.PAID">
                    View Payments
                </h4>
                <h4 v-else>Add Payment</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="page-body">
                    <div
                        class="form-area"
                        v-if="invoice.status != InvoiceStatusEnum.PAID"
                    >
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Amount Due</label>
                                <div class="d-flex gap-2">
                                    <input
                                        class="form-control"
                                        :value="balance.formatted"
                                        disabled
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary d-flex align-items-center gap-1 text-nowrap"
                                        @click="$refs.fileInput.click()"
                                    >
                                        <i
                                            data-feather="paperclip"
                                            class="feather-paperclip"
                                        ></i>
                                        Attach Proof
                                        <span
                                            v-if="form.data.files.length"
                                            class="badge bg-primary ms-1"
                                        >
                                            {{ form.data.files.length }}
                                        </span>
                                    </button>
                                    <input
                                        type="file"
                                        ref="fileInput"
                                        multiple
                                        class="d-none"
                                        @change="handleFileSelect"
                                    />
                                </div>
                                <div v-if="form.data.files.length" class="mt-2">
                                    <div
                                        v-for="(file, index) in form.data.files"
                                        :key="index"
                                        class="d-flex justify-content-between align-items-center border rounded px-2 py-1 mb-1"
                                    >
                                        <small class="text-truncate">{{
                                            file.name
                                        }}</small>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-link text-danger p-0 ms-2"
                                            @click="removeFile(index)"
                                        >
                                            <i
                                                data-feather="x"
                                                class="feather-x"
                                            ></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required"
                                    >Amount Paid</label
                                >
                                <input-text
                                    v-model="form.data.amount"
                                    type="number"
                                    step="0.01"
                                />
                                <input-error :message="form.errors.amount" />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required"
                                    >Date Paid</label
                                >
                                <VueDatePicker
                                    v-model="form.data.paid_at"
                                    placeholder="Select Date"
                                    :formats="{
                                        month: 'MMMM',
                                    }"
                                    :time-config="{
                                        enableTimePicker: false,
                                    }"
                                    :ui="{ input: 'form-control' }"
                                    auto-apply
                                />
                                <input-error :message="form.errors.paid_at" />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required"
                                    >Payment Method</label
                                >
                                <select
                                    v-model="form.data.method"
                                    class="form-select"
                                >
                                    <option value="" disabled>
                                        Select payment method
                                    </option>
                                    <option
                                        v-for="(label, value) in paymentMethods"
                                        :key="value"
                                        :value="value"
                                    >
                                        {{ label }}
                                    </option>
                                </select>
                                <input-error :message="form.errors.method" />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Reference</label>
                                <input-text v-model="form.data.reference" />
                                <input-error :message="form.errors.reference" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Existing Payments</label>
                        <div v-if="invoice.payments?.length" class="list-group">
                            <div
                                v-for="p in invoice.payments"
                                :key="p.id"
                                class="list-group-item"
                            >
                                <div
                                    class="d-flex justify-content-between align-items-center"
                                >
                                    <div>
                                        <div class="fw-semibold">
                                            {{
                                                p.internal_reference
                                                    ? p.internal_reference +
                                                      (p.reference
                                                          ? ' / ' + p.reference
                                                          : '')
                                                    : p.reference
                                                      ? p.reference
                                                      : `Payment #${p.id}`
                                            }}
                                        </div>
                                        <small class="text-muted"
                                            >{{
                                                p.paid_at
                                                    ? dayjs(p.paid_at).format(
                                                          'DD MMM YYYY',
                                                      )
                                                    : '—'
                                            }}
                                            ·
                                            {{
                                                paymentMethodLabels[p.method] ||
                                                '-'
                                            }}</small
                                        >
                                    </div>
                                    <div class="fw-semibold">
                                        {{
                                            p.amount?.formatted ||
                                            formatCurrency(p.amount)
                                        }}
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-xs btn-light"
                                        :disabled="isSendingReceipt"
                                        @click="confirmSendReceipt(p)"
                                    >
                                        <i
                                            data-feather="mail"
                                            class="feather-mail px-1"
                                        ></i>
                                        {{
                                            p.sent_at
                                                ? isSendingReceipt
                                                    ? 'Resending...'
                                                    : 'Resend Payment Receipt'
                                                : isSendingReceipt
                                                  ? 'Sending...'
                                                  : 'Send Payment Receipt'
                                        }}
                                    </button>

                                    <a
                                        v-if="p.media?.length"
                                        :href="
                                            route(
                                                'admin.billing.payment.media.view',
                                                {
                                                    billing: invoice.id,
                                                    payment: p.id,
                                                    media: latestMedia(p.media)
                                                        .id,
                                                },
                                            )
                                        "
                                        target="_blank"
                                        class="btn btn-xs btn-light"
                                    >
                                        <i
                                            data-feather="mail"
                                            class="feather-paperclip"
                                        ></i>
                                        View Proof
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-muted small">
                            No payments yet
                        </div>
                    </div>
                </div>

                <div class="page-footer-buttons mt-3">
                    <div>
                        <button
                            type="button"
                            class="btn btn-secondary me-2"
                            @click="close()"
                        >
                            <span
                                v-if="invoice.status == InvoiceStatusEnum.PAID"
                                >Close</span
                            >
                            <span v-else>Cancel</span>
                        </button>
                        <submit-btn
                            :loading="form.processing"
                            v-if="invoice.status != InvoiceStatusEnum.PAID"
                            >Record Payment</submit-btn
                        >
                    </div>
                </div>
            </form>
        </Modal>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import dayjs from 'dayjs';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { formatCurrency } from '@/helpers/number';
import { useTemplateRef, ref } from 'vue';
import { InvoiceStatus as InvoiceStatusEnum } from '@/enums/invoice-status';

const props = defineProps({
    invoice: Object,
    balance: Object,
    payment_methods: Object,
});

const paymentMethods = props.payment_methods;
const loading = ref(false);
const paymentMethodLabels = {
    cash: 'Cash',
    bank: 'Bank Transfer',
    check: 'Check',
    gcash: 'Gcash',
};

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    amount: '',
    paid_at: dayjs().format('YYYY-MM-DD'),
    reference: '',
    method: '',
    files: [],
});

const handleFileSelect = (event) => {
    for (const file of event.target.files) {
        form.data.files.push(file);
    }
    event.target.value = null;
};

const removeFile = (index) => {
    form.data.files.splice(index, 1);
};

const submitForm = () => {
    form.post(route('admin.billing.payment.store', props.invoice.id), {
        onSuccess: () => {
            modalRef.value.close();
            emitter.emit('payment:created');
            alert.showSuccess('Payment added successfully.');
        },
    });
};

const latestMedia = (mediaArray) => {
    if (!mediaArray || !mediaArray.length) return null;
    // Sort by created_at descending, or by id if created_at is not available
    return [...mediaArray].sort(
        (a, b) => new Date(b.created_at) - new Date(a.created_at),
    )[0];
};

const isSendingReceipt = ref(false);

const confirmSendReceipt = (payment) => {
    // Show browser confirm dialog
    const message = payment.sent_at
        ? 'Are you sure you want to resend the payment receipt email?'
        : 'Are you sure you want to send the payment receipt email?';

    if (!confirm(message)) return; // cancel if user clicks "Cancel"

    sendReceipt(payment); // call your existing function
};

const sendReceipt = (payment) => {
    if (isSendingReceipt.value) return;

    isSendingReceipt.value = true;

    router.post(
        route('admin.billing.send-receipt', {
            billing: props.invoice.id,
            payment: payment.id,
        }),
        {},
        {
            onFinish: () => {
                isSendingReceipt.value = false;
            },
        },
    );
};
</script>
