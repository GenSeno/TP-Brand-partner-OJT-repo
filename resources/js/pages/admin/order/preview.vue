<style scoped>
th {
    border: 1px solid #ddd;
    padding: 8px;
}
</style>

<template>
    <div class="print-preview">
        <!-- HEADER -->
        <div class="print-header row g-2 mb-2">
            <!-- COMPANY INFO -->
            <div class="col-sm">
                <img
                    src="/img/logo/logo-pdf.png"
                    alt="Company Logo"
                    class="company-logo"
                />
            </div>
            <div class="col-sm-auto text-end">
                <h3 class="mt-2 fw-bold">SALES ORDER</h3>
                <h6 class="text-muted">{{ order.reference }}</h6>
            </div>

            <div class="col-12">
                <div class="row">
                    <div class="col-md-6">
                        <address class="address-block">
                            <h5>TP Ink Lab Corp.</h5>
                            <p>109 Roxas Ave, Poblacion District, Davao City</p>
                            <p>8000 Davao del Sur, Philippines</p>
                            <p>
                                Viber / WhatsApp:
                                <a href="tel:+639923090084">+63 992 309 0084</a>
                            </p>
                            <p>
                                <span
                                    >Email:
                                    <a href="mailto:contact@tpinklab.com"
                                        >contact@tpinklab.com</a
                                    ></span
                                >
                                <span class="separator">·</span>
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
                        <div class="row align-items-center g-1 mb-1">
                            <div class="col d-flex">
                                <strong class="text-black">Date</strong>
                                <span class="ms-auto">:</span>
                            </div>
                            <div class="col-5 text-muted">
                                <span v-show="!isEdit" class="text-muted">{{
                                    order.placed_at
                                        ? dayjs(order.placed_at).format(
                                              dateFormat,
                                          )
                                        : dayjs().format(dateFormat)
                                }}</span>
                                <VueDatePicker
                                    v-show="isEdit"
                                    v-model="form.placed_at"
                                    placeholder="Select Date"
                                    :formats="{
                                        month: 'MMMM',
                                    }"
                                    :time-config="{
                                        enableTimePicker: false,
                                    }"
                                    :ui="{
                                        input: classMerge([
                                            'form-control form-control-sm',
                                            {
                                                'is-invalid':
                                                    form.errors['placed_at'],
                                            },
                                        ]),
                                    }"
                                    @update:model-value="
                                        form.clearErrors('placed_at')
                                    "
                                    auto-apply
                                />
                            </div>
                        </div>
                        <div class="row align-items-center g-1 mb-0">
                            <div class="col d-flex">
                                <strong class="text-black"
                                    >Expected Delivery Date</strong
                                >
                                <span class="ms-auto">:</span>
                            </div>
                            <div class="col-5">
                                <span v-show="!isEdit" class="text-muted">{{
                                    order.expected_delivery
                                        ? dayjs(order.expected_delivery).format(
                                              dateFormat,
                                          )
                                        : 'N/A'
                                }}</span>
                                <VueDatePicker
                                    v-show="isEdit"
                                    v-model="form.expected_delivery"
                                    placeholder="Select Date"
                                    :formats="{
                                        month: 'MMMM',
                                    }"
                                    :time-config="{
                                        enableTimePicker: false,
                                    }"
                                    :ui="{
                                        input: classMerge([
                                            'form-control form-control-sm',
                                            {
                                                'is-invalid':
                                                    form.errors[
                                                        'expected_delivery'
                                                    ],
                                            },
                                        ]),
                                    }"
                                    @update:model-value="
                                        form.clearErrors('expected_delivery')
                                    "
                                    auto-apply
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-3 mb-3" :class="{ 'mt-4': !isEdit }">
            <div v-show="isEdit" class="col-12">
                <div class="d-flex">
                    <ModalLink
                        navigate
                        :href="route('admin.order.address.edit', order.id)"
                        class="btn btn-sm btn-light ms-auto"
                    >
                        <i
                            data-feather="settings"
                            class="feather-settings me-1"
                        ></i>
                        Edit Info
                    </ModalLink>
                </div>
            </div>

            <!-- BILL TO -->
            <div class="col-md-6">
                <p class="text-muted mb-0">BILL TO:</p>
                <AddressBlock
                    v-if="billingAddress"
                    :address="billingAddress"
                    class="mb-0"
                />
                <em v-else class="text-muted">(No billing address)</em>
            </div>

            <!-- SHIP TO -->
            <div class="col-md-6">
                <p class="text-muted mb-0">SHIP TO:</p>
                <AddressBlock
                    v-if="shippingAddress"
                    :address="shippingAddress"
                    class="mb-0"
                />
                <em v-else-if="billingAddress" class="text-muted"
                    >(Same as billing address)</em
                >
            </div>
        </div>

        <!-- ITEM TABLE -->
        <div v-show="isEdit">
            <div class="d-flex justify-content-end gap-2 mb-2">
                <ModalLink
                    navigate
                    :href="route('admin.order.item.create', order.id)"
                    class="btn btn-sm btn-primary"
                    #default="{ loading }"
                >
                    <loading-text :loading="loading">
                        <i
                            data-feather="plus-circle"
                            class="feather-plus-circle me-1"
                        ></i>
                        Add Item
                    </loading-text>
                </ModalLink>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-sm">
                <thead class="table-light text-center">
                    <tr>
                        <th class="fw-bold" style="width: 50px">S/N</th>
                        <th class="fw-bold" style="width: 400px">Product</th>
                        <th class="fw-bold">Printing Option & Size</th>
                        <th class="fw-bold" style="width: 80px">Qty</th>
                        <th class="fw-bold" style="width: 80px">UOM</th>
                        <th class="fw-bold" style="width: 100px">Price</th>
                        <th class="fw-bold" style="width: 120px">TOTAL</th>
                        <th v-if="isEdit" class="fw-bold" style="width: 80px">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody
                    v-if="!order.print_lines || order.print_lines.length == 0"
                >
                    <tr>
                        <td colspan="7" class="text-center">
                            No items in this order yet.
                            <button
                                class="btn btn-light ms-2"
                                @click="isEdit = !isEdit"
                            >
                                <i data-feather="edit" class="feather-edit"></i>
                                Add Items
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr
                        v-for="(line, index) in order.print_lines"
                        :key="line.id"
                    >
                        <td class="text-center">{{ index + 1 }}</td>
                        <td>
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <a
                                        href="javascript:void(0);"
                                        class="avatar avatar-xl bg-light-900 p-1"
                                        @click="
                                            previewImage(line.product.image)
                                        "
                                    >
                                        <img
                                            class="object-fit-contain"
                                            :src="
                                                getImagePreview(
                                                    line.product.image,
                                                )
                                            "
                                            alt="img"
                                        />
                                    </a>
                                </div>
                                <div class="col">
                                    <small class="fw-bold">
                                        {{ line.product_name }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div
                                v-for="(option, oIdx) in line.options_payload"
                                :key="oIdx"
                                class="small mb-3"
                            >
                                <p class="fw-bold mb-0">
                                    {{ option.printing_option }}
                                </p>
                                <div
                                    v-for="item in option.items"
                                    :key="item.order_line_id"
                                    class="mb-2"
                                >
                                    <div
                                        class="d-flex justify-content-between align-items-center"
                                    >
                                        <span>
                                            <!-- Check if this is a custom size -->
                                            <template
                                                v-if="isCustomSize(item.size)"
                                            >
                                                Custom: {{ item.size }}:
                                                {{ item.quantity }}
                                            </template>
                                            <template v-else>
                                                {{ item.size }}:
                                                {{ item.quantity }}
                                            </template>
                                        </span>
                                        <!-- Per-variation Edit / Delete -->
                                        <div
                                            v-if="isEdit && item.order_line_id"
                                            class="d-flex gap-1"
                                        >
                                            <ModalLink
                                                navigate
                                                :href="
                                                    route(
                                                        'admin.order.item.edit',
                                                        {
                                                            order: order.id,
                                                            item: item.order_line_id,
                                                        },
                                                    )
                                                "
                                                class="btn btn-icon btn-outline-light btn-sm"
                                                title="Edit Variation"
                                            >
                                                <vue-feather
                                                    type="edit"
                                                    size="14"
                                                ></vue-feather>
                                            </ModalLink>
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-danger-light btn-sm"
                                                title="Delete Variation"
                                                @click="
                                                    openDeleteModal(
                                                        item.order_line_id,
                                                        `${line.product_name} (${item.size})`,
                                                    )
                                                "
                                            >
                                                <vue-feather
                                                    type="trash-2"
                                                    size="14"
                                                ></vue-feather>
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="!!item.names" class="ms-2">
                                        <p
                                            v-for="(name, nIdx) in item.names"
                                            :key="nIdx"
                                            class="mb-0"
                                        >
                                            {{ nIdx + 1 }}) {{ name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">{{ line.quantity }}</td>
                        <td class="text-center">
                            {{ handleUomCode(line.uom_code, line.quantity) }}
                        </td>
                        <td class="text-end">
                            <template
                                v-if="getOrderPriceDisplay(line).length <= 1"
                            >
                                {{ getOrderPriceDisplay(line)[0] || 'N/A' }}
                            </template>
                            <template v-else>
                                <div
                                    v-for="priceLine in getOrderPriceDisplay(
                                        line,
                                    )"
                                    :key="priceLine"
                                >
                                    {{ priceLine }}
                                </div>
                            </template>
                        </td>
                        <td class="text-end">
                            {{ line.total?.formatted || 'N/A' }}
                        </td>
                        <!-- Action column (per-item / product-level) -->
                        <td v-if="isEdit" class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <ModalLink
                                    navigate
                                    :href="
                                        route('admin.order.item.edit-product', {
                                            order: order.id,
                                            product: line.product_id,
                                        })
                                    "
                                    class="btn btn-icon btn-outline-light btn-sm"
                                    title="Edit Item"
                                >
                                    <vue-feather
                                        type="edit"
                                        size="14"
                                    ></vue-feather>
                                </ModalLink>
                                <button
                                    type="button"
                                    class="btn btn-icon btn-danger-light btn-sm"
                                    title="Delete Item"
                                    @click="
                                        openDeleteModal(
                                            null,
                                            line.product_name,
                                            line.product_id,
                                        )
                                    "
                                >
                                    <vue-feather
                                        type="trash-2"
                                        size="14"
                                    ></vue-feather>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <!-- TOTALS -->
                    <tr>
                        <td colspan="3" rowspan="11" class="align-top border-0">
                            <small class="fst-italic mt-1 text-muted">
                                This is a system generated Sales Order, no
                                signature is required.
                            </small>
                        </td>
                        <td class="bg-light text-center fw-bold">
                            {{ totalQty }}
                        </td>
                        <td colspan="2" class="bg-light">Total</td>
                        <td class="text-end">
                            {{ order.sub_total?.formatted || 'N/A' }}
                        </td>
                    </tr>

                    <template v-if="hasBillings">
                        <tr>
                            <td colspan="4" class="border-0">
                                <h6 class="mt-3">Payment Breakdown</h6>
                            </td>
                        </tr>
                        <tr v-for="invoice in order.invoices" :key="invoice.id">
                            <td colspan="3" class="bg-light">
                                {{ invoice.type_label }}
                                (#{{ invoice.id }}):
                                {{ invoice.reference }}
                            </td>
                            <td class="text-end">
                                {{ invoice.amount_due?.formatted }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="fw-bold bg-light">
                                Total Amount Paid
                            </td>
                            <td class="text-end fw-bold">
                                {{
                                    order.billing_summary?.amount_paid
                                        ?.formatted
                                }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4" class="border-0">&nbsp;</td>
                        </tr>

                        <tr>
                            <td colspan="3" class="bg-light">Balance Amount</td>
                            <td class="text-end">
                                {{
                                    order.billing_summary?.amount_balance
                                        ?.formatted
                                }}
                            </td>
                        </tr>
                    </template>
                </tfoot>
            </table>
        </div>

        <!-- NOTES -->
        <div class="mt-4 small" v-if="order.notes">
            <h6 class="fw-bold mb-2">Notes</h6>
            <p v-show="!isEdit">{{ form.notes }}</p>
            <textarea
                v-show="isEdit"
                v-model="form.notes"
                class="form-control"
            ></textarea>
        </div>

        <!-- TERMS -->
        <div class="mt-4 small">
            <h6 class="fw-bold mb-2">Terms & Conditions</h6>
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
                    Remaining Balance: Any balance must be settled before pickup
                    or delivery of your finished items.
                </li>
                <li>
                    Late Payments: Unpaid invoices beyond the due date may be
                    subject to service charges or interest fee as applicable.
                </li>
                <li>
                    Order Cancellation: Orders cancelled after artwork approval
                    or material preparation may be subject to charges for labor
                    and materials already used.
                </li>
            </ol>
        </div>

        <div class="d-flex align-items-center my-3">
            <div class="flex-grow-1 border-top"></div>
            <div class="px-2 small text-center">
                Thank you for your business, we hope to work with you again!
            </div>
            <div class="flex-grow-1 border-top"></div>
        </div>

        <div class="d-flex justify-content-end align-items-center gap-2">
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
                :model-id="order.id"
                post-route="admin.order.media.upload"
                delete-route="admin.order.media.destroy"
                :existing-files="order.media"
            />
        </div>

        <!-- Delete Item Modal -->
        <div
            class="modal fade"
            ref="deleteModalRef"
            tabindex="-1"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="p-4 text-center">
                        <span
                            class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"
                        >
                            <i class="ti ti-trash fs-24 text-danger"></i>
                        </span>
                        <h4 class="fs-20 fw-bold mb-2 mt-1">Delete Item</h4>
                        <p class="mb-3 fs-14">
                            Are you sure you want to delete
                            <strong>{{ deleteItemLabel }}</strong
                            >?
                        </p>
                        <div class="text-start mb-3">
                            <label class="form-label required"
                                >Reason for deletion</label
                            >
                            <textarea
                                v-model="deleteReason"
                                class="form-control"
                                rows="2"
                                placeholder="Enter reason for deleting this item..."
                            ></textarea>
                        </div>
                        <div class="d-flex justify-content-center gap-2">
                            <button
                                type="button"
                                class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                :disabled="
                                    !deleteReason.trim() || deleteLoading
                                "
                                @click="confirmDelete"
                            >
                                <i
                                    v-if="deleteLoading"
                                    class="spinner-border spinner-border-sm me-1"
                                ></i>
                                Yes, Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <vue-easy-lightbox
        :visible="lightbox.visible"
        :index="lightbox.index"
        :imgs="lightbox.gallery"
        @hide="lightbox.visible = false"
    >
    </vue-easy-lightbox>
</template>

<script setup>
import { capitalize, computed, reactive, ref, onMounted, nextTick } from 'vue';
import dayjs from 'dayjs';
import AddressBlock from '@/components/address-block.vue';
import { pluralize } from '@/helpers/string';
import VueEasyLightbox from 'vue-easy-lightbox';
import { getImagePreview, getImageUrl } from '@/helpers/media';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import { classMerge } from '@/helpers/layout';
import MediaUpload from '@/components/media-upload.vue';
import axios from 'axios';
import * as alert from '@/helpers/alert';
import { emitter } from '@/composables/eventBus';
import { router } from '@inertiajs/vue3';

const isEdit = defineModel('isEdit', { type: Boolean, default: false });
const form = defineModel('form', { type: Object, default: () => ({}) });

const props = defineProps({
    order: Object,
});

const billingAddress = computed(() => props.order?.billing_address);
const shippingAddress = computed(() => props.order?.shipping_address);
const hasBillings = computed(
    () => props.order?.billing_summary?.amount_billed?.value > 0,
);
const totalQty = computed(
    () =>
        props.order?.print_lines?.reduce(
            (sum, line) => sum + (line.quantity || 0),
            0,
        ) ?? 0,
);
const dateFormat = 'MMMM DD, YYYY';
const lightbox = reactive({
    visible: false,
    index: 0,
    gallery: [],
});
const previewImage = (image) => {
    if (image) {
        lightbox.gallery = getImageUrl(image);
        lightbox.visible = true;
    }
};

const handleUomCode = (uomCode, count = null) => {
    if (!uomCode) return '-';
    return capitalize(pluralize(uomCode, count));
};

const getOrderPriceDisplay = (line) => {
    const items = (line.options_payload || []).flatMap((g) => g.items || []);

    // Deduplicate by (size, hasNames) composite key
    const seen = new Set();
    const uniqueItems = [];
    for (const item of items) {
        if (!item.unit_price_formatted) continue;
        const hasNames = !!(item.names && item.names.length > 0);
        const key = `${item.size}|${hasNames}`;
        if (!seen.has(key)) {
            seen.add(key);
            uniqueItems.push({
                size: item.size,
                hasNames,
                formatted: item.unit_price_formatted,
                price: item.unit_price,
            });
        }
    }

    // If all entries share the same price, show a single price
    const uniquePrices = new Set(uniqueItems.map((i) => i.price));
    if (uniquePrices.size <= 1) {
        return [line.unit_price?.formatted || 'N/A'];
    }

    // Determine which sizes have both named and unnamed variants
    const sizeCounts = {};
    for (const item of uniqueItems) {
        sizeCounts[item.size] = (sizeCounts[item.size] || 0) + 1;
    }

    return uniqueItems.map((item) => {
        if (sizeCounts[item.size] > 1) {
            const nameLabel = item.hasNames ? 'w/ Name' : 'No Name';
            return `${item.size} (${nameLabel}): ${item.formatted}`;
        }
        return `${item.size}: ${item.formatted}`;
    });
};

const isCustomSize = (size) => {
    // You can adjust this condition based on how custom sizes are structured
    // For example, if they contain 'x' like '10x12'
    return /\d+\s*x\s*\d+/i.test(size);
};

// Delete item flow
const deleteModalRef = ref(null);
const deleteLineId = ref(null);
const deleteProductId = ref(null);
const deleteItemLabel = ref('');
const deleteReason = ref('');
const deleteLoading = ref(false);
let deleteModal = null;

const openDeleteModal = (orderLineId, label, productId = null) => {
    deleteLineId.value = orderLineId;
    deleteProductId.value = productId;
    deleteItemLabel.value = label;
    deleteReason.value = '';
    deleteLoading.value = false;
    nextTick(() => {
        if (!deleteModal) {
            deleteModal = new bootstrap.Modal(deleteModalRef.value);
        }
        deleteModal.show();
    });
};

const confirmDelete = async () => {
    if (!deleteReason.value.trim()) return;
    deleteLoading.value = true;

    try {
        let data;

        if (deleteProductId.value) {
            // Product-level delete (all variations)
            const res = await axios.delete(
                route('admin.order.item.bulk-destroy', {
                    order: props.order.id,
                }),
                {
                    data: {
                        product_id: deleteProductId.value,
                        reason: deleteReason.value,
                    },
                },
            );
            data = res.data;
        } else {
            // Single variation delete
            const res = await axios.delete(
                route('admin.order.item.destroy', {
                    order: props.order.id,
                    item: deleteLineId.value,
                }),
                {
                    data: {
                        reason: deleteReason.value,
                    },
                },
            );
            data = res.data;
        }

        deleteModal.hide();
        alert.showSuccess(data.message || 'Item deleted successfully.');
        emitter.emit('order:item-deleted');
        router.reload({ preserveScroll: true });
    } catch (error) {
        alert.showError(
            error.response?.data?.message || 'Failed to delete item.',
        );
    } finally {
        deleteLoading.value = false;
    }
};
</script>
