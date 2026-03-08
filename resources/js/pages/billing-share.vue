<template>
    <div class="pdf-toolbar no-print">
        <button
            class="btn btn-primary"
            @click="pdfDownload"
            :disabled="loading"
        >
            <loading-text :loading="loading">
                <i data-feather="printer" class="feather-printer px-1"></i>
                <span class="d-none d-lg-inline">&nbsp;Download PDF</span>
            </loading-text>
        </button>
    </div>
    <div class="pdf-preview">
        <div class="pdf-page">
            <!-- HEADER -->
            <div class="d-flex mb-4">
                <div class="col-md-6">
                    <img
                        src="/img/logo/logo-pdf.png"
                        alt="Company Logo"
                        style="width: 160px"
                    />
                </div>
                <div class="col-md-6 text-end">
                    <h3 class="mt-2 fw-bold">BILLING STATEMENT</h3>
                    <h6 class="text-muted">{{ invoice.reference }}</h6>
                </div>
            </div>

            <!-- COMPANY INFO & DETAILS -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>TP Ink Lab Corp.</h5>
                    <p class="mb-0">
                        109 Roxas Ave, Poblacion District, Davao City
                    </p>
                    <p class="mb-0">8000 Davao del Sur, Philippines</p>
                    <p class="mb-0">
                        Viber / WhatsApp:
                        <a href="tel:+639923090084">+63 992 309 0084</a>
                    </p>
                    <p class="mb-0">
                        Email:
                        <a href="mailto:contact@tpinklab.com"
                            >contact@tpinklab.com</a
                        >
                        · Website:
                        <a
                            href="https://tpinklab.com"
                            target="_blank"
                            rel="noopener noreferrer"
                            >tpinklab.com</a
                        >
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="row g-1 mb-1">
                        <div class="col d-flex">
                            <strong>Bill Date</strong>
                            <span class="ms-auto">:</span>
                        </div>
                        <div class="col-5 text-muted">
                            {{
                                invoice.invoiced_at
                                    ? dayjs(invoice.invoiced_at).format(
                                          dateFormat,
                                      )
                                    : '—'
                            }}
                        </div>
                    </div>
                    <div class="row g-1 mb-1">
                        <div class="col d-flex">
                            <strong>Due Date</strong>
                            <span class="ms-auto">:</span>
                        </div>
                        <div class="col-5 text-muted">
                            {{
                                invoice.due_at
                                    ? dayjs(invoice.due_at).format(dateFormat)
                                    : '—'
                            }}
                        </div>
                    </div>
                    <div v-if="invoice.order?.reference" class="row g-1 mb-1">
                        <div class="col d-flex">
                            <strong>Sales Order No.</strong>
                            <span class="ms-auto">:</span>
                        </div>
                        <div class="col-5 text-muted">
                            {{ invoice.order.reference }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- ADDRESSES -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="text-muted mb-0">BILL TO:</p>
                    <div v-if="invoice.billing_address">
                        <strong>{{
                            invoice.billing_address.company_name
                        }}</strong>
                        <p class="mb-0">
                            {{ invoice.billing_address.full_name }}
                        </p>
                        <p v-if="invoice.billing_address.line1" class="mb-0">
                            {{ invoice.billing_address.line1 }}
                            <template v-if="invoice.billing_address.line2"
                                >, {{ invoice.billing_address.line2 }}</template
                            >
                        </p>
                        <p
                            v-if="
                                invoice.billing_address.city ||
                                invoice.billing_address.province
                            "
                            class="mb-0"
                        >
                            {{ invoice.billing_address.barangay }},
                            {{ invoice.billing_address.city
                            }}<template v-if="invoice.billing_address.province"
                                >,
                                {{ invoice.billing_address.province }}</template
                            >
                        </p>
                    </div>
                    <p v-else class="text-muted">No billing address</p>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-0">SHIP TO:</p>
                    <div v-if="invoice.shipping_address">
                        <strong>{{
                            invoice.shipping_address.company_name
                        }}</strong>
                        <p class="mb-0">
                            {{ invoice.shipping_address.full_name }}
                        </p>
                        <p v-if="invoice.shipping_address.line1" class="mb-0">
                            {{ invoice.shipping_address.line1 }}
                            <template v-if="invoice.shipping_address.line2"
                                >,
                                {{ invoice.shipping_address.line2 }}</template
                            >
                        </p>
                        <p
                            v-if="
                                invoice.shipping_address.city ||
                                invoice.shipping_address.province
                            "
                            class="mb-0"
                        >
                            {{ invoice.shipping_address.barangay }},
                            {{ invoice.shipping_address.city
                            }}<template
                                v-if="invoice.shipping_address.province"
                                >,
                                {{
                                    invoice.shipping_address.province
                                }}</template
                            >
                        </p>
                    </div>
                    <em
                        v-else-if="invoice.billing_address"
                        class="text-muted"
                        >(Same as billing address)</em
                    >
                </div>
            </div>

            <!-- LINE ITEMS TABLE -->
            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle table-sm">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="fw-bold" style="width: 50px">S/N</th>
                            <th class="fw-bold" style="width: 400px">
                                Product
                            </th>
                            <th class="fw-bold">Printing Option & Size</th>
                            <th class="fw-bold" style="width: 80px">Qty</th>
                            <th class="fw-bold" style="width: 80px">UOM</th>
                            <th class="fw-bold" style="width: 100px">Price</th>
                            <th class="fw-bold" style="width: 120px">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(line, index) in invoice.lines"
                            :key="line.id"
                        >
                            <td class="text-center">{{ index + 1 }}</td>
                            <td>
                                <small class="fw-bold">{{
                                    line.product_name
                                }}</small>
                            </td>
                            <td>
                                <div
                                    v-for="(
                                        option, oi
                                    ) in line.options_payload"
                                    :key="oi"
                                    class="small mb-1"
                                >
                                    <p class="fw-bold mb-0">
                                        {{ option.printing_option }}
                                    </p>
                                    <div
                                        v-for="item in option.items"
                                        :key="item"
                                    >
                                        <p class="mb-0">
                                            {{ item.size }}: {{ item.quantity }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{{ line.quantity }}</td>
                            <td class="text-center">
                                {{ line.uom_code || '-' }}
                            </td>
                            <td class="text-end">
                                {{ line.unit_price?.formatted || 'N/A' }}
                            </td>
                            <td class="text-end">
                                {{ line.total?.formatted || 'N/A' }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <!-- Totals -->
                        <tr>
                            <td
                                colspan="3"
                                :rowspan="rowspan"
                                class="small text-muted align-top border-0 pe-5"
                            >
                                <small class="fst-italic">
                                    This is a system generated billing
                                    statement, no signature is required.
                                </small>
                            </td>
                            <!-- <template v-if="hasShipping">
                                <td colspan="3" class="bg-light fw-bold">
                                    Shipping Fee
                                </td>
                                <td class="text-end fw-bold">
                                    {{
                                        invoice.order.shipping_total.formatted
                                    }}
                                </td>
                            </template>
                            <template v-else>
                                <td colspan="3" class="bg-light fw-bold">
                                    Total
                                </td>
                                <td class="text-end fw-bold">
                                    {{ invoice.total.formatted }}
                                </td>
                            </template> -->
                            <td colspan="3" class="bg-light fw-bold">TOTAL</td>
                            <td class="text-end fw-bold">
                                {{ invoice.total.formatted }}
                            </td>
                        </tr>
                        <!-- <tr v-if="hasShipping">
                            <td colspan="3" class="bg-light fw-bold">Total</td>
                            <td class="text-end fw-bold">
                                {{ invoice.total.formatted }}
                            </td>
                        </tr> -->
                        <tr>
                            <td colspan="4" class="border-0">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="bg-light">
                                {{ invoice.description }}
                            </td>
                            <td class="text-end">
                                {{ invoice.amount_due.formatted }}
                            </td>
                        </tr>
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
                        <tr>
                            <td colspan="3" class="bg-light fw-bold">
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
                                     {{ bill.reference }}
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

                        <!-- VAT Breakdown -->
                        <!-- <tr>
                            <td colspan="4" class="border-0">
                                <h6 class="mt-3">VAT Breakdown</h6>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="bg-light">
                                Vatable Amount
                            </td>
                            <td class="text-end">
                                {{ invoice.vatable_amount?.formatted || '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="bg-light">
                                12% VAT Amount
                            </td>
                            <td class="text-end">
                                {{ invoice.vat_amount?.formatted || '—' }}
                            </td>
                        </tr> 
                        <tr>
                            <td colspan="3" class="fw-bold bg-light">
                                Total Amount
                            </td>
                            <td class="text-end fw-bold">
                                {{ invoice.amount_due.formatted }}
                            </td>
                        </tr>-->
                    </tfoot>
                </table>
            </div>

            <!-- PAYMENT INFO -->
            <div class="small">
                <h6>HOW TO MAKE PAYMENT?</h6>
                <p>
                    TP Ink Lab uses Xendit, a secure and trusted payment
                    platform, to provide multiple convenient payment options for
                    our clients.
                </p>
                <p class="mb-0">
                    <strong>Step 1:</strong> Click the Payment Link:
                    <a
                        href="https://linkhere.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        >LINKHERE.COM</a
                    >
                </p>
                <p class="mb-0">
                    <strong>Step 2:</strong> Choose Your Preferred Payment
                    Method
                </p>
                <p class="mb-0">
                    <strong>Step 3:</strong> Follow the Instructions on the
                    Screen
                </p>
                <p><strong>Step 4:</strong> Payment Confirmation</p>
                <p>
                    Once payment is completed, you will automatically receive a
                    confirmation email or SMS from Xendit. TP Ink Lab will also
                    be notified, and we will begin processing your order once
                    payment is verified.
                </p>

                <h6>Terms & Conditions</h6>
                <ol class="list">
                    <li>
                        Start of Production: We begin working on your order once
                        your payment or down payment has been confirmed.
                    </li>
                    <li>
                        Down Payments: Down payments are non-refundable once
                        materials are prepared or production has started.
                    </li>
                    <li>
                        Remaining Balance: Any balance must be settled before
                        pickup or delivery of your finished items.
                    </li>
                    <li>
                        Late Payments: Unpaid invoices beyond the due date may
                        be subject to service charges or interest fee as
                        applicable.
                    </li>
                    <li>
                        Order Cancellation: Orders cancelled after artwork
                        approval or material preparation may be subject to
                        charges for labor and materials already used.
                    </li>
                </ol>
            </div>

            <!-- FOOTER -->
            <div class="d-flex align-items-center my-3">
                <div class="flex-grow-1 border-top"></div>
                <div class="px-2 small text-center">
                    Thank you for your business, we hope to work with you again!
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
        </div>
    </div>
</template>

<style>
.pdf-toolbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    background: #333;
    padding: 10px 20px;
    display: flex;
    gap: 10px;
    justify-content: center;
}
.pdf-preview {
    background: #aeaeae;
    min-height: 100vh;
    padding: 60px 20px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.pdf-page {
    background: #ffffff;
    max-width: 900px;
    width: 100%;
    padding: 20mm;
    font-size: 12px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}
th {
    border: 1px solid #ddd;
    padding: 8px;
}
@media print {
    .pdf-toolbar {
        display: none !important;
    }
    .pdf-preview {
        background: none !important;
        padding: 0;
    }
    .pdf-page {
        box-shadow: none;
        width: 100%;
        min-height: auto;
        padding: 15mm;
    }
}
</style>

<script setup>
import { ref, computed } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({
    invoice: Object,
     billing: Array,
    totalBilled: Number,
    unbilledAmount: Number,
});

const dateFormat = ref('DD MMM YYYY');
const loading = ref(false);

const hasShipping = computed(() => {
    return (
        props.invoice?.shipping_total &&
        props.invoice.shipping_total.value > 0
    );
});

const rowspan = computed(() => {
    let span = 19;
    if (hasShipping.value) span += 1;
    if (props.billing?.length ?? 0) span += props.billing?.length;
    return span;
});

function pdfDownload() {
    if (loading.value) return;
    loading.value = true;

    const url = route('download.billing', { billing: props.invoice.id });

    const link = document.createElement('a');
    link.href = url;
    link.click();

    setTimeout(() => (loading.value = false), 1000);
}
</script>
