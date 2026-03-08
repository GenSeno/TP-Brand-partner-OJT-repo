<style scoped>
th {
    border: 1px solid #ddd;
    padding: 8px;
}
</style>

<template>
    <div class="row">
        <Header :quotation="quotation" />
        <!-- ITEM TABLE -->
        <div class="table-responsive" id="print_area">
            <table class="table table-bordered align-middle table-responsive">
                <thead class="table-light text-center">
                    <tr>
                        <th class="fw-bold" width="5%">S/N</th>
                        <th class="fw-bold">Product</th>
                        <th class="fw-bold">Printing Option & Size</th>
                        <th class="fw-bold" width="5%">Qty</th>
                        <th class="fw-bold" width="5%">UOM</th>
                        <th class="fw-bold" width="5%">Price</th>
                        <th class="fw-bold" width="5%">Total</th>
                    </tr>
                </thead>
                <tbody v-if="Object.keys(lines).length === 0">
                    <tr>
                        <td colspan="7" class="text-center">
                            No Item Selected, Please click the
                            <button class="btn btn-light" @click="goToItems">
                                <i data-feather="edit" class="feather-edit"></i
                                >Edit
                            </button>
                            button.
                        </td>
                    </tr>
                </tbody>
                <tbody v-if="Object.keys(lines).length">
                    <template
                        v-for="(productLines, index) in lines"
                        :key="productLines[0]?.purchasable?.product?.id"
                    >
                        <tr>
                            <!-- Row number -->
                            <td class="text-center">{{ index + 1 }}</td>
                            <td>
                                <div>
                                    <img
                                        :src="
                                           productLines[0]?.purchasable
                                            ?.product?.image
                                            ?.original_url || ''
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
                                            productLines[0]?.purchasable?.product?.name
                                        }}
                                    </div>
                                </div>
                            </td>
                            <td class="small">
                                <!-- Group by With Name / No Name -->
                                <div
                                    v-for="(
                                        groupLines, groupKey
                                    ) in groupByNameTypeAndSize(productLines)"
                                    :key="groupKey"
                                    class="mb-3"
                                >
                                    <div class="fw-bold mb-1">
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
                                            {{ getTotalQuantity(linesBySize) }}
                                        </div>

                                        <!-- Expand each line into multiple rows based on quantity -->
                                        <div v-if="groupKey === 'withName'">
                                            <div
                                                v-for="line in linesBySize"
                                                :key="line.id"
                                            >
                                                <div
                                                    v-for="item in line.namesWithNumbers"
                                                    :key="item.number" class="ms-2 text-muted"
                                                >
                                                    {{ item.number }}.)
                                                    <span class="mb-0"
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
                            <td class="text-center">
                                {{
                                    productLines.reduce(
                                        (sum, l) => sum + l.quantity,
                                        0,
                                    )
                                }}
                            </td>
                            <!-- UOM -->
                            <td class="text-center">
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
                            <td class="text-end">
                                <template
                                    v-if="
                                        typeof getPriceDisplay(
                                            productLines,
                                            currency,
                                        ) === 'string'
                                    "
                                >
                                    {{
                                        getPriceDisplay(productLines, currency)
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
                            <td class="text-end">
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
                            "
                        >
                            This is a system generated Quotation, no signature
                            is required.
                        </td>
                        <td colspan="1" class="fw-bold bg-light text-center">
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
                        <td colspan="2" class="fw-bold bg-light">
                            Total
                        </td>
                        <td class="text-end fw-bold">
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
                            <td colspan="3" class="bg-light">
                                <b>Shipping Breakdown:</b>
                                <i>
                                    ({{ quotation.shipping_total.formatted }})
                                </i>
                            </td>
                            <td class="text-end fw-bold"></td>
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
                            <td colspan="3" class="bg-light">
                                <i class="text-muted">{{ line.name }}</i>
                            </td>
                            <td class="text-end fw-bold">
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
                            <td colspan="3" class="bg-light">
                                <b>Tax Breakdown:</b>
                                <i> ({{ quotation.tax_total.formatted }} )</i>
                            </td>
                            <td class="text-end fw-bold"></td>
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
                            <td colspan="3" class="bg-light  text-muted">
                                <i
                                    >{{ tax.identifier
                                    }}<span class="px-1" v-if="tax.percentage"
                                        >({{ tax.percentage }} %)</span
                                    ></i
                                >
                            </td>
                            <td class="text-end fw-bold">
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
                            <td colspan="3" class="bg-light">
                                <b>Discount Breakdown:</b>
                                <i class="text-danger">
                                    ( -{{
                                        quotation.discount_total.formatted
                                    }}
                                    )</i
                                >
                            </td>
                            <td class="text-end fw-bold"></td>
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
                            <td colspan="3" class="bg-light  text-muted">
                                <i
                                    >{{ discount.description
                                    }}<span
                                        class="px-1"
                                        v-if="discount.type === 'percentage'"
                                        >({{ discount.percentage }} %)</span
                                    ></i
                                >
                            </td>
                            <td class="text-end fw-bold text-danger">
                                - {{ discount.price?.formatted }}
                            </td>
                        </tr>
                    </template>
                    <!-- Total Quantity -->
                    <tr>
                        <td
                            colspan="3"
                            class="fw-bold"
                            style="
                                border-left: 1px solid white !important;
                                border-bottom: 1px solid white !important;
                            "
                        ></td>
                        <td colspan="3" class="fw-bold bg-light">
                           Grand Total
                        </td>
                        <td class="text-end fw-bold">{{ quotation.total.formatted }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Terms :validity="quotation?.validity_days" />

        <div>
            <MediaUpload
                :model-id="quotation.id"
                post-route="admin.quotation.media.upload"
                delete-route="admin.quotation.media.destroy"
                :existing-files="quotation.media"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import Terms from './terms.vue';
import Header from './header.vue';

import {
    groupByNameTypeAndSize,
    groupByUOM,
    getTotalQuantity,
    getTotalPrice,
    getPriceDisplay,
} from '@/helpers/quote';
import MediaUpload from '@/components/media-upload.vue';

const props = defineProps({
    quotation: Object,
    lines: Object,
});

const quotation = reactive({ ...props.quotation });
const lines = reactive({...props.lines}); 
const address = quotation?.billing_address;
const currency = quotation?.currency;
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
    if (!quotation.discount_breakdown?.amounts) return [];

    return quotation.discount_breakdown.amounts;
});

function goToItems() {
    router.get(route('admin.quotation.item', { quotation: quotation?.id }));
}
</script>
