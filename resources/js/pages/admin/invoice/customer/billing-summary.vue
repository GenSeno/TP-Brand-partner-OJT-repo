<template>
    <Head :title="`Billing Summary - ${customer.full_name}`" />

    <Modal ref="modalRef" max-width="5xl" v-slot="{ close }">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Billing Summary</h4>
                    <h6>{{ customer.full_name }}</h6>
                </div>
            </div>
        </div>

        <div class="page-body bg-light">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Customer Details Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                Customer Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small"
                                            >Name</label
                                        >
                                        <p class="mb-0 fw-semibold">
                                            {{ customer.full_name }}
                                        </p>
                                    </div>
                                    <div
                                        class="mb-3"
                                        v-if="customer.company_name"
                                    >
                                        <label class="text-muted small"
                                            >Company</label
                                        >
                                        <p class="mb-0">
                                            {{ customer.company_name }}
                                        </p>
                                    </div>
                                    <div class="mb-3" v-if="customer.vat_no">
                                        <label class="text-muted small"
                                            >VAT No.</label
                                        >
                                        <p class="mb-0">
                                            {{ customer.vat_no }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6" v-if="billingAddress">
                                    <div class="mb-3">
                                        <label class="text-muted small"
                                            >Contact Information</label
                                        >
                                        <p
                                            class="mb-0"
                                            v-if="billingAddress.email"
                                        >
                                            <i class="ti ti-mail me-1"></i>
                                            {{ billingAddress.email }}
                                        </p>
                                        <p
                                            class="mb-0"
                                            v-if="billingAddress.phone"
                                        >
                                            <i class="ti ti-phone me-1"></i>
                                            {{ billingAddress.phone }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted small"
                                            >Billing Address</label
                                        >
                                        <p class="mb-0 small">
                                            {{
                                                [
                                                    billingAddress.address_line_1,
                                                    billingAddress.address_line_2,
                                                    billingAddress.city,
                                                    billingAddress.province,
                                                    billingAddress.postcode,
                                                    billingAddress.country
                                                        ?.name,
                                                ]
                                                    .filter(Boolean)
                                                    .join(', ')
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Statements -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Billing Statements</h5>
                        </div>
                        <div class="card-body p-0">
                            <div
                                v-if="customer.invoices.length === 0"
                                class="text-center py-5 text-muted"
                            >
                                <i
                                    class="ti ti-file-invoice fs-1 d-block mb-2"
                                ></i>
                                <p>No billing statements found</p>
                            </div>
                            <div v-else>
                                <div
                                    v-for="invoice in customer.invoices"
                                    :key="invoice.id"
                                    class="invoice-item border-bottom"
                                >
                                    <div
                                        class="p-3 d-flex justify-content-between align-items-start"
                                    >
                                        <div class="flex-grow-1">
                                            <div
                                                class="d-flex align-items-center gap-2 mb-2"
                                            >
                                                <Link
                                                    :href="
                                                        route(
                                                            'admin.billing.show',
                                                            invoice.id,
                                                        )
                                                    "
                                                    class="text-primary fw-semibold"
                                                >
                                                    {{ invoice.reference }}
                                                </Link>
                                                <InvoiceStatus
                                                    :status="invoice.status"
                                                />
                                            </div>
                                            <div
                                                class="d-flex gap-3 text-muted small mb-2"
                                            >
                                                <span
                                                    ><i
                                                        class="ti ti-calendar me-1"
                                                    ></i
                                                    >{{
                                                        invoice.invoiced_at
                                                            ? dayjs(
                                                                  invoice.invoiced_at,
                                                              ).format(
                                                                  'MMMM D, YYYY',
                                                              )
                                                            : '—'
                                                    }}</span
                                                >
                                                <span
                                                    ><i
                                                        class="ti ti-clock me-1"
                                                    ></i
                                                    >Due:
                                                    {{
                                                        invoice.due_at
                                                            ? dayjs(
                                                                  invoice.due_at,
                                                              ).format(
                                                                  'MMMM D, YYYY',
                                                              )
                                                            : '—'
                                                    }}</span
                                                >
                                                <span v-if="invoice.order"
                                                    ><i
                                                        class="ti ti-file-text me-1"
                                                    ></i
                                                    >{{
                                                        invoice.order.reference
                                                    }}</span
                                                >
                                            </div>
                                            <div class="d-flex gap-4 small">
                                                <div>
                                                    <span class="text-muted"
                                                        >Amount Due:</span
                                                    >
                                                    <span
                                                        class="fw-semibold ms-1"
                                                        >{{
                                                            invoice.amount_due
                                                                .formatted
                                                        }}</span
                                                    >
                                                </div>
                                                <div>
                                                    <span class="text-muted"
                                                        >Paid:</span
                                                    >
                                                    <span
                                                        class="fw-semibold ms-1 text-success"
                                                        >{{
                                                            formatCurrency(
                                                                calculatePaid(
                                                                    invoice,
                                                                ).value,
                                                            )
                                                        }}</span
                                                    >
                                                </div>
                                                <div>
                                                    <span class="text-muted"
                                                        >Balance:</span
                                                    >
                                                    <span
                                                        class="fw-semibold ms-1"
                                                        :class="
                                                            invoice.amount_due
                                                                .value -
                                                                calculatePaid(
                                                                    invoice,
                                                                ).value >
                                                            0
                                                                ? 'text-danger'
                                                                : 'text-muted'
                                                        "
                                                        >{{
                                                            formatCurrency(
                                                                invoice
                                                                    .amount_due
                                                                    .value -
                                                                    calculatePaid(
                                                                        invoice,
                                                                    ).value,
                                                            )
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payments for this invoice -->
                                    <div
                                        v-if="invoice.payments?.length > 0"
                                        class="bg-light p-3"
                                    >
                                        <h6 class="small fw-semibold mb-2">
                                            <i
                                                class="ti ti-credit-card me-1"
                                            ></i
                                            >Payments
                                        </h6>
                                        <div class="table-responsive">
                                            <table
                                                class="table table-sm table-borderless mb-0"
                                            >
                                                <thead>
                                                    <tr
                                                        class="text-muted small"
                                                    >
                                                        <th>Reference</th>
                                                        <th>Date</th>
                                                        <th>Method</th>
                                                        <th class="text-end">
                                                            Amount
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                        v-for="payment in invoice.payments"
                                                        :key="payment.id"
                                                        class="small"
                                                    >
                                                        <td>
                                                            {{
                                                                payment.reference
                                                            }}
                                                        </td>
                                                        <td>
                                                            {{
                                                                payment.paid_at
                                                                    ? dayjs(
                                                                          payment.paid_at,
                                                                      ).format(
                                                                          'MMMM D, YYYY',
                                                                      )
                                                                    : '—'
                                                            }}
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge badge-soft-info"
                                                                >{{
                                                                    payment.method
                                                                }}</span
                                                            >
                                                        </td>
                                                        <td
                                                            class="text-end fw-semibold"
                                                        >
                                                            {{
                                                                payment.amount
                                                                    .formatted
                                                            }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div
                                        v-else
                                        class="bg-light p-3 text-muted small"
                                    >
                                        <i class="ti ti-info-circle me-1"></i>
                                        No payments recorded
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats Sidebar -->
                <div class="col-lg-4">
                    <div class="card sticky-top" style="top: 0">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2"
                                >
                                    <span class="text-muted">Total Billed</span>
                                    <span class="fw-bold">{{
                                        formatCurrency(stats.totalBilled)
                                    }}</span>
                                </div>
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2"
                                >
                                    <span class="text-muted">Total Paid</span>
                                    <span class="fw-bold text-success">{{
                                        formatCurrency(stats.totalPaid)
                                    }}</span>
                                </div>
                                <div class="border-top pt-2 mt-2">
                                    <div
                                        class="d-flex justify-content-between align-items-center"
                                    >
                                        <span class="fw-semibold"
                                            >Outstanding Balance</span
                                        >
                                        <span
                                            class="fs-4 fw-bold"
                                            :class="{
                                                'text-danger':
                                                    stats.totalBalance.value >
                                                    0,
                                                'text-success':
                                                    stats.totalBalance.value ===
                                                    0,
                                            }"
                                            >{{
                                                formatCurrency(
                                                    stats.totalBalance,
                                                )
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="border-top pt-3">
                                <h6 class="small fw-semibold mb-3">
                                    Status Breakdown
                                </h6>
                                <div
                                    v-for="status in statusBreakdown"
                                    :key="status.key"
                                    class="d-flex justify-content-between align-items-center mb-2 small"
                                >
                                    <div
                                        class="d-flex align-items-center gap-2"
                                    >
                                        <InvoiceStatus :status="status.key" />
                                    </div>
                                    <span class="fw-semibold">{{
                                        status.count
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-footer">
            <button type="button" class="btn btn-secondary" @click="close()">
                Close
            </button>
        </div>
    </Modal>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, useTemplateRef } from 'vue';
import { formatCurrency } from '@/helpers/number';
import InvoiceStatus from '@/components/invoice/invoice-status.vue';
import dayjs from 'dayjs';

const props = defineProps({
    customer: Object,
});

const modalRef = useTemplateRef(null);

const billingAddress = computed(() => {
    return props.customer.addresses?.find(
        (addr) => addr.type === 'billing' && addr.default,
    );
});

const stats = computed(() => {
    const totalBilled = props.customer.invoices.reduce(
        (sum, invoice) => sum + (invoice.amount_due?.value || 0),
        0,
    );
    const totalPaid = props.customer.invoices.reduce(
        (sum, invoice) =>
            sum +
            (invoice.payments?.reduce(
                (pSum, payment) => pSum + (payment.amount?.value || 0),
                0,
            ) || 0),
        0,
    );

    const totalBalance = totalBilled - totalPaid;

    return {
        totalBilled: { value: totalBilled },
        totalPaid: { value: totalPaid },
        totalBalance: { value: totalBalance },
    };
});

const statusBreakdown = computed(() => {
    const breakdown = {};
    props.customer.invoices.forEach((invoice) => {
        const status = invoice.status;
        breakdown[status] = (breakdown[status] || 0) + 1;
    });

    return Object.entries(breakdown).map(([key, count]) => ({
        key,
        count,
    }));
});

const calculatePaid = (invoice) => {
    const paid =
        (invoice.payments?.reduce(
            (sum, payment) => sum + (payment.amount?.value || 0),
            0,
        ) || 0) +
        (invoice.unpaid_payments?.reduce(
            (sum, payment) => sum + (payment.amount?.value || 0),
            0,
        ) || 0);
    return { value: paid };
};
</script>

<style scoped>
.invoice-item:last-child {
    border-bottom: none !important;
}

@media print {
    .page-header,
    .sticky-top {
        position: relative !important;
    }
}
</style>
