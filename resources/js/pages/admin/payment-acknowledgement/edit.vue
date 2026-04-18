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
                <h4>Edit Payment</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="page-body">
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
                                :disabled="isPosted"
                            />
                            <input-error :message="form.errors.amount" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Date Paid</label>
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
                                :disabled="isPosted"
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
                                :disabled="isPosted"
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
                            <input-text
                                v-model="form.data.reference"
                                :disabled="isPosted"
                            />
                            <input-error :message="form.errors.reference" />
                        </div>

                        <div class="col-12">
                            <label class="form-label">Attached Files</label>
                            <div
                                v-if="payment.media?.length"
                                class="list-group"
                            >
                                <div
                                    v-for="pmedia in payment.media"
                                    :key="pmedia.id"
                                    class="list-group-item"
                                >
                                    <div
                                        class="d-flex justify-content-between align-items-center"
                                    >
                                        <div>
                                            <div class="fw-semibold">
                                                <small
                                                    class="text-ellipsis"
                                                    :title="pmedia.file_name"
                                                >
                                                    {{ pmedia.file_name }}
                                                </small>
                                            </div>
                                        </div>
                                        <div
                                            class="ms-auto d-flex gap-2 mt-2 mt-sm-0"
                                        >
                                            <a
                                                :href="
                                                    route(
                                                        'admin.billing.payment.media.view',
                                                        {
                                                            billing: invoice.id,
                                                            payment: payment.id,
                                                            media: pmedia.id,
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
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-xs btn-danger-light"
                                                @click="
                                                    removesaveFile(
                                                        index,
                                                        pmedia.id,
                                                    )
                                                "
                                            >
                                                <i
                                                    data-feather="trash-2"
                                                    class="feather-trash-2"
                                                ></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-muted small">
                                No attached files
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label"
                                >Billing Amount Due:
                                {{ invoice.amount_due.formatted }}</label
                            >
                        </div>
                        <div class="col-12">
                            <label class="form-label">Existing Payments</label>
                            <div
                                v-if="invoice.payments?.length > 1"
                                class="list-group"
                            >
                                <div v-for="p in invoice.payments" :key="p.id">
                                    <div
                                        v-if="payment.id != p.id"
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
                                                                  ? ' / Ref #: ' +
                                                                    p.reference
                                                                  : '')
                                                            : p.reference
                                                              ? p.reference
                                                              : `Payment #${p.id}`
                                                    }}
                                                </div>
                                                <small class="text-muted"
                                                    >{{
                                                        p.paid_at
                                                            ? dayjs(
                                                                  p.paid_at,
                                                              ).format(
                                                                  'DD MMM YYYY',
                                                              )
                                                            : '—'
                                                    }}
                                                    ·
                                                    {{
                                                        paymentMethodLabels[
                                                            p.method
                                                        ] || '-'
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
                                        <a
                                            v-if="p.media?.length"
                                            :href="
                                                route(
                                                    'admin.billing.payment.media.view',
                                                    {
                                                        billing: invoice.id,
                                                        payment: p.id,
                                                        media: latestMedia(
                                                            p.media,
                                                        ).id,
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
                                No other payments yet
                            </div>
                        </div>
                        <small v-if="payment.posted_at" class="text-info">
                            Note: This payment has already been posted. You may
                            only upload supporting files.
                        </small>
                    </div>
                </div>

                <div
                    class="page-footer-buttons mt-4 d-flex justify-content-between align-items-center"
                >
                    <!-- LEFT SIDE: Download + Send Receipt -->
                    <div class="d-flex gap-2">
                        <button
                            type="button"
                            class="btn btn-md btn-light"
                            :disabled="isSendingReceipt"
                            @click="confirmSendReceipt"
                        >
                            <i
                                data-feather="mail"
                                class="feather-mail px-1"
                            ></i>
                            {{
                                payment.sent_at
                                    ? isSendingReceipt
                                        ? 'Resending...'
                                        : 'Resend Receipt'
                                    : isSendingReceipt
                                      ? 'Sending...'
                                      : 'Send Receipt'
                            }}
                        </button>
                        <button
                            class="btn btn-light btn-md"
                            @click="pdfDownload"
                            :disabled="loading"
                        >
                            <loading-text :loading="loading">
                                <i
                                    data-feather="download"
                                    class="feather-download px-1"
                                ></i>
                            </loading-text>
                        </button>
                    </div>

                    <!-- RIGHT SIDE: Cancel + Update Payment -->
                    <div class="d-flex gap-2">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="close()"
                        >
                            Cancel
                        </button>

                        <submit-btn :loading="form.processing">
                            Update Payment
                        </submit-btn>
                    </div>
                </div>
            </form>
        </Modal>
    </div>
</template>
<style>
.text-ellipsis {
    display: inline-block; /* or block / inline-block */
    max-width: 200px; /* set your max width */
    white-space: nowrap; /* prevent wrapping */
    overflow: hidden; /* hide overflow */
    text-overflow: ellipsis; /* show "…" */
    vertical-align: middle; /* optional alignment */
}
</style>
<script setup>
import { Head, router } from '@inertiajs/vue3';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import dayjs from 'dayjs';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { formatCurrency } from '@/helpers/number';
import { useTemplateRef, ref, computed } from 'vue';

const props = defineProps({
    invoice: Object,
    balance: Object,
    payment_methods: Object,
    payment: Object,
});
const loading = ref(false);
const isSendingReceipt = ref(false);

const paymentMethods = props.payment_methods;

const paymentMethodLabels = {
    cash: 'Cash',
    bank: 'Bank Transfer',
    check: 'Check',
    gcash: 'Gcash',
};

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    amount: props.payment.amount.decimal,
    paid_at: props.payment.paid_at ?? dayjs().format('YYYY-MM-DD'),
    reference: props.payment.reference,
    method: props.payment.method,
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
    form.post(
        route('admin.billing.payment.update', {
            billing: props.invoice.id,
            payment: props.payment.id,
        }),
        {
            onSuccess: () => {
                modalRef.value.close();
                emitter.emit('payment:created');
                alert.showSuccess('Payment updated successfully.');
            },
        },
    );
};

const paymentMedia = ref([...props.payment.media]);

const removesaveFile = (index, id = null) => {
    if (id) {
        axios
            .delete(
                route('admin.billing.payment.media.destroy', {
                    billing: props.invoice.id,
                    payment: props.payment.id,
                    mediaId: id,
                }),
            )
            .then(() => paymentMedia.value.splice(index, 1))
            .catch(() => alert.showError('Failed to delete file'));
    } else {
        form.data.files.splice(index, 1);
    }
};

function pdfDownload() {
    if (loading.value) return; // prevent multiple clicks
    loading.value = true;

    // Build URL via Ziggy
    const url = route('admin.billing.payment.download', {
        billing: props.invoice.id,
        payment: props.payment.id,
    });

    // Trigger download
    const link = document.createElement('a');
    link.href = url;
    link.click();

    // reset loading after short delay
    setTimeout(() => (loading.value = false), 1000);
}

const confirmSendReceipt = () => {
    // Show browser confirm dialog
    const message = props.payment.sent_at
        ? 'Are you sure you want to resend the payment receipt email?'
        : 'Are you sure you want to send the payment receipt email?';

    if (!confirm(message)) return; // cancel if user clicks "Cancel"

    sendReceipt(); // call your existing function
};

const sendReceipt = () => {
    if (isSendingReceipt.value) return;

    isSendingReceipt.value = true;

    router.post(
        route('admin.billing.payment.send', {
            billing: props.invoice.id,
            payment: props.payment.id,
        }),
        {},
        {
            onFinish: () => {
                isSendingReceipt.value = false;
            },
        },
    );
};

const isPosted = computed(() => !!props.payment.posted_at);

const latestMedia = (mediaArray) => {
    if (!mediaArray || !mediaArray.length) return null;
    // Sort by created_at descending, or by id if created_at is not available
    return [...mediaArray].sort(
        (a, b) => new Date(b.created_at) - new Date(a.created_at),
    )[0];
};
</script>
