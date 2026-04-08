<style scoped>
th {
    border: 1px solid #ddd;
    padding: 8px;
}
</style>

<template>
    <div class="row">
        <Header :quotation="quotation" />
        <div class="d-flex justify-content-end mb-2">
            <ModalLink
                navigate
                :href="route('admin.quotation.add', quotation.id)"
                class="btn btn-primary btn-sm"
            >
                <i
                    data-feather="plus-circle"
                    class="feather-plus-circle px-1"
                ></i>
                Add Item
            </ModalLink>
            <ModalLink
                navigate
                :href="route('admin.quotation.adjustment', quotation.id)"
                class="btn btn-secondary btn-sm mx-1"
            >
                <i
                    data-feather="plus-circle"
                    class="feather-plus-circle px-1"
                ></i
                >Adjustment
            </ModalLink>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th class="fw-bold" width="5%">S/N</th>
                        <th class="fw-bold">Product</th>
                        <th class="fw-bold">Printing Option & Size</th>
                        <th class="fw-bold" width="5%">Qty</th>
                        <th class="fw-bold" width="5%">UOM</th>
                        <th class="fw-bold" width="5%">Price</th>
                        <th class="fw-bold" width="5%">Total</th>
                        <th class="fw-bold" width="5%">Action</th>
                    </tr>
                </thead>
                <tbody v-if="lines.length == 0">
                    <tr>
                        <td colspan="8" class="text-center">
                            No items added yet. Click the "Add Item" button
                            above to get started.
                        </td>
                    </tr>
                </tbody>
                <tbody v-if="Object.keys(lines).length">
                    <template
                        v-for="(productLines, index) in lines"
                        :key="productLines[0]?.purchasable?.product_id"
                    >
                        <tr>
                            <!-- Row number -->
                            <td class="text-center">{{ index + 1 }}</td>
                            <td class="text-truncate" style="max-width: 300px">
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
                                            productLines[0]?.purchasable
                                                ?.product?.name
                                        }}
                                    </div>
                                </div>
                            </td>
                            <td class="small">
                                <div
                                    v-for="(
                                        groupLines, groupKey
                                    ) in groupByNameTypeAndSize(productLines)"
                                    :key="groupKey"
                                    class="mb-3"
                                >
                                    <div class="fw-bold text-uppercase mb-1">
                                        {{
                                            groupKey === 'withName'
                                                ? 'With Name'
                                                : 'No Name'
                                        }}
                                    </div>

                                    <div
                                        v-for="(
                                            linesBySize, size
                                        ) in groupLines"
                                        :key="size"
                                        class="mb-2"
                                    >
                                        <div
                                            class="d-flex justify-content-between align-items-center"
                                        >
                                            <!-- Size + Quantity -->
                                            <div>
                                                <span>{{ size }}</span
                                                >:
                                                {{
                                                    getTotalQuantity(
                                                        linesBySize,
                                                    )
                                                }}
                                            </div>

                                            <!-- Delete button beside size -->
                                            <dt-bulk-delete3
                                                :ids="
                                                    linesBySize.map(
                                                        (line) => line.id,
                                                    )
                                                "
                                                route-name="admin.quotation.lines.bulk-destroy"
                                                name="Size"
                                                :emitter-event="
                                                    deleteEmitterEvent
                                                "
                                                :btnClass="'btn btn-icon btn-danger-light btn-sm'"
                                                title="Delete this size"
                                            />
                                        </div>

                                        <!-- Names -->

                                        <div v-if="groupKey === 'withName'">
                                            <div
                                                v-for="line in linesBySize"
                                                :key="line.id"
                                                class="mb-1 text-muted ms-3"
                                            >
                                                <!-- Show custom dimension above names if OTHERS -->
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
                            <td class="text-end">
                                <ModalLink
                                    navigate
                                    :href="
                                        route(
                                            'admin.quotation.lines.edit.product',
                                            {
                                                quotation: quotation.id,
                                                product:
                                                    productLines[0]?.purchasable
                                                        ?.product_id,
                                            },
                                        )
                                    "
                                    class="btn btn-icon btn-outline-light btn-sm me-1"
                                    title="Edit Product Lines"
                                >
                                    <i
                                        data-feather="edit"
                                        class="feather-edit"
                                    ></i>
                                </ModalLink>
                                <!-- Delete button -->
                                <dt-bulk-delete3
                                    :ids="productLines.map((s) => s.id)"
                                    route-name="admin.quotation.lines.bulk-destroy"
                                    name="Item"
                                    :btnClass="'btn btn-icon btn-danger-light btn-sm mt-1'"
                                    :emitter-event="deleteEmitterEvent"
                                />
                            </td>
                        </tr>
                    </template>
                    <!-- Sub Total -->
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
                        <td colspan="2" class="fw-bold bg-light">Total</td>
                        <td class="text-end fw-bold">
                            {{ quotation.sub_total.formatted }}
                        </td>
                        <td class="fw-bold"></td>
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
                                <b
                                    >Shipping Breakdown:
                                    <i
                                        >(
                                        {{ quotation.shipping_total.formatted }}
                                        )</i
                                    >
                                </b>
                            </td>
                            <td class="text-end fw-bold"></td>
                            <td></td>
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
                            <td colspan="3" class="bg-light text-muted">
                                <i>{{ line.name }}</i>
                            </td>
                            <td class="text-end fw-bold">
                                {{ line.formatted }}
                            </td>
                            <td></td>
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
                                <b
                                    >Tax Breakdown:
                                    <i
                                        >(
                                        {{ quotation.tax_total.formatted }} )</i
                                    ></b
                                >
                            </td>
                            <td class="text-end fw-bold"></td>
                            <td></td>
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
                            <td colspan="3" class="bg-light text-muted">
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
                            <td></td>
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
                                <b
                                    >Discount Breakdown:
                                    <i class="text-danger"
                                        >( -{{
                                            quotation.discount_total.formatted
                                        }}
                                        )</i
                                    ></b
                                >
                            </td>
                            <td class="text-end fw-bold"></td>
                            <td></td>
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
                            <td colspan="3" class="bg-light text-muted">
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
                            <td></td>
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
                        <td class="text-end fw-bold">
                            {{ quotation.total.formatted }}
                        </td>
                        <td class="fw-bold"></td>
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
import { ref, computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
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
const quotation = ref({
    billing_address: {},
    shipping_address: {},
    currency: {},
});

watch(
    () => props.quotation,
    (val) => val && (quotation.value = val),
    { immediate: true },
);

const deleteEmitterEvent = ref('quote_lines:deleted');
const updateEmitterEvent = ref('quote_lines:updated');
const currency = computed(() => quotation.value.currency ?? {});

const shippingLines = computed(() => {
    if (!quotation.value.shipping_breakdown) return [];

    // If it's already an array, return it directly
    if (Array.isArray(quotation.value.shipping_breakdown)) {
        return quotation.value.shipping_breakdown;
    }

    // If it's an object with items
    if (typeof quotation.value.shipping_breakdown === 'object') {
        return quotation.value.shipping_breakdown.items ?? [];
    }

    // If it is a JSON string (fallback)
    try {
        return JSON.parse(quotation.value.shipping_breakdown);
    } catch (e) {
        return [];
    }
});

const taxLines = computed(() => {
    if (!quotation.value.tax_breakdown) return [];

    if (Array.isArray(quotation.value.tax_breakdown)) {
        return quotation.value.tax_breakdown;
    }

    if (typeof quotation.value.tax_breakdown === 'object') {
        return quotation.value.tax_breakdown.items ?? [];
    }

    try {
        return JSON.parse(quotation.value.tax_breakdown);
    } catch (e) {
        return [];
    }
});

const discountLines = computed(() => {
    if (!quotation.value.discount_breakdown?.amounts) return [];
    return quotation.value.discount_breakdown.amounts;
});

onMounted(() => {
    emitter.on(updateEmitterEvent.value, (updated) => {
        if (updated) {
            quotation.value = {
                billing_address: {},
                shipping_address: {},
                currency: {},
                ...updated,
            };
        }
    });
    emitter.on(deleteEmitterEvent.value, (data) => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });

        alert.showSuccess(data.message || 'Item deleted successfully.');
    });
});
</script>
