<template>
    <div class="pdf-toolbar no-print">
        <button
            href="#"
            class="btn btn-primary"
            @click="pdfDownload"
            :disabled="loading"
        >
            <loading-text :loading="loading">
                <i data-feather="printer" class="feather-printer px-1"></i>
                <span class="d-none d-lg-inline">&nbsp;⬇ Download PDF</span>
            </loading-text>
        </button>
    </div>
    <div class="pdf-preview">
        <div class="pdf-page">
            <div class="d-flex">
                <!-- COMPANY INFO -->
                <div class="col-md-6">
                    <img
                        src="/img/logo/logo-pdf.png"
                        alt="Company Logo"
                        style="width: 160px"
                        class="mb-2"
                    />
                </div>
                <div class="col-md-6 text-end">
                    <h3 class="fw-bold">QUOTATION</h3>
                </div>
            </div>
            <div class="d-flex border-bottom mb-2">
                <div class="col-md-6">
                    <Company />
                </div>
                <!-- RIGHT DETAILS -->
                <div class="col-md-6">
                    <table class="medium text-muted table-responsive">
                        <tbody>
                            <tr>
                                <td class="fw-bold text-black pr-4">
                                    Quotation No.
                                </td>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td>: {{ quotation.reference }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-black pr-4">Date</td>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td>
                                    :
                                    {{
                                        quotation?.quoted_at
                                            ? dayjs(quotation.quoted_at).format(
                                                  dateFormat,
                                              )
                                            : 'Not Set'
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-black pr-4">
                                    Expected Delivery Date
                                </td>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td>
                                    :
                                    {{
                                        quotation?.expected_delivery
                                            ? dayjs(
                                                  quotation.expected_delivery,
                                              ).format(dateFormat)
                                            : 'Not Set'
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-black pr-4">
                                    Validity
                                </td>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td>
                                    :
                                    {{
                                        quotation?.validity_days
                                            ? quotation.validity_days + ' Days'
                                            : 'Not Set'
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex mb-2">
                <!-- BILL TO -->
                <div class="col-md-12 mb-4 mt-4">
                    <span class="text-muted">BILL TO:</span>
                    <div class="small text-muted mt-1">
                        <h6>{{ address.company_name }}</h6>
                        {{ address.title }} {{ address.first_name }}
                        {{ address.last_name }}<br />
                        {{ address.line1 }}, {{ address.line2 }}<br />
                        {{ address.barangay }}, {{ address.city }}<br />
                        {{ address.province }}, {{ address.postcode }},
                        {{ address.country.name }}<br />
                        Email: {{ address.email }} / Mobile:+{{ address.phone }}
                    </div>
                </div>
            </div>
            <!-- ITEM TABLE -->
            <div class="table-responsive">
                <table
                    class="table table-bordered align-middle table-responsive p-2"
                >
                    <thead class="table-light text-center">
                        <tr>
                            <th
                                class="fw-bold"
                                width="80"
                                style="font-size: 12px !important"
                            >
                                S/N
                            </th>
                            <th
                                class="fw-bold"
                                style="font-size: 12px !important"
                            >
                                Product
                            </th>
                            <th
                                class="fw-bold"
                                style="font-size: 12px !important"
                            >
                                Printing Option & Size
                            </th>
                            <th
                                class="fw-bold"
                                width="80"
                                style="font-size: 12px !important"
                            >
                                Qty
                            </th>
                            <th
                                class="fw-bold"
                                width="80"
                                style="font-size: 12px !important"
                            >
                                UOM
                            </th>
                            <th
                                class="fw-bold"
                                width="100"
                                style="font-size: 12px !important"
                            >
                                Price
                            </th>
                            <th
                                class="fw-bold"
                                width="100"
                                style="font-size: 12px !important"
                            >
                                TOTAL
                            </th>
                        </tr>
                    </thead>
                    <tbody v-if="lines.length == 0">
                        <tr>
                            <td
                                colspan="7"
                                class="text-center"
                                style="font-size: 12px !important"
                            >
                                No Item Found
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-if="Object.keys(lines).length">
                        <template
                            v-for="(productLines, index) in lines"
                            :key="index"
                        >
                            <tr>
                                <!-- Row number -->
                                <td
                                    class="text-center"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    {{ index + 1 }}
                                </td>
                                <td
                                    style="
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    <div>
                                        <img
                                            :src="
                                                productLines[0].purchasable
                                                    .product.image
                                                    .preview_url ||
                                                productLines[0].purchasable
                                                    .product.image
                                                    .original_url ||
                                                ''
                                            "
                                            class="rounded border"
                                            style="
                                                width: 70px;
                                                height: 70px;
                                                object-fit: contain;
                                                border: 0;
                                            "
                                        />
                                        <div class="small fw-bold mt-1">
                                            {{
                                                productLines[0].purchasable
                                                    .product.name
                                            }}
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="small"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    <!-- Group by With Name / No Name -->
                                    <div
                                        v-for="(
                                            groupLines, groupKey
                                        ) in groupByNameTypeAndSize(
                                            productLines,
                                        )"
                                        :key="groupKey"
                                        class="mb-3"
                                    >
                                        <div
                                            class="fw-bold text-uppercase mb-1"
                                        >
                                            {{
                                                groupKey === 'withName'
                                                    ? 'With Name'
                                                    : 'No Name'
                                            }}
                                        </div>

                                        <!-- Group by size -->
                                        <div
                                            v-for="(
                                                linesBySize, size
                                            ) in groupLines"
                                            :key="size"
                                            class="mb-2"
                                        >
                                            <div>
                                                {{ size }}:
                                                {{
                                                    getTotalQuantity(
                                                        linesBySize,
                                                    )
                                                }}
                                            </div>

                                            <!-- Expand each line into multiple rows based on quantity -->
                                            <div
                                                v-if="groupKey === 'withName'"
                                                class="text-muted ms-3"
                                            >
                                                <div
                                                    v-for="line in linesBySize"
                                                    :key="line.id"
                                                >
                                                    <div
                                                        v-for="item in line.namesWithNumbers"
                                                        :key="item.number"
                                                    >
                                                        {{ item.number }}.)
                                                        <span
                                                            v-html="
                                                                item.name ??
                                                                '<i class=\'text-danger\'>Please set name</i>'
                                                            "
                                                        ></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Quantity -->
                                <td
                                    class="text-center"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    {{
                                        productLines.reduce(
                                            (sum, l) => sum + l.quantity,
                                            0,
                                        )
                                    }}
                                </td>
                                <!-- UOM -->
                                <td
                                    class="text-center"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    <div
                                        v-for="(lines, uom) in groupByUOM(
                                            productLines,
                                        )"
                                        :key="uom"
                                        class="mb-2"
                                    >
                                        <div class="fw-bold">{{ uom }}</div>
                                    </div>
                                </td>
                                <!-- Price-->
                                <td
                                    class="text-end"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    <template
                                        v-if="
                                            typeof getPriceDisplay(
                                                productLines,
                                                currency,
                                            ) === 'string'
                                        "
                                    >
                                        {{
                                            getPriceDisplay(
                                                productLines,
                                                currency,
                                            )
                                        }}
                                    </template>
                                    <template v-else>
                                        <div
                                            v-for="priceLine in getPriceDisplay(
                                                productLines,
                                                currency,
                                            )"
                                            :key="priceLine"
                                        >
                                            {{ priceLine }}
                                        </div>
                                    </template>
                                </td>
                                <!--Total Price-->
                                <td
                                    class="text-end"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    {{ getTotalPrice(productLines, currency) }}
                                </td>
                            </tr>
                        </template>
                        <tr>
                            <td
                                colspan="3"
                                class="fst-italic small mt-1 text-muted"
                                style="
                                    border-left: 1px solid white !important;
                                    border-bottom: 1px solid white !important;
                                    font-size: 14px !important;
                                "
                            >
                                <small
                                    >This is a system generated Quotation, no
                                    signature is required.
                                </small>
                            </td>
                            <td
                                colspan="1"
                                class="fw-bold bg-light text-center"
                            >
                                {{
                                    Object.values(lines).reduce(
                                        (total, productLines) =>
                                            total +
                                            productLines.reduce(
                                                (sum, l) => sum + l.quantity,
                                                0,
                                            ),
                                        0,
                                    )
                                }}
                            </td>
                            <td
                                colspan="2"
                                class="fw-bold bg-light"
                                style="
                                    font-size: 12px !important;
                                    border-bottom: 1px solid #ddd !important;
                                "
                            >
                                Total
                            </td>
                            <td
                                class="text-end fw-bold"
                                style="
                                    font-size: 12px !important;
                                    border-bottom: 1px solid #ddd !important;
                                "
                            >
                                {{ quotation.sub_total.formatted }}
                            </td>
                        </tr>

                        <!-- Shipping Breakdown -->
                        <template v-if="shippingLines.length">
                            <tr v-if="shippingLines.length > 1">
                                <td
                                    colspan="3"
                                    class="fw-bold"
                                    style="
                                        border-left: 1px solid white !important;
                                        border-bottom: 1px solid white !important;
                                    "
                                ></td>
                                <td
                                    colspan="3"
                                    class="bg-light"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    <b>Shipping Breakdown:</b>
                                    <i
                                        >(
                                        {{ quotation.shipping_total.formatted }}
                                        )</i
                                    >
                                </td>
                                <td
                                    class="text-end fw-bold"
                                    style="
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                ></td>
                            </tr>
                            <tr
                                v-for="(line, index) in shippingLines"
                                :key="'ship-' + index"
                            >
                                <td
                                    colspan="3"
                                    class="fw-bold"
                                    style="
                                        border-left: 1px solid white !important;
                                        border-bottom: 1px solid white !important;
                                    "
                                ></td>
                                <td
                                    colspan="3"
                                    class="bg-light"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    &nbsp;&nbsp;<i>{{ line.name }}</i>
                                </td>
                                <td
                                    class="text-end fw-bold"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    {{ line.formatted }}
                                </td>
                            </tr>
                        </template>
                        <!-- Tax Breakdown -->
                        <template v-if="taxLines.length">
                            <tr v-if="taxLines.length > 1">
                                <td
                                    colspan="3"
                                    class="fw-bold"
                                    style="
                                        border-left: 1px solid white !important;
                                        border-bottom: 1px solid white !important;
                                    "
                                ></td>
                                <td
                                    colspan="3"
                                    class="bg-light"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    <b>Tax Breakdown:</b>
                                    <i
                                        >(
                                        {{ quotation.tax_total.formatted }} )</i
                                    >
                                </td>
                                <td
                                    class="text-end fw-bold"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                ></td>
                            </tr>
                            <tr
                                v-for="(tax, index) in taxLines"
                                :key="'ship-' + index"
                            >
                                <td
                                    colspan="3"
                                    class="fw-bold"
                                    style="
                                        border-left: 1px solid white !important;
                                        border-bottom: 1px solid white !important;
                                    "
                                ></td>
                                <td
                                    colspan="3"
                                    class="bg-light"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    &nbsp;&nbsp;<i
                                        >{{ tax.identifier
                                        }}<span
                                            class="px-1"
                                            v-if="tax.percentage"
                                            >({{ tax.percentage }} %)</span
                                        ></i
                                    >
                                </td>
                                <td
                                    class="text-end fw-bold"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    {{ tax.description }}
                                </td>
                            </tr>
                        </template>
                        <!-- Discount Breakdown -->
                        <template v-if="discountLines.length">
                            <tr v-if="discountLines.length > 1">
                                <td
                                    colspan="3"
                                    class="fw-bold"
                                    style="
                                        border-left: 1px solid white !important;
                                        border-bottom: 1px solid white !important;
                                    "
                                ></td>
                                <td
                                    colspan="3"
                                    class="bg-light"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    <b>Discount Breakdown:</b>
                                    <i class="text-danger"
                                        >( -{{
                                            quotation.discount_total.formatted
                                        }}
                                        )</i
                                    >
                                </td>
                                <td
                                    class="text-end fw-bold"
                                    style="
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                ></td>
                            </tr>
                            <tr
                                v-for="(discount, index) in discountLines"
                                :key="'ship-' + index"
                            >
                                <td
                                    colspan="3"
                                    class="fw-bold"
                                    style="
                                        border-left: 1px solid white !important;
                                        border-bottom: 1px solid white !important;
                                    "
                                ></td>
                                <td
                                    colspan="3"
                                    class="bg-light"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    &nbsp;&nbsp;<i
                                        >{{ discount.description
                                        }}<span
                                            class="px-1"
                                            v-if="
                                                discount.type === 'percentage'
                                            "
                                            >({{ discount.percentage }} %)</span
                                        ></i
                                    >
                                </td>
                                <td
                                    class="text-end fw-bold text-danger"
                                    style="
                                        font-size: 12px !important;
                                        border-bottom: 1px solid #ddd !important;
                                    "
                                >
                                    - {{ discount.price?.formatted }}
                                </td>
                            </tr>
                        </template>
                        <!-- Grand Total -->
                        <tr>
                            <td
                                colspan="3"
                                class="fw-bold"
                                style="
                                    border-left: 1px solid white !important;
                                    border-bottom: 1px solid white !important;
                                "
                            ></td>
                            <td
                                colspan="3"
                                class="fw-bold bg-light"
                                style="
                                    font-size: 12px !important;
                                    border-bottom: 1px solid #ddd !important;
                                "
                            >
                                Grand Total
                            </td>
                            <td
                                class="text-end fw-bold"
                                style="
                                    font-size: 12px !important;
                                    border-bottom: 1px solid #ddd !important;
                                "
                            >
                                {{ quotation.total.formatted }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- TERMS -->
            <Terms
                :validity="quotation?.validity_days"
                :style="'font-size:12px !important'"
            />
        </div>
    </div>
</template>
<style scoped>
.pdf-toolbar {
    position: sticky;
    top: 0;
    z-index: 50;
    background: #ffffff;
    padding: 10px 20px;
    border-bottom: 1px solid #aeaeae;
    display: flex;
    justify-content: flex-end;
}
.pdf-preview {
    background: #aeaeae; /* gray background */
    min-height: 100vh;
    padding: 10px 0;
    display: flex;
    justify-content: center;
}
.pdf-page {
    background: #ffffff;
    padding: 20mm;
    font-size: 12px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}
th {
    border: 1px solid #ddd;
    padding: 8px;
}
@media print {
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
import Terms from './admin/quotation/template/terms.vue';
import {
    groupByNameTypeAndSize,
    groupByUOM,
    getTotalQuantity,
    getTotalPrice,
    getPriceDisplay,
} from '@/helpers/quote';
import Company from './admin/company-info.vue';

const props = defineProps({
    quotation: Object,
    lines: Object,
});

const address = props?.quotation?.billing_address;
const currency = props?.quotation?.currency;
const dateFormat = ref('MMM. DD, YYYY');
const loading = ref(false);
const shippingLines = computed(() => {
    if (!props.quotation.shipping_breakdown) return [];

    // If it's already an array, return it directly
    if (Array.isArray(props.quotation.shipping_breakdown)) {
        return props.quotation.shipping_breakdown;
    }

    // If it's an object with items
    if (typeof props.quotation.shipping_breakdown === 'object') {
        return props.quotation.shipping_breakdown.items ?? [];
    }

    // If it is a JSON string (fallback)
    try {
        return JSON.parse(props.quotation.shipping_breakdown);
    } catch (e) {
        return [];
    }
});

const taxLines = computed(() => {
    if (!props.quotation.tax_breakdown) return [];

    if (Array.isArray(props.quotation.tax_breakdown)) {
        return props.quotation.tax_breakdown;
    }

    if (typeof props.quotation.tax_breakdown === 'object') {
        return props.quotation.tax_breakdown.items ?? [];
    }

    try {
        return JSON.parse(props.quotation.tax_breakdown);
    } catch (e) {
        return [];
    }
});

const discountLines = computed(() => {
    if (!props?.quotation.discount_breakdown?.amounts) return [];

    return props.quotation.discount_breakdown.amounts;
});

function pdfDownload() {
    if (loading.value) return; // prevent multiple clicks
    loading.value = true;

    // Build URL via Ziggy
    const url = route('download.quotation', { quotation: props.quotation.id });

    // Trigger download
    const link = document.createElement('a');
    link.href = url;
    link.click();

    // reset loading after short delay
    setTimeout(() => (loading.value = false), 1000);
}
</script>
