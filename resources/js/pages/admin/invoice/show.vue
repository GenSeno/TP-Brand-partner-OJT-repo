<style scoped>
th {
    border: 1px solid #ddd;
    padding: 8px;
}
</style>

<style>
@media print {
    /* Hide layout chrome */
    .header,
    .sidebar,
    .sidebar-overlay,
    .horizontal-sidebar {
        display: none !important;
    }

    .main-wrapper,
    .page-wrapper,
    .page-wrapper > .content {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Hide non-print elements within the page */
    .page-header,
    .no-print,
    .attachment-component,
    .notes-history {
        display: none !important;
    }

    /* Hide sidebar column and actions bar */
    .col-lg-3 {
        display: none !important;
    }

    .col-lg-9 {
        width: 100% !important;
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }

    /* Hide actions bar (first card) and notes */
    .col-lg-9 > .card:first-child {
        display: none !important;
    }

    /* Print preview card: remove card styling */
    .print-preview {
        box-shadow: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    body {
        background: #fff !important;
    }
}
</style>

<template>
    <Head :title="`Billing Statement - ${invoice.reference}`" />

    <div class="row">
        <div class="content">
            <div class="d-flex page-header">
                <!-- ORDER INFO -->
                <div class="col-md-6 page-title">
                    <h4>Billing Statement No. {{ invoice.reference }}</h4>
                    <h6>Manage Your Billing Statement</h6>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="text-end text-uppercase">
                        Status:
                        <InvoiceStatus
                            :status="invoice.status"
                            :is-badge="false"
                        />
                    </h6>
                </div>
            </div>
            <div class="row">
                <!-- LEFT LIST -->
                <div class="col-lg-3">
                    <Sidebar :invoice="invoice" ref="sidebar" />
                </div>

                <!-- MAIN CONTENT -->
                <div class="col-lg-9 col-md-12 col-sm-12">
                    <!-- TOP ACTIONS BAR -->
                    <div class="card mb-3 no-print">
                        <div
                            class="card-body d-flex justify-content-between align-items-center"
                        >
                            <div class="d-flex align-items-center">
                                <div
                                    v-if="
                                        invoice.order.billing_summary
                                            .amount_unbilled.value > 0
                                    "
                                    class="border-end pe-1 me-1"
                                >
                                    <ModalLink
                                        navigate
                                        :href="
                                            route(
                                                'admin.order.billing.create',
                                                invoice.order_id,
                                            )
                                        "
                                        class="btn btn-sm btn-light-ghost"
                                    >
                                        <i
                                            data-feather="file-text"
                                            class="feather-file-text px-1"
                                        ></i>
                                        New Billing
                                    </ModalLink>
                                </div>
                                <div
                                    class="border-end pe-1 me-1"
                                >
                                    <ModalLink
                                        navigate
                                        :href="
                                            route(
                                                'admin.billing.payment.create',
                                                invoice.id,
                                            )
                                        "
                                        class="btn btn-sm btn-light-ghost"
                                    >
                                        <i
                                            data-feather="credit-card"
                                            class="feather-credit-card px-1"
                                        ></i>
                                        Payment
                                    </ModalLink>
                                </div>
                                <div
                                    class="border-end pe-1 me-1"
                                    >
                                    <ModalLink
                                        v-if="!hasShipping"
                                        navigate
                                        :href="
                                            route('admin.shipping.edit', invoice.id)
                                        "
                                        class="btn btn-sm btn-light-ghost"
                                    >
                                        <i
                                            data-feather="plus-circle"
                                            class="feather-plus-circle px-1"
                                        ></i>
                                        Add on Fee
                                    </ModalLink>
                                    <ModalLink
                                        v-else
                                        navigate
                                        :href="
                                            route('admin.shipping.edit', invoice.id)
                                        "
                                        class="btn btn-sm btn-light-ghost"
                                    >
                                        <i
                                            data-feather="plus-circle"
                                            class="feather-plus-circle px-1"
                                        ></i>
                                        Edit Add on Fee
                                    </ModalLink>
                                </div>
                                <div class="border-end pe-1 me-1">
                                    <a
                                        href="#"
                                        class="btn btn-sm btn-light-ghost"
                                        @click.prevent="shareBilling"
                                        :disabled="sharing"
                                    >
                                        <loading-text :loading="sharing">
                                            <i
                                                data-feather="share-2"
                                                class="feather-share-2"
                                            ></i>
                                            <span class="d-none d-lg-inline ms-2">Share</span>
                                        </loading-text>
                                    </a>
                                </div>
                                <button
                                    type="button"
                                    @click="handlePrint"
                                    class="btn btn-sm btn-light-ghost"
                                >
                                    <i
                                        data-feather="printer"
                                        class="feather-printer me-1"
                                    ></i>
                                    PDF/Print
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <Link
                                    :href="route('admin.billing.index')"
                                    class="btn btn-sm btn-secondary d-flex align-items-center"
                                >
                                    <vue-feather
                                        type="arrow-left"
                                        class="me-2"
                                    ></vue-feather>
                                    Back to List
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- PREVIEW AREA -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="print-preview">
                                <!-- HEADER -->
                                <div class="print-header row g-2 mb-4">
                                    <!-- COMPANY INFO -->
                                    <div class="col-sm">
                                        <img
                                            src="/img/logo/logo-pdf.png"
                                            alt="Company Logo"
                                            class="company-logo"
                                        />
                                    </div>
                                    <div class="col-sm-auto text-end">
                                        <h3 class="mt-2 fw-bold">
                                            BILLING STATEMENT
                                        </h3>
                                        <h6 class="text-muted">
                                            {{ invoice.reference }}
                                        </h6>
                                    </div>

                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <address class="address-block">
                                                    <h5>TP Ink Lab Corp.</h5>
                                                    <p>
                                                        109 Roxas Ave, Poblacion
                                                        District, Davao City
                                                    </p>
                                                    <p>
                                                        8000 Davao del Sur,
                                                        Philippines
                                                    </p>
                                                    <p>
                                                        Viber / WhatsApp:
                                                        <a
                                                            href="tel:+639923090084"
                                                            >+63 992 309 0084</a
                                                        >
                                                    </p>
                                                    <p>
                                                        <span
                                                            >Email:
                                                            <a
                                                                href="mailto:contact@tpinklab.com"
                                                                >contact@tpinklab.com</a
                                                            ></span
                                                        >
                                                        <span class="separator"
                                                            >·</span
                                                        >
                                                        <span
                                                            >Website:
                                                            <a
                                                                href="https://tpinklab.com"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                >tpinklab.com</a
                                                            ></span
                                                        >
                                                    </p>
                                                </address>
                                            </div>

                                            <!-- RIGHT DETAILS -->
                                            <div class="col-md-6">
                                                <div
                                                    class="row align-items-center g-1 mb-1"
                                                >
                                                    <div class="col d-flex">
                                                        <strong
                                                            class="text-black"
                                                            >Bill Date</strong
                                                        >
                                                        <span class="ms-auto"
                                                            >:</span
                                                        >
                                                    </div>
                                                    <div
                                                        class="col-5 text-muted"
                                                    >
                                                        {{
                                                            invoice.invoiced_at
                                                                ? dayjs(
                                                                      invoice.invoiced_at,
                                                                  ).format(
                                                                      dateFormat,
                                                                  )
                                                                : '—'
                                                        }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="row align-items-center g-1 mb-1"
                                                >
                                                    <div class="col d-flex">
                                                        <strong
                                                            class="text-black"
                                                            >Due Date</strong
                                                        >
                                                        <span class="ms-auto"
                                                            >:</span
                                                        >
                                                    </div>
                                                    <div
                                                        class="col-5 text-muted"
                                                    >
                                                        {{
                                                            invoice.due_at
                                                                ? dayjs(
                                                                      invoice.due_at,
                                                                  ).format(
                                                                      dateFormat,
                                                                  )
                                                                : '—'
                                                        }}
                                                    </div>
                                                </div>
                                                <div
                                                    v-if="
                                                        invoice.order?.reference
                                                    "
                                                    class="row align-items-center g-1 mb-1"
                                                >
                                                    <div class="col d-flex">
                                                        <strong
                                                            class="text-black"
                                                            >Sales Order
                                                            No.</strong
                                                        >
                                                        <span class="ms-auto"
                                                            >:</span
                                                        >
                                                    </div>
                                                    <div
                                                        class="col-5 text-muted"
                                                    >
                                                        {{
                                                            invoice.order
                                                                .reference
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ADDRESSES -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="text-muted mb-0">BILL TO:</p>
                                        <AddressBlock
                                            v-if="invoice.billing_address"
                                            :address="invoice.billing_address"
                                        />
                                        <p v-else class="text-muted">
                                            No billing address
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted mb-0">SHIP TO:</p>
                                        <AddressBlock
                                            v-if="invoice.shipping_address"
                                            :address="invoice.shipping_address"
                                        />
                                        <em
                                            v-else-if="invoice.billing_address"
                                            class="text-muted"
                                            >(Same as billing address)</em
                                        >
                                    </div>
                                </div>

                                <!-- LINE ITEMS TABLE -->
                                <PrintTable :lines="invoice.lines">
                                    <template #footer>
                                        <!-- TOTALS -->
                                        <tr>
                                            <td
                                                colspan="3"
                                                :rowspan="rowspan"
                                                class="small text-muted align-top border-0 pe-5"
                                                style="
                                                    word-break: break-word;
                                                    white-space: normal;
                                                "
                                            >
                                                <small
                                                    class="fst-italic mt-1 text-muted mb-5"
                                                >
                                                    This is a system generated
                                                    billing statement, no
                                                    signature is required.
                                                </small>
                                            </td>
                                            <!-- <template v-if="hasShipping">
                                                <td
                                                    colspan="3"
                                                    class="bg-light fw-bold"
                                                >
                                                    Shipping Fee
                                                </td>
                                                <td class="text-end fw-bold">
                                                    {{
                                                        invoice.order
                                                            .shipping_total
                                                            .formatted
                                                    }}
                                                </td>
                                            </template> -->
                                            <!-- <template>
                                                <td
                                                    colspan="3"
                                                    class="bg-light fw-bold"
                                                >
                                                    TOTAL
                                                </td>
                                                <td class="text-end fw-bold">
                                                    {{
                                                        invoice.total.formatted
                                                    }}
                                                </td>
                                            </template> -->
                                            <td
                                                colspan="3"
                                                class="bg-light fw-bold"
                                                >
                                                TOTAL
                                            </td>
                                            <td class="text-end fw-bold">
                                                {{ invoice.total.formatted }}
                                            </td>
                                        </tr>

                                       

                                        <!-- Amount Due -->
                                        <tr>
                                            <td colspan="4" class="border-0">
                                                &nbsp;
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="bg-light">
                                                {{ invoice.description }}
                                            </td>
                                            <td class="text-end">
                                                {{
                                                    invoice.amount_due.formatted
                                                }}
                                            </td>
                                        </tr>
                                        <!-- Addon Fees -->
                                        <tr
                                            v-for="fee in invoice.shipping_breakdown"
                                            :key="fee.name"
                                        >
                                            <td colspan="3" class="bg-light">
                                                Add on: {{ fee.name }}
                                            </td>
                                            <td class="text-end">
                                                {{ fee.formatted }}
                                            </td>
                                        </tr>
                                        <!-- Total Amount Due -->
                                        <tr>
                                            <td
                                                colspan="3"
                                                class="bg-light fw-bold"
                                            >
                                                Total Amount Due
                                            </td>
                                            <td class="text-end fw-bold">
                                                  {{ invoice.summary.with_shipping.formatted }}
                                            </td>
                                        </tr>
                                        <tr  v-if="billing?.length == 0">
                                            <td colspan="4" class="border-0">
                                                &nbsp;
                                            </td>
                                        </tr>
                                        <template v-if="billing?.length > 0">
                                            <tr>
                                                <td
                                                    colspan="4"
                                                    class="border-0"
                                                >
                                                    <h6 class="mt-3">
                                                        Billing Breakdown
                                                    </h6>
                                                </td>
                                            </tr>
                                            <tr
                                                v-for="bill in billing"
                                                :key="bill.id"
                                            >
                                                <td
                                                    colspan="3"
                                                    class="bg-light"
                                                >
                                                    Billing No.:
                                                    <Link
                                                        :href="
                                                            route(
                                                                'admin.billing.show',
                                                                bill.id,
                                                            )
                                                        "
                                                    >
                                                        {{ bill.reference }}
                                                    </Link>
                                                </td>
                                                <td class="text-end">
                                                    {{
                                                        bill.summary.amount_paid
                                                            .formatted
                                                    }}
                                                </td>
                                            </tr>
                                            <tr  v-if="invoice.type != 'down-payment'">
                                                <td
                                                    colspan="3"
                                                    class="fw-bold bg-light"
                                                >
                                                    Total Amount Billed
                                                </td>
                                                <td class="text-end fw-bold">
                                                    {{
                                                        totalBilled
                                                            .formatted
                                                    }}
                                                </td>
                                            </tr>
                                            <tr v-if="invoice.type != 'down-payment'">
                                                <td
                                                    colspan="3"
                                                    class="bg-light text-danger"
                                                >
                                                    Unbilled Amount
                                                </td>
                                                <td class="text-end text-danger">
                                                    {{
                                                        unbilledAmount
                                                            .formatted
                                                    }}
                                                </td>
                                            </tr>
                                            <br/>
                                        </template>
                                    </template>
                                </PrintTable>
                                
                                <div class="small">
                                    <h6>HOW TO MAKE PAYMENT?</h6>
                                    <p>
                                        TP Ink Lab uses Xendit, a secure and
                                        trusted payment platform, to provide
                                        multiple convenient payment options for
                                        our clients.
                                    </p>
                                    <p class="mb-0">
                                        <strong>Step 1:</strong>
                                        Click the Payment Link:
                                        <a
                                            href="https://linkhere.com"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            >LINKHERE.COM</a
                                        >
                                    </p>
                                    <p class="mb-0">
                                        <strong>Step 2:</strong>
                                        Choose Your Preferred Payment Method
                                    </p>
                                    <p class="mb-0">
                                        <strong>Step 3:</strong>
                                        Follow the Instructions on the Screen
                                    </p>
                                    <p>
                                        <strong>Step 4:</strong>
                                        Payment Confirmation
                                    </p>

                                    <p>
                                        Once payment is completed, you will
                                        automatically receive a confirmation
                                        email or SMS from Xendit. TP Ink Lab
                                        will also be notified, and we will begin
                                        processing your order once payment is
                                        verified.
                                    </p>

                                    <h6>Terms & Conditions</h6>
                                    <ol class="list">
                                        <li>
                                            Start of Production: We begin
                                            working on your order once your
                                            payment or down payment has been
                                            confirmed.
                                        </li>
                                        <li>
                                            Down Payments: Down payments are
                                            non-refundable once materials are
                                            prepared or production has started.
                                        </li>
                                        <li>
                                            Remaining Balance: Any balance must
                                            be settled before pickup or delivery
                                            of your finished items.
                                        </li>
                                        <li>
                                            Late Payments: Unpaid invoices
                                            beyond the due date may be subject
                                            to service charges or interest fee
                                            as applicable.
                                        </li>
                                        <li>
                                            Order Cancellation: Orders cancelled
                                            after artwork approval or material
                                            preparation may be subject to
                                            charges for labor and materials
                                            already used.
                                        </li>
                                    </ol>
                                </div>

                                <div class="d-flex align-items-center my-3">
                                    <div class="flex-grow-1 border-top"></div>
                                    <div class="px-2 small text-center">
                                        Thank you for your business, we hope to
                                        work with you again!
                                    </div>
                                    <div class="flex-grow-1 border-top"></div>
                                </div>

                                <div
                                    class="d-flex justify-content-end align-items-center gap-2"
                                >
                                    <div class="small text-end">Powered by</div>
                                    <img
                                        src="/img/tech-hive-logo-black-font.png"
                                        alt="Techhive"
                                        style="width: 100px"
                                        class="mb-2"
                                    />
                                </div>

                                <div>
                                    <MediaUpload
                                        :model-id="invoice.id"
                                        post-route="admin.billing.media.upload"
                                        delete-route="admin.billing.media.destroy"
                                        :existing-files="invoice.media"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="no-print">
                        <NotesHistory
                            :post-url="route('admin.billing.note', invoice.id)"
                            :activities="activities"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import axios from 'axios';
import * as alert from '@/helpers/alert';
import AddressBlock from '@/components/address-block.vue';
import InvoiceStatus from '@/components/invoice/invoice-status.vue';
import DashboardLayout from '@/layouts/dashboard-layout.vue';
import Sidebar from './sidebar.vue';
import PrintTable from '@/components/order/print-table.vue';

import MediaUpload from '@/components/media-upload.vue';
import NotesHistory from '@/components/notes-history.vue';
import { emitter } from '@/composables/eventBus';

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    invoice: Object,
    billing: Array,
    activities: Array,
    totalBilled: Object,
    unbilledAmount: Object,
});
const dateFormat = ref('DD MMM YYYY');
console.log('invoice', props.invoice );

const hasShipping = computed(() => {
    return (
        props.invoice?.shipping_total &&
        props.invoice.shipping_total.value > 0
    );
});

const addonFees = computed(() => {
    return props.invoice?.shipping_breakdown || [];
});

const rowspan = computed(() => {
    let span = 5; // base: shipping/total row, spacer, description, addon fees header area, total amount due
    if (hasShipping.value) span += 1;
    span += addonFees.value.length;
    if (props.billing.length > 0) span += props.billing.length + 4;
    return span;
});

const sharing = ref(false);

const shareBilling = () => {
    sharing.value = true;
    axios
        .post(route('admin.billing.share', props.invoice.id))
        .then((res) => {
            navigator.clipboard.writeText(res.data.url);
            sharing.value = false;
            alert.showSuccess('Share link copied!');
        });
};

const handlePrint = () => {
    window.print();
};



onMounted(() => {
    emitter.on('note:created', () => {
        router.reload({
            only: ['activities'],
        });
    });

    emitter.on('shipping:updated', () => {
        router.reload();
    });
});

</script>
